<?php
/**
 * Created by Shy
 * Date 2020/10/13
 * Time 14:58
 */


namespace app\admin\model;


use app\common\Common;
use think\Model;
use function app\common\response;

class Menu extends Model
{
    static function del($data){
        $menu = self::find($data['id']);
        if ($menu && $data['id']) {
            if ($menu->delete()) {
                Common::insertLog($data['admin_id'],'删除名称为'.$menu->getAttr('name').'的菜单');
                return response(200, '删除成功');
            }
        }
        return response(500, '菜单不存在或传值有误');
    }


    static function status($data){
        $model = self::find($data['id']);
        if($model){
            $model->status = $data['status'];
            if ($model->save()){
                Common::insertLog($data['admin_id'],'修改id为：'.$data['id'].'的菜单状态'.$data['status']);
                return response(200,'成功');
            }
        }
        return response(500,'数据不存在');
    }
}