<?php
/**
 * Created by Shy
 * Date 2020/10/14
 * Time 15:28
 */


namespace app\admin\model;


use Exception;
use think\facade\Db;
use think\Model;
use function app\common\response;

class Goods extends Model
{

    protected $pk = 'g_id';

    /**
     * 创建订单(一订单对应多商品模式)
     * @param $data
     * @return \think\response\Json
     */
    static function add($data)
    {
        Db::startTrans();
        try {
            if (!empty($data['carts'])) {

                //生成一条订单
                foreach ($data['carts'] as $v) {
                    $cart = Cart::find($v);
                    if (!$cart) {
                        return response(500, '购物车不存在');
                    }
                    if ($cart->goods->status < 1) {
                        return response(500, $cart->goods->name . '的商品已下架,请清除购物车后重新下单');
                    }
                    //组装order_goods
                    $arr_order_goods[$v]['order_id'] = 0;
                    $arr_order_goods[$v]['user_id'] = $data['user_id'];
                    $arr_order_goods[$v]['goods_id'] = $cart->goods->g_id;
                    $arr_order_goods[$v]['discount'] = $cart->goods->discount;
                    $arr_order_goods[$v]['amount'] = $cart->amount - $cart->goods->discount; //购物车金额 - 商品优惠金额
                }
                //寻找用户对应优惠卷
                if ($data['user_rebate_id']) {
                    $user_rebate = 10;
//                $arr_order_goods[$v]['amount'] -= $user_rebate;
                }
                //组装order
                $arr_order_amount = round(array_sum(array_column(array_values($arr_order_goods), 'amount')),3)?:'0.000';
                $arr_order_discount = round(array_sum(array_column(array_values($arr_order_goods), 'discount')),3)?:'0.000';
                $arr_order['order'] = 'E' . date("YmdHis") . rand(1000, 9999);
                $arr_order['user_id'] = $data['user_id'];
                $arr_order['amount'] = $arr_order_amount - $user_rebate ?: 0;
                $arr_order['discount'] = $arr_order_discount;
                $order = new Order();
                if (!$order->replace()->save($arr_order)) {
                    return response(500, '订单创建失败');
                }
                foreach ($arr_order_goods as &$v) {
                    $v['order_id'] = $order->id;
                }
                $order_goods = new OrderGoods();
                if (!$order_goods->replace()->saveAll(array_values($arr_order_goods))) {
                    return response(500, '订单创建失败');
                }
                $cart_ids['ids'] = implode(',', $data['carts']);
                Cart::delCart($cart_ids,2);
                Db::commit();
                return response(200, '购买成功');
            }
            return response(500, '购物车为空');
        }
        catch (Exception $e) {
            Db::rollback();
            return response(500, $e->getMessage());
        }
    }


}