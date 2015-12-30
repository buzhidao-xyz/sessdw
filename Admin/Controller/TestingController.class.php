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

    //获取试卷id
    private function _getTestingid()
    {
        $testingid = mRequest('testingid');

        return $testingid;
    }

    //试卷管理
    public function index()
    {
        $this->display();
    }

    //添加试卷
    public function newtesting()
    {
        $courseid = $this->_getCourseid();
        $this->assign('courseid', $courseid);

        //获取课程列表
        $courselist = D('Course')->getCourse();
        $courselist = $courselist['data'];
        $this->assign('courselist', $courselist);

        $this->display();
    }

    //查看试卷详情 - 编辑
    public function profile()
    {
        $courseid = $this->_getCourseid();
        $testingid = $this->_getTestingid();

        //获取试卷信息
        $testinginfo = D('Testing')->getTestingByID($courseid, $testingid);
        if (!is_array($testinginfo) || empty($testinginfo)) {
            header('Location:'.__APP__.'?s=Testing/newtesting&courseid='.$courseid);
            exit;
        }

        //获取试卷的试题信息
        

        $this->display();
    }
}