<?php
/**
 * 随堂测试模型逻辑控制
 * buzhidao
 * 2015-12-08
 */
namespace Front\Controller;

use Any\Controller;

class TestingController extends CommonController
{
    //导航栏目navflag标识
    public $navflag = 'Testing';

    public function __construct()
    {
        parent::__construct();
    }

    //随堂测评首页
    public function index()
    {
        $this->display();
    }

    //随堂测评 试卷页
    public function exam()
    {
        $this->display();
    }

    //随堂测评结果页
    public function profile()
    {
        $this->display();
    }
}