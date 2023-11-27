<?php
/**
 * @file 微信登录处理service
 *
 * 需要配置 user 日志项
 */
namespace Ake\Tools\Services;

use EasyWeChat\Factory as EasyWechat;
use Ake\Tools\Base\Service;
use App\Models\User as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Wechat extends Service
{
    public $mini;

    public $pc;

    public $mb;

    /**
     * @var string 微信标识key
     */
    private $wechat_key = 'openid';

    private $state = 'STATE';

    private $scope = 'snsapi_login';

    private $grant_type = 'authorization_code';

    public $modelClass = 'App\Models\User';

    public function mini()
    {
        $this->mini = EasyWeChat::miniProgram(config('wechat.mini'));
    }

    public function pc()
    {
        $this->pc = EasyWechat::openPlatform(config('wechat.pc'));
    }

    public function mb()
    {
        $this->mb = EasyWechat::officialAccount(config('wechat.mb'));
    }

    #pc 二维码页面
    public function pcUrl()
    {
        $callback = route('wechat.pc-callback');
        $url = 'https://open.weixin.qq.com/connect/qrconnect?';
        $config = config('wechat.pc');
        return $url . 'appid=' . $config['app_id'] . '&redirect_uri=' . urlencode($callback) . '&response_type=code&scope=' . $this->scope . '&state=' . $this->state . '#wechat_redirect';
    }

    #手机跳转连接
    public function mbUrl()
    {
        $this->mb();
        $callback = route('wechat.mb-callback');
        return $this->mb->oauth->scopes(['snsapi_userinfo'])->redirect($callback);
    }

    #pc回调
    public function pcCallback($input)
    {
        $code = $input['code'];
        if (!$code) throw new \Exception('code不存在，未授权');
        $config = config('wechat.pc');
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
        $token_str = $token_url . 'appid=' . $config['app_id'] . '&secret=' . $config['secret'] . '&code=' . $code . '&grant_type=' . $this->grant_type;
        $userinfo = json_decode(file_get_contents($token_str), 1);
        if (isset($userinfo['errcode'])) throw new \Exception('授权失败');
        $key = Arr::get($userinfo, $this->wechat_key);
        if (!$key) throw new \Exception("登录失败，无法获取");
        $user_id = $this->actionLogin($key);
        $bool = $this->login($user_id);
        if (!$bool) throw new \Exception('登录失败');
        return true;
    }

    #手机回调
    public function mbCallback($input)
    {
        $this->mb();
        $app = $this->mb;
        $user = $app->oauth->userFromCode($input['code']);
        $wechat = $user->getRaw();
        $key = Arr::get($wechat, $this->wechat_key);;
        if (!$key) throw new \Exception("登录失败，无法获取");
        $avatarUrl = $user->getAvatar();
        $nickname = $user->getName();
        $data = compact('avatarUrl', 'nickname');
        $user_id = $this->actionLogin($key, $data);
        $bool = $this->login($user_id);
        if (!$bool) throw new \Exception('登录失败');
        return true;
    }

    #小程序登录
    public function miniLogin($code, Request $request)
    {
        #初始化小程序配置
        $this->mini();
        $app = $this->mini;
        $data = $app->auth->session($code);
        if (isset($data['errcode']) && $data['errcode'] !=0 ) throw new \Exception('code已过期或不正确');
        $sessionKey = $data['session_key'];
        try {
            #解析信息
            $decryptedData = $app->encryptor->decryptData($sessionKey, $request->iv, $request->encryptedData);
            $wechct = Arr::get($data, $this->wechat_key);
            if (!$wechct) throw new \Exception("登录失败，无法获取");
            #处理用户信息
            $user_id =  $this->actionLogin($wechct, $decryptedData);
        }catch (\Exception $exception){
            throw new \Exception('登录过期，请重新登录');
        }
        #登录
        $token = $this->apiLogin($user_id);
        #获取用户信息
        $userinfo = $this->find($user_id);
        return [
            'token' => $token,
            'wechat_avatar' => $userinfo->wechat_avatar,
            'wechat_nickname' => $userinfo->wechat_nickname,
        ];
    }

    #api登录
    private function apiLogin($id)
    {
        return $this->getToken($id);
    }

    #登录
    private function login($id)
    {
        $user = (new Model())->find($id);
        try {
            auth()->login($user, 1);
            return  true;
        }catch (\Exception $e){
            return false;
        }
    }

    #处理登陆
    public function actionLogin($wechat, $decryptedData = [])
    {
        $user = $this->getUser($wechat);
        if (empty($user)){
            $data = [
                $this->wechat_key => $wechat,
                'nickname' => $decryptedData['nickName'] ?? '',
                'avatar' => $decryptedData['avatarUrl'] ?? '',
            ];
            $id = $this->register($data);
            if ($id === false) throw new \Exception('登陆失败');
        }else{
            $id = $user->id;
        }
        return $id;
    }

    #生成token
    private function getToken($id)
    {
        $user = $this->model()->find($id);
        $token = $user->createToken('mini_wechat')->plainTextToken;
        #记录最后登陆ip，时间
        $this->recordLastLoginIp($id);
        return $token;
    }

    #记录最后登陆ip，时间
    private function recordLastLoginIp($id)
    {
        $data = [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => \request()->ip(),
        ];
        $this->update(['id' => $id], $data);
    }

    #修改用户信息
    private function update(array $where, array $data)
    {
        DB::beginTransaction();
        try {
            $this->model()->where($where)->update($data);
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            $msg = '修改用户信息失败';
            $data['user_id'] = $this->model()->where($where)->pluck('id');
            Log::channel('user')->error($msg, ['data' => $data, 'error' => $e->getMessage()]);
            throw new \Exception($msg);
        }
    }

    #注册
    private function register($data)
    {
        $data = [
            'wechat' => $data[$this->wechat_key],
            'wechat_nickname' => $data['nickname'],
            'wechat_avatar' => $data['avatar'],
            'created_at' =>  date('Y-m-d H:i:s'),
        ];
        try {
            return $this->model()->insertGetId($data);
        }catch (\Exception $e){
            $error = $e->getMessage();
            Log::channel('user')->error('注册失败', compact('error', 'data'));
            throw new \Exception('登录失败');
        }
    }

    #获取用户
    private function getUser($wechat)
    {
        return $this->model()->where('wechat', $wechat)->first();
    }
}
