<?php

namespace Ake\Tools\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GeneratePermission extends Command
{
    #命令
    protected $signature = 'ake:permission';

    #描述
    protected $description = '备份菜单,填充菜单数据,填充权限';

    #磁盘地址
    protected $disk;

    #备份文件路径
    protected $backup_path = 'backup/menu.bak';

    #菜单模型
    protected $menu_model;

    #权限模型
    protected $permission_model;

    #菜单,权限中间表名
    protected $permission_menu_table;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->menu_model = config('admin.database.menu_model');

        $this->permission_model = config('admin.database.permissions_model');

        $this->permission_menu_table = config('admin.database.permission_menu_table');

        $this->disk = Storage::disk(config('admin.upload.disk'));
    }

    /**
     * @description:
     * @Author:AKE
     * @Date:2022/5/7 10:40
     */
    public function handle()
    {
        $type = $this->choice('请选择要执行的步骤', [
            '备份菜单', '填充权限', '填充菜单'
        ],0);

        switch ($type){
            case '备份菜单':
                $this->generateBackup();
                break;
            case '填充权限':
                $this->fillpermission();
                break;
            case '填充菜单':
                $this->fillMenu();
                break;
            default:
                $this->error('请选择要执行的步骤');
                break;
        }
    }

    /**
     * @description:生成备份文件
     * @Author:AKE
     * @Date:2022/5/7 9:46
     */
    private function generateBackup()
    {
        #获取菜单数据
        $arr = optional((new $this->menu_model())->get())->toArray();
        #写入文件
        $res = $this->disk->put($this->backup_path, serialize($arr));
        $res ? $this->info('备份成功') : $this->error('备份失败');
    }

    /**
     * @description:填充权限
     * @Author:AKE
     * @Date:2022/5/7 10:54
     */
    private function fillPermission()
    {
        $disk = $this->disk;
        #判断备份文件是否存在
        $exist = $disk->exists($this->backup_path);
        if (!$exist) {
            $this->error('请先进行菜单备份');
            return;
        }
        #获取数据并反序列化
        $menu = unserialize($disk->get($this->backup_path));
        #数据组装
        $permission = $this->generatePermissions($menu);
        DB::beginTransaction();
        try {
            $this->info('清除权限表...');
            #清空权限表
            (new $this->permission_model())->truncate();
            $this->info('清除完成');

            $this->info('填充权限...');
            $this->withProgressBar($permission, function ($permission){
                #填充权限表
                (new $this->permission_model())->insert($permission);
            });
            $this->newLine();
            $this->info('权限填充完成');

            $this->info('清除权限菜单中间表...');
            #清空权限,菜单中间表
            DB::table($this->permission_menu_table)->truncate();
            $this->info('清除完成');

            $this->info('填充权限菜单中间表...');
            $bar = $this->output->createProgressBar(count($permission));
            $bar->start();
            #循环插入权限菜单中间表
            foreach ($permission as $item) {
                $query = DB::table($this->permission_menu_table);
                #菜单id和权限id
                $query->insert([
                    'permission_id' => $item['id'],
                    'menu_id'       => $item['id'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
                #如果不是顶级,则另外绑定父级菜单
                if ($item['parent_id'] != 0) {
                    $this->recursive($item['id'], $item['parent_id']);
                }
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('权限菜单中间表填充完成');
            DB::commit();
            $this->info('权限填充完成');
        }catch (\Exception $exception){
            DB::rollBack();
            $this->error($exception->getMessage());
            $this->newLine();
            $this->error('权限填充失败');
        }
    }

    /**
     * @description:递归,添加权限(为了有三级或者四级的菜单的情况)
     * @param $id
     * @param $parent_id
     * @Author:AKE
     * @Date:2022/5/16 16:47
     */
    private function recursive($id, $parent_id)
    {
        if ($parent_id != 0){
            $query = DB::table($this->permission_menu_table);
            $query->insert([
                'permission_id' => $id,
                'menu_id' => $parent_id,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $parent_id = (new $this->permission_model())->where('id', $parent_id)->value('parent_id');
            if ($parent_id != 0){
                $this->recursive($id, $parent_id);
            }
        }
    }

    /**
     * @description:填充菜单
     * @Author:AKE
     * @Date:2022/5/7 13:20
     */
    private function fillMenu()
    {
        $disk = $this->disk;
        #判断备份文件是否存在
        $exist = $disk->exists($this->backup_path);
        if (!$exist) {
            $this->error('请先进行菜单备份');
            return;
        }
        #获取数据并反序列化
        $menu = unserialize($disk->get($this->backup_path));
        DB::beginTransaction();
        try {
            $this->info('清除菜单表...');
            #清空菜单表
            (new $this->menu_model())->truncate();
            $this->info('清除完成');

            $this->info('填充菜单...');
            $this->withProgressBar($menu, function ($menu){
                #菜单表数据填充
                (new $this->menu_model())->insert($menu);
            });
            $this->newLine();
            $this->info('菜单填充完成');
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            $this->error($exception->getMessage());
            $this->newLine();
            $this->error('填充失败');
        }

    }

    /**
     * @description:生成权限
     * @param $menu
     * @return array
     * @Author:AKE
     * @Date:2022/5/7 10:47
     */
    private function generatePermissions($menu)
    {
        $permissions = [];
        #循环整合数据
        foreach ($menu as $item) {
            #第一条不加入权限(第一条为主页,默认所有管理员都可以看到)
            if ($item['id'] == 1) continue;
            $temp = [];

            $temp['id'] = $item['id'];
            $temp['name'] = $item['title'];
            $temp['slug'] = (string)Str::uuid();
            $temp['http_path'] = $this->getHttpPath($item['uri']);
            $temp['order'] = $item['order'];
            $temp['parent_id'] = $item['parent_id'];
            $temp['created_at'] = date('Y-m-d H:i:s');
            $temp['updated_at'] = date('Y-m-d H:i:s');

            $permissions[] = $temp;
            unset($temp);
        }

        return $permissions;
    }

    /**
     * @description:生成权限链接
     * @param $uri
     * @return string
     * @Author:AKE
     * @Date:2022/5/7 10:47
     */
    private function getHttpPath($uri)
    {
        if ($uri == '/' || $uri == '') return '';
        if (strpos($uri, '/') !== 0)  $uri = '/' . $uri;
        #为了防止有两个一样的路由故这样写,例如 /index 和 /index-a 等
        #如果没有可以直接写成 $uri . '*'
        return $uri . ',' . $uri . '/*';
    }
}
