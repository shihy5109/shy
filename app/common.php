<?php
// 应用公共文件
namespace app\common;

use app\admin\model\AdminLog;

class Common
{
    /**
     * @param $admin_id
     * @param $content
     * @return bool|false|string
     */
    static function insertLog($admin_id, $content)
    {
        $log = new AdminLog();
        $result = [
            'admin_id' => $admin_id,
            'content' => $content ?: '',
            'ip' => $_SERVER["REMOTE_ADDR"] ?: '',
        ];
        $log_insert = $log->save($result);
        if (!$log_insert) {
            return response(500, '日志出错');
        }
    }


    //随机取4位字符串
    static function random_string($length = 4)
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $name = substr(str_shuffle($chars), mt_rand(0, strlen($chars) - 11), $length);
        return $name;
    }

}

/**
 * @param $prams //我需要的字段（字符串） , 隔开
 * @param $v_data //前端数据
 * @param $type //
 * @return false|string
 */
if (!function_exists('verify_data')) {
    function verify_data($prams, $v_data,$type=1)
    {
        $prams = explode(',', $prams);

        foreach ($prams as $v) {
            $result = [
                'status' => 501,
                'msg' => '缺少字段:'.$v,
                'data' => [],
            ];
            if($type == 2){
                $result['page'] = 0;
                $result['page_number'] = 10;
            }
            if (!array_key_exists($v, $v_data)) {
                echo  json_encode($result);die;
            }
            //指定字段
            if($v == 'authority'){
                if(is_array($v)){
                    $result['mes'] = 'authority必须是json格式';
                    echo  json_encode($result);die;
                }
            }
        }
    }
}

if (!function_exists('response')) {
    /**
     * @param int $status
     * @param string $msg
     * @param array $data
     * @return \think\response\Json
     */
//    $page = 1,$page_number = 10
    function response($status = 200, $msg = '', $data = [])
    {
        $result = [
            'status' => $status,
            'msg' => $msg,
            'data' => $data?:(object)[],
//            'page' => $page,
//            'page_number' => $page_number,
        ];
        return json($result);

    }
}
