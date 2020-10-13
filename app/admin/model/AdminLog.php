<?php
/**
 * Created by Shy
 * Date 2020/10/10
 * Time 10:36
 */


namespace app\admin\model;


use think\Model;

class AdminLog extends Model
{

    protected static function init(){}
    protected $createTime='created_time';
    protected $table = 'admin_log';


    protected $pk = 'id';

    protected $schema = [
        'id' => 'int',

        'admin_id' => 'admin_id',

        'content' => 'string',

        'ip' => 'string',

        'created_time' => 'int',
    ];
}