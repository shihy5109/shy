<?php
/**
 * Created by Shy
 * Date 2020/10/14
 * Time 15:25
 */


namespace app\api\controller;


use app\admin\controller\BaseController;
use app\admin\model\Cart;
use app\admin\model\Goods;
use function app\common\verify_data;

class Order extends BaseController
{

    public function index(){

    }


    /**
     * 添加购物车
     * 判断用户购物车中是否有数据 如果有就加入
     */
    public function addCart(){
        verify_data('cart',$this->data);
        return Cart::addCart($this->data);
    }

    /**
     * 删除购物车
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delCart(){
        verify_data('ids',$this->data);
        return Cart::delCart($this->data);
    }




}