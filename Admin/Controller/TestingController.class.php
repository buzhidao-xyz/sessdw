<?php
/**
 * 试卷模块控制器
 * buzhidao
 * 2015-12-27
 */
namespace Admin\Controller;

class TestingController extends CommonController
{
    public function __construct()
    {
        parent::__construct();

        $this->assign("sidebar_active", array("Testing","index"));

        $this->_page_location = __APP__.'?s=Testing/index';

        $this->assign("classlist", D('Course')->_course_class);
    }

    //获取课程id
    private function _getCourseid()
    {
        $courseid = mRequest('courseid');

        return $courseid;
    }

    //查看试卷详情
    public function profile()
    {
        $courseid = $this->_getCourseid();

        $this->display();
    }
}