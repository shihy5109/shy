<?php
/**
 * Created by Shy
 * Date 2020/10/26
 * Time 16:30
 */


namespace app\admin\controller;


class Tcp extends BaseController
{

    public function demo()
    {
        // 创建TCP客户端
        $client = new \Swoole\Client(SWOOLE_SOCK_TCP);

        /**
         * 函数：bool Client->connect(string $host, int $port, float $timeout = 0.5)
         * 作用：连接到服务器
         * 参数：
         *  $host，远程服务器的地址
         *  $port，远程服务器端口
         *  $timeout，网络 IO 的超时时间
         */
        if (!$client->connect('127.0.0.1', 9601, 1)) {
            die("connect failed.");
        }

        //向服务器发送数据
        if (!$client->send("hello world")) {
            echo '发送失败';
        }

        //从服务器接收数据
        $data = $client->recv();
        if (!$data) {
            die("recv failed.");
        }

        //打印从服务端接收到的数据
        echo $data;

        //关闭连接
        $client->close();
        exit();
    }
}