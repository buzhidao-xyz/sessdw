<?php
/**
 * 作业模型逻辑控制
 * buzhidao
 * 2015-12-08
 */
namespace Front\Controller;

use Any\Controller;

class WorkController extends CommonController
{
    //导航栏目navflag标识
    public $navflag = 'Work';

    public function __construct()
    {
        parent::__construct();
    }

    //作业首页
    public function index()
    {
        $this->display();
    }
}