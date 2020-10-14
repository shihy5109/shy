<?php
/**
 * Created by Shy
 * Date 2020/10/14
 * Time 10:15
 */


namespace app\admin\controller;


use think\facade\Db;
use function app\common\response;
use function app\common\verify_data;

class Categories extends BaseController
{
    public function index(){
        $sql = Db::name('categories')
            ->where(['parent_id'=>0])
            ->order('sort')
            ->select()
            ->toArray();
        if($sql){
            foreach ($sql as &$value){
                $son = Db::name('categories')
                    ->where(['parent_id'=>$value['id']])
                    ->order('sort')
                    ->select()
                    ->toArray();
                if($son){
                    $value['son'] = $son;
                }
            }
        }
        return response(200,'成功',$sql);
    }


    public function add(){
        if(\think\facade\Request::isGet()){
            $sql = Db::name('categories')
                ->where(['parent_id'=>0])
                ->field('id,name')
                ->order('sort')
                ->select()
                ->toArray();
            return response(200,'成功',$sql);
        }
        verify_data('parent_id,name,status,sort,img',$this->data);
        $model = new \app\admin\model\Categories();
        if($model->save(\think\facade\Request::only(['parent_id','name','status','sort','img']))){
            return response(200,'成功');
        }
        return  response(500,'失败');
    }


    public function update(){
        if(\think\facade\Request::isGet()){
            $sql = Db::name('categories')
                ->where(['parent_id'=>0])
                ->field('id,name')
                ->order('sort')
                ->select()
                ->toArray();
            return response(200,'成功',$sql);
        }
        verify_data('id,parent_id,name,status,sort,img',$this->data);
        $model = \app\admin\model\Categories::find($this->data['id']);
        if($model->save(\think\facade\Request::only(['id','parent_id','name','status','sort','img']))){
            return response(200,'成功');
        }
        return response(500,'失败');
    }



    public function delete(){
        verify_data('id', $this->data);
        return \app\admin\model\Categories::del($this->data);
    }



    public function sort(){
        verify_data('id,sort', $this->data);
        return \app\admin\model\Categories::sort($this->data);
    }





}