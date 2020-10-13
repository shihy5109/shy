<?php
declare (strict_types=1);

namespace app\admin\controller;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use app\admin\model\Admin;
use app\admin\model\Verification;
use app\common\Common;
use think\App;
use think\cache\driver\Redis;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use function app\common\response;
use function app\common\verify_data;

class Index extends BaseController
{
    const Shy = 1;


    /**
     * 登录
     * @return string
     */
    public function login()
    {
        $redis = new Redis();
        if (Request::isPost()) {
            verify_data('username,password', $this->data);
            $admin = Admin::getByUsername($this->data['username']);
            if ($admin && $admin->status === 1) {
                if (Admin::encryption($admin->password, $this->data['password']) && $this->data['captcha'] == $redis->get('captcha_' . $this->data['random'])) {
                    Common::insertLog($admin->admin_id, '登录');
                    return response(200, 'success');
                }
            }
            return response(500, 'Incorrect username or password');
        } else {
            //验证码
            $code = new Verification();
            $random = sha1((string)time());
            $redis->set('captcha_' . $random, $code->code, 120);
            View::assign([
                'img' => $code->outImage(),
                'code' => $code->code,
            ]);
            return View::fetch();
        }
    }


    public function index()
    {
        verify_data('type,sort_id,status', $this->data);
        $prams = [
            'a.type' => $this->data['type'],
            'a.sort_id' => $this->data['sort_id'],
            'a.status' => $this->data['status'],
        ];
        foreach ($prams as $key => &$v) {
            if (!$v) {
                unset($prams[$key]);
            }
        }
        $sql = Db::name('admin')
            ->where($prams)
            ->alias('a')
            ->leftJoin('sort b', 'a.sort_id=b.sort_id')
//            ->fetchSql()
            ->field('a.username,a.phone,a.status,a.created_time,b.name')
            ->select()
            ->toArray();
        return response(200, 'success', $sql ?: []);
    }


    public function add()
    {
        if (!preg_match('/^[a-z\d]*$/i', $this->data['username'])) {
            return response(500, '用户名必须是英文、数字或组合 1');
        }
        if (strlen($this->data['username']) < 3 || strlen($this->data['username']) > 12 || strlen($this->data['password']) < 6 || strlen($this->data['password']) > 12) {
            return response(500, '用户名,登陆密码必须6-12位 2');
        }
        $is_admin = Admin::getByUsername($this->data['username']);
        if ($is_admin) {
            return response(500, '用户名已存在 3');
        }
        $validate = new Admin();
        $save_data = Request::only(['username', 'password']);
        $save_data['password'] = sha1($save_data['password']);
        $result = $validate->save($save_data);
        Common::insertLog(1, '添加用户 ID:' . $validate->id);
        if ($result) {
            return response(200, '添加成功');
        }
        return $validate->getError();
    }


    //上传图片
    public function photo()
    {
        $time = date('YmdHis');
        if ($_FILES["file"]["error"]) {
            return response(500, $_FILES["file"]["error"]);
        } else {
            if (($_FILES["file"]["type"] == "image/png" || $_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/jpg" || $_FILES["file"]["type"] == "image/gif") && $_FILES["file"]["size"] < 1024000 * 3) {
                $filename = \think\facade\App::getRootPath() . '/public/static/img/' . $time . '.jpg';
                $filename = iconv("UTF-8", "gb2312", $filename);
                move_uploaded_file($_FILES["file"]["tmp_name"], $filename);//将临时地址移动到指定地址
                $url = Request::host() . '/images/' . $time . '.jpg';

                return response(200, '成功', ['url' => $url]);
            } else {
                return response(500, '文件类型不对');
            }
        }
    }


    //导出excel
    public function excel_put()
    {
        $data = Db::name('admin')->select();
        $title = ['admin_id', 'username', 'password', 'phone', 'type', 'sort_id', 'status', 'group_id', 'created_time', 'updated_time'];
        return self::excels($title, $data);
    }


    //导入excel数据
    public function excel_get(){
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($_FILES['excel']['tmp_name']);
        foreach ( $spreadsheet->getWorksheetIterator() as $cell ) {
            $cells = $cell->toArray();
        }
        unset($cells[0]);
        foreach ( $cells as $key=>$cell ) {
            var_dump($cells);die;
            // 添加或更新数据
        }
    }


    /**
     * 删除
     * @param $id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete($id)
    {
        verify_data('id', $this->data);
        return Admin::del($id);
    }


    /**
     * 修改状态
     * @param $admin_id
     * @param $status
     */
    public function status($admin_id, $status)
    {
        verify_data('admin_id,status', $this->data);
        return Admin::status($admin_id, $status);
    }


    /**
     * 导出Excel表格 Xlsx格式(2007版)
     *
     * @datetime 2019-12-22
     *
     * @param  array  $title    表头单元格内容
     * @param  array  $data     从第二行开始写入的数据
     * @param  string $path 	Excel文件保存位置,路径中的目录必须存在
     *
     * @return null 没有设定返回值
     */
    private function excels($title = [], $data = [], $path = '')
    {
        // 获取Spreadsheet对象
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 表头单元格内容 第一行
        $titCol = 'A';
        foreach ($title as $value) {
            // 单元格内容写入
            $sheet->setCellValue($titCol . '1', $value);
            $titCol++;
        }


        // 从第二行开始写入数据
        $row = 2;
        foreach ($data as $item) {
            $dataCol = 'A';
            foreach ($item as $value) {
                // 单元格内容写入
                $sheet->setCellValue($dataCol . $row, $value);
                $dataCol++;
            }
            $row++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = Common::random_string();
        $writer->save($filename.'.xlsx');
        return  $filename.'xlsx';
    }

}
