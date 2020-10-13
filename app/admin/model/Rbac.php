<?php
/**
 * Created by Shy
 * Date 2020/10/12
 * Time 11:07
 */


namespace app\admin\model;


use think\facade\Db;

class Rbac
{

    /**
     * 寻找用户权限
     * @param $admin_id
     * @param $request 路径
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    static function FindUserPath($admin_id,$request){
        if(!$admin_id){
            return \app\common\response(501,'admin_id');
        }
        //可存redis或cache中  后期可优化
        $result = Db::name('admin')
            ->alias('a')
            ->leftJoin('rule b','a.group_id=b.group_id')
            ->where(['a.admin_id'=>$admin_id,'b.menu_path'=>$request])
            ->field('a.admin_id')
            ->select()
            ->toArray();
        if($result){
            return true;
        }
        return \app\common\response(500,'没有权限');
    }
}