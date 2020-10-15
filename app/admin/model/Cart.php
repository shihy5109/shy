<?php
/**
 * Created by Shy
 * Date 2020/10/14
 * Time 15:58
 */


namespace app\admin\model;


use Exception;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\Model;
use think\response\Json;
use function app\common\response;

class Cart extends Model
{


    protected $createTime = 'created_time';
    protected $updataTime = 'updated_time';


    /**
     * 1对1  goods
     * @return \think\model\relation\HasOne
     */
    public function goods()
    {
        return $this->hasOne(Goods::class, 'g_id', 'g_id');
    }

    /**
     * 添加购物车
     * @param $data 购物车数据 g_id  num
     * @param $type 1.添加 2.减少
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * 后期做redis优化处理
     */
    static function addCart($data)
    {
        Db::startTrans();
        try {
            if (!empty($data)) {
                //寻找购物车中是否有当前商品的信息
                $carts = self::where(['g_id' => $data['g_id'], 'user_id' => $data['user_id']])->find();
                $g_goods = Goods::find($data['g_id']);
                if ($g_goods->status < 1 ) {
                    return response(500, '商品已下架');
                }
                if (($g_goods->num - $data['num']) < 0) {
                    return response(500, '库存不足');
                }
                //还原商品库存
                $g_arr_num = $g_goods->num - $data['num'];
                if ($carts) {
                    // +
                    $amount = ($data['num'] * $g_goods->amount) + $carts->amount;
                    $num = $carts->num + $data['num'];
                    // -
                    if ($data['type'] == '2') {
                        $num = $carts->num - $data['num'];
                        if ($num < 0) {
                            return response(500, '购物车中不存在此商品');
                        }
                        $amount = $carts->amount - ($data['num'] * $g_goods->amount);
                        //防止出现加入购物车之后后台修改金钱然后减少了购物车数量
                        if ($amount < 0) {
                            return response(500, '金额错误,联系客服');
                        }
                        //还原商品库存
                        $g_arr_num = $g_goods->num + $data['num'];
                    }
                    $cart = [
                        'id' => $carts->id,
                        'num' => $num,
                        'amount' => $amount,
                    ];
                } else {
                    $carts = new self();
                    $cart = [
                        'num' => $data['num'],
                        'amount' => $g_goods->amount,
                    ];
                }

                $g_arr = ['g_id' => $g_goods->g_id, 'num' => $g_arr_num];


                if (!$g_goods->save($g_arr)) {
                    return response(500, '商品数量出错');
                }
                if ($carts->replace()->save($cart)) {
                    //为0后清除购物车
                    if ($carts->num == 0 && $carts->amount == 0) {
                        $carts->delete();
                    }
                    Db::commit();
                    return response(200, '成功');
                }
            }
        } catch (Exception $e) {
            Db::rollback();
            return response(500, $e->getMessage());
        }
    }

    /**
     * 删除购物车
     * @param $data
     * @param int $type
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static function delCart($data,$type=1)
    {
        $id = $data['ids'];
        $arr = self::where("id in ($id)")->field('g_id,num')->select()->toArray();
        $result = self::where("id in ($id)");
        $res = $result->select();
        if ($res->delete()) {
            //还远商品库存
            if($type == 2){
                foreach ($arr as $v) {
                    $model = Goods::find($v['g_id']);
                    $model->num += $v['num'];
                    $model->save();
                }
            }

            return response(200, '成功');
        }
        return response(500);
    }



}