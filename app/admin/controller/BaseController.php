<?php
/**
 * Created by Shy
 * Date 2020/10/12
 * Time 10:31
 */


namespace app\admin\controller;


use app\admin\model\Rbac;
use think\facade\Request;
use function app\common\verify_data;

class BaseController
{

    public $data;

    public function __construct()
    {
        $controller =  strtolower(app('request')->controller());
        $action =  app('request')->action();
        $quest = $controller.'/'.$action;
        $this->data = Request::request(); //表头信息 可接收 post get json xml格式数据
        $this->initialize($quest);
    }

    protected function initialize($quest)
    {
        return true;
        verify_data('admin_id', $this->data);
        $ignoreList = array('index/login','index/status');
        if(in_array($quest,$ignoreList)){
           return true;
        }
       return Rbac::FindUserPath($this->data['admin_id'],$quest);
    }

}