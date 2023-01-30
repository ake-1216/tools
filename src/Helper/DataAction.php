<?php
/**
 * @file 数据处理类
 */
namespace Ake\Tools\Helper;

use Ake\Tools\Exceptions\DataActionException;
use Illuminate\Support\Facades\DB;

class DataAction
{
    /**
     * @var string 表名
     */
    private $table;

    /**
     * @var string 需要处理的字段
     */
    private $name = 'title';

    /**
     * @var string 父级字段
     */
    private $parent_id = 'parent_id';

    /**
     * @var object|null 模型实例
     */
    private $model = null;

    /**
     * @var string 排序字段
     */
    private $order = ['order' => 'desc'];

    /**
     * @var string id名
     */
    private $id_name = 'id';

    /**
     * @var array 搜索条件
     */
    private $where = [];

    /**
     * @param array $config
     * [
     * 'model' => 'model实例对象',
     * 'table' => '数据表名' （model和table填一即可）,
     * 'order' => '排序（数组，key是排序字段，value是排序规则）'，
     * 'parent_id' => '父级id',
     * 'name' => '名'，
     * 'is_name' => 'id的字段名'
     * ]
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $k => $v){
            if (property_exists($this, $k)){
                $this->$k = $v;
            }
        }
    }

    /**
     * @description: 根据id获取所有的子类
     * @param string $pid 要处理的父级id
     * @param int $is_self 是否包含本身
     * $config ['order', 'parent_id']
     * @return array
     * @throws DataActionException
     * @Author:AKE
     * @Date:2022/11/30 11:44
     */
    public function getChildById(string $pid, int $is_self = 0) :array
    {
        if ($is_self == 0) return $this->child($pid);
        $res = $this->first(['id' => $pid]);
        $res = objToArr($res);
        $arr = $this->child($pid);
        $res['child'] = $arr;
        return $res;

    }

    /**
     * @description: 根据id获取表内所有子类id
     * @param int $id 父级id
     * $config ['parent_id']
     * @return array
     * @throws DataActionException
     * @Author:AKE
     * @Date:2022/11/30 11:43
     */
    public function getAllIdById(int $id) :array
    {
        $arr = [];
        $res = $this->model()->where($this->parent_id, $id)->get();
        if (!empty($res)) {
            foreach ($res as $v) {
                $arr[] = $v->id;
                if (!empty($this->exist([$this->parent_id => $v->id]))) {
                    $res = $this->getAllIdById($v->id);
                    $arr = array_merge($res, $arr);
                }
            }
        }
        array_unique($arr);
        return $arr;
    }

    /**
     * @description: 根据id获取自身及父级分类下关联表所有id
     * @param string $relevance 关联表名
     * @param int $id 要处理的主表id
     * @param string $relevance_id 关联字段名
     * $config['table', 'parent_id']
     * @return mixed
     * @throws DataActionException
     * @Author:AKE
     * @Date:2022/11/30 11:51
     */
    public function getRelevanceById(string $relevance, int $id, string $relevance_id = '')
    {
        $relevance_id = $relevance_id ?: $this->table . '_id';
        $arr = $this->getAllIdById($id);
        $arr[] = $id;
        return $this->model($relevance)->whereIn($relevance_id, $arr)->pluck('id');
    }

    /**
     * @description: 根据id获取所有的子类
     * @param int $pid 要处理的父级id
     * @return array
     * @throws DataActionException
     * @Author:AKE
     * @Date:2022/11/30 11:43
     */
    private function child(int $pid) : array
    {
        $model = $this->model()->where($this->parent_id, $pid);
        if (is_array($this->where) && !empty($this->where)) {
            $model = $model->where($this->where);
        }
        if ($this->order) {
            foreach ($this->order as $key => $item){
                $model = $model->orderBy($key, $item);
            }
        }
        $arr = [];
        $res = $model->get()->toArray();
        if (!empty($res)) {
            foreach ($res as $k => $v) {
                $tmp = objToArr($v);
                $arr[$k] = $tmp;
                if (!empty($this->exist([$this->parent_id => $tmp['id']]))) {
                    $arr[$k]['child'] = $this->child($tmp['id']);
                }
            }
        }
        return $arr;
    }

    /**
     * @description:根据指定条件获取一条记录
     * @param array $w 条件数组
     * @return mixed
     * @throws DataActionException
     * @Author:AKE
     * @Date:2022/11/30 11:43
     */
    private function first(array $w)
    {
        return $this->model()->where($w)->first();
    }

    /**
     * @description:根据指定条件判断数据是否存在
     * @param array $w
     * @return boolean
     * @throws DataActionException
     * @Author:AKE
     * @Date:2022/11/30 11:43
     */
    private function exist(array $w)
    {
        return $this->model()->where($w)->exists();
    }

    /**
     * @description:获取model
     * @param string $table 表名
     * @return object 返回model实例（可能是model对象，也可能是DB对象）
     * @throws DataActionException
     * @Author:AKE
     * @Date:2022/11/30 11:49
     */
    private function model(string $table = '') :object
    {
        if ($table) return DB::table($table);
        if (is_null($this->model) && !$this->table) throw new DataActionException('model');
        return $this->model ?? DB::table($this->table);
    }

}
