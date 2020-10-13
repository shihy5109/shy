<?php
/**
 * Created by Shy
 * Date 2020/10/13
 * Time 13:45
 */


namespace app\admin\controller;


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

}