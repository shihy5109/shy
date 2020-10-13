<?php


namespace app\admin\model;


use app\common\Common;
use think\Model;
use function app\common\response;

class Admin extends Model
{
    protected static function init(){}

    protected $createTime='created_time';
    protected $updataTime='updated_time';

    protected $table = 'admin';

    protected $name = 'admin';

    protected $pk = 'admin_id';

    protected $schema = [
        'admin_id' => 'int',

        'username' => 'string',

        'password' => 'string',

        'phone' => 'string',

        'type' => 'int',

        'sort_id' => 'int',

        'status' => 'int',

        'group_id' => 'int', //用户组

        'created_time' => 'int',

        'updated_time' => 'int',
    ];

    /**
     * @param $password
     * @param $transport_pass
     * @return bool
     */
    static function encryption($password,$transport_pass){
        if($password == sha1($transport_pass)){
            return true;
        }
        return false;
    }

     static function status($admin_id,$status){
        $model = self::find($admin_id);
        if($model){
            $model->status = $status;
            if ($model->save()){
                Common::insertLog($admin_id,'修改'.$model->getAttr('username').'用户状态,当前状态:'.$status);
                return response(200,'成功');
            }
        }
        return response(500,'数据不存在');
    }

    static function del($admin_id){
        $admin = self::find($admin_id);
        if ($admin && $admin_id) {
            $admin->status = 0;
            $result = $admin->save();
            if ($result) {
                Common::insertLog($admin_id,'删除用户名为'.$admin->getAttr('username').'的管理员');
                return response(200, '删除成功');
            }
        }
        return response(500, '用户不存在或传值有误');
    }

}