<?php
/**
 * 在线课程逻辑层
 * buzhidao
 */
namespace Weixin\Controller;

class CourseController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->display();
    }
}