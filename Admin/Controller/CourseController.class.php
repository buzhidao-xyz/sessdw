<?php
/**
 * 课程模块控制器
 * buzhidao
 * 2015-12-27
 */
namespace Admin\Controller;

class CourseController extends CommonController
{
    public function __construct()
    {
        parent::__construct();

        $this->assign("sidebar_active", array("Course","index"));

        $this->_page_location = __APP__.'?s=Course/index';
    }

    public function index()
    {
        $this->display();
    }
}