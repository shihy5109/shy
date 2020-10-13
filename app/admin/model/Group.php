<?php
/**
 * Created by Shy
 * Date 2020/10/13
 * Time 10:33
 */


namespace app\admin\model;


use app\common\Common;
use think\Model;
use function app\common\response;

class Group extends Model
{
    protected static function init(){}

    protected $createTime='created_time';
    protected $updataTime='updated_time';

    static function del($id){
        $group = self::find($id);
        if ($group && $id) {
            $group->status = 0;
            $result = $group->save();
            if ($result) {
                Common::insertLog($id,'删除用户名为'.$group->getAttr('name').'的管理员');
                return response(200, '删除成功');
            }
        }
        return response(500, '用户不存在或传值有误');
    }
}