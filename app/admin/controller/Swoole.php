<?php

namespace server;

/**
 * 概要描述：TCP服务器
 * @author: shy
 * @since: 2020-05-23 22:45
 */
class TcpServer
{
    protected $serv = null;       //Swoole\Server对象
    protected $host = '0.0.0.0'; //监听对应外网的IP 0.0.0.0监听所有ip
    protected $port = 9601;      //监听端口号

    public function __construct()
    {
        $this->serv = new \Swoole\Server($this->host, $this->port);

        //设置参数
        //如果业务代码是全异步 IO 的，worker_num设置为 CPU 核数的 1-4 倍最合理
        //如果业务代码为同步 IO，worker_num需要根据请求响应时间和系统负载来调整，例如：100-500
        //假设每个进程占用 40M 内存，100 个进程就需要占用 4G 内存
        $this->serv->set(array(
            'worker_num' => 4,         //设置启动的worker进程数。【默认值：CPU 核数】
            'max_request' => 10000,    //设置每个worker进程的最大任务数。【默认值：0 即不会退出进程】
            'daemonize' => 0,          //守护进程化【默认值：0】
        ));

        //监听链接进入事件
        $this->serv->on('connect', function ($serv, $fd) {
            echo '链接成功';
        });

        //监听数据接收事件
        $this->serv->on('receive', function ($serv, $fd, $from_id, $data) {

            var_dump($fd.'--'.$data);

            /**
             * 函数：bool Server->send(mixed $fd, string $data, int $serverSocket = -1);
             * 作用：向客户端发送数据
             * 参数：
             *  $fd，客户端的文件描述符
             *  $data，发送的数据，TCP协议最大不得超过2M，可修改 buffer_output_size 改变允许发送的最大包长度
             *  $serverSocket，向Unix Socket DGRAM对端发送数据时需要此项参数，TCP客户端不需要填写
             */
            $this->serv->send($fd, "服务端向用户{$fd}发送数据：{$data}");
        });

        //监听链接关闭事件
        $this->serv->on('close', function ($serv, $fd) {
            echo '关闭链接';
        });

        //启动服务
        $this->serv->start();
    }
}

$tcpServer = new TcpServer();