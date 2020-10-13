<?php
/**
 * Created by Shy
 * Date 2020/10/13
 * Time 13:45
 */


namespace app\admin\controller;


use app\admin\model\Admin;
use app\admin\model\AdminLog;
use app\admin\model\Group;
use app\common\Common;
use think\facade\Db;
use function app\common\response;
use function app\common\verify_data;

class Menu extends BaseController
{
    //查
    public function index(){
        verify_data('name', $this->data);
        $where = '';
        if($this->data['name']){
            $where = ['name'=>$this->data['name']];
        }
        $sql = Db::name('menu')
            ->where($where)
            ->select()
            ->toArray();
        return response(200, 'success', $sql ?: []);
    }

    //增
    public function add(){
        verify_data('name,path,status,sort,type', $this->data);
        if(\app\admin\model\Menu::create($this->data,['name','path','status','sort','type'])){
            Common::insertLog($this->data['admin_id'],'添加菜单');
            return response(200,'成功');
        }
        return response(500,'添加失败');
    }



    //删
    public function delete(){
        verify_data('id', $this->data);
        return \app\admin\model\Menu::del($this->data);
    }


    //改
    public function update(){
        verify_data('id,name,path,status,sort,type', $this->data);
        $menu = \app\admin\model\Menu::find($this->data['id']);
        if($menu){
            if($menu->save($this->data)){
                Common::insertLog($this->data['admin_id'],'修改id为'.$this->data['id'].'的菜单');
                return response(200,'成功');
            }
        }
    }

    public function status(){
        verify_data('id,status', $this->data);
        return \app\admin\model\Menu::status($this->data);
    }



    //系统日志
    public function log(){
        verify_data('username,start_time,end_time', $this->data,2);
        $admin_id = false;
        if($this->data['username']){
            $admin = Admin::getByUsername($this->data['username']);
            if(!$admin){
                return response(500,'用户不存在');
            }
            $admin_id = $admin->admin_id;
        }
        $prams = $admin_id?['a.admin_id'=>$admin_id]:'';
        $data = Db::name('admin_log')
            ->alias('a')
            ->field('a.id,b.username,a.content,a.created_time,a.ip')
            ->leftJoin('admin b','a.admin_id=b.admin_id')
            ->where($prams?:'')
            ->whereTime('a.created_time','between',[strtotime($this->data['start_time']),strtotime($this->data['end_time'])])
//            ->fetchSql();
            ->page($this->data['page'],$this->data['page_number'])
            ->select()
            ->toArray();
        return response(200,'成功',$data);
    }



}