<?php
/**
 * Created by Shy
 * Date 2020/10/15
 * Time 14:10
 */


namespace app\api\controller;


use app\admin\controller\BaseController;
use function app\common\verify_data;

class Goods extends BaseController
{
    /**
     * 下单
     * @return \think\response\Json
     */
    public function add(){
        //假设是数组
        //carts 购物车ID
        verify_data('carts',$this->data);
        return \app\admin\model\Goods::add($this->data);

    }
}