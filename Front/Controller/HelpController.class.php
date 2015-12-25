<?php
/**
 * 帮助模型逻辑控制
 * buzhidao
 * 2015-12-15
 */
namespace Front\Controller;

use Any\Controller;

class HelpController extends BaseController
{
    //导航栏目navflag标识
    public $navflag = 'Index';

    public function __construct()
    {
        parent::__construct();
    }

    //帮助模型入口
    public function index()
    {
        $this->qa();
    }

    //常见问题
    public function qa()
    {
        $this->assign("resumenavflag", "qa");

        $this->display();
    }
}