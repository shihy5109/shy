<?php
/**
 * Created by Shy
 * Date 2020/10/14
 * Time 10:34
 */


namespace app\admin\model;


use app\common\Common;
use think\Model;
use function app\common\response;

class Categories extends Model
{
    static function del($data){
        $Categories = self::find($data['id']);
        if ($Categories && $data['id']) {
            if ($Categories->delete()) {
                Common::insertLog($data['admin_id'],'删除名称为'.$Categories->getAttr('name').'的商品分类');
                return response(200, '删除成功');
            }
        }
        return response(500, '菜单不存在或传值有误');
    }

    static function sort($data){
        $model = self::find($data['id']);
        if($model){
            $model->status = $data['sort'];
            if ($model->save()){
                Common::insertLog($data['admin_id'],'修改id为：'.$data['id'].'的商品分类排序'.$data['sort']);
                return response(200,'成功');
            }
        }
        return response(500,'数据不存在');
    }
}