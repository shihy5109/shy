<?php
/**
 * Created by Shy
 * Date 2020/10/12
 * Time 13:41
 */


namespace app\admin\controller;

use app\admin\model\Group;
use app\admin\model\Rule;
use app\common\Common;
use think\facade\Db;
use function app\common\verify_data;
use function app\common\response;
class Rbac extends BaseController
{

    //角色列表
    public function index(){
        verify_data('name', $this->data);
        $where = '';
        if($this->data['name']){
            $where = ['name'=>$this->data['name']];
        }
        $sql = Db::name('group')
            ->where($where)
            ->select()
            ->toArray();
        return response(200,'成功',$sql?:[]);
    }

    //修改
    public function update($id){
        verify_data('id,name', $this->data);
        $group = Group::find($id);
        if($group){
            $group->name = $this->data['name'];
            if($group->save()){
                Common::insertLog($this->data['admin_id'],'修改id为'.$id.'的管理员');
                return response(200,'成功');
            }
        }
    }



    //删除
    public function delete(){
        verify_data('id', $this->data);
        return Group::del($this->data);
    }

    //给角色配置权限
    public function authority(){
        return response(500,'商量authority数据格式');
        verify_data('admin_id,id,authority', $this->data);
        Db::startTrans();
        try {
            $group = Group::find($this->data['id']);
            if($group){
                //添加
                Rule::where(['group_id'=>$this->data['id']])->delete();
//                $this->data['authority'] = [['menu_id'=>1,'menu_path'=>''],['menu_id'=>2,'menu_path'=>'index/index'],['menu_id'=>3,'menu_path'=>'index/delete']];
//                var_dump(json_decode($this->data['authority'],true));die;
                foreach ($this->data['authority'] as &$v){
                    $v['group_id'] = $this->data['id'];
                }
                $rule = new Rule();
                if($rule->replace()->saveAll($this->data['authority'])){
                    Db::commit();
                    Common::insertLog($this->data['admin_id'],$this->data['admin_id'].'修改'.$group->name.'用户组');
                    return response(200,'配置完成');
                }
            }
            return response(500,'用户组不存在');
        } catch (\Exception $e) {
            Db::rollback();
            return response(500,$e->getMessage());
        }

    }



}