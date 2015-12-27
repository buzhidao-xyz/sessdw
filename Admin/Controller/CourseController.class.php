<?php
/**
 * 课程模块控制器
 * buzhidao
 * 2015-12-27
 */
namespace Admin\Controller;

class CourseController extends CommonController
{
    // 课程分类
    private $_course_class = array(
        1 => array('id'=>1, 'name'=>'学党章'),
        2 => array('id'=>2, 'name'=>'学讲话'),
        3 => array('id'=>3, 'name'=>'学条例'),
    );

    public function __construct()
    {
        parent::__construct();

        $this->assign("sidebar_active", array("Course","index"));

        $this->_page_location = __APP__.'?s=Course/index';

        $this->assign("classlist", $this->_course_class);
    }

    //获取课程id
    private function _getCourseid()
    {
        $courseid = mRequest('courseid');

        return $courseid;
    }

    //获取课程标题
    private function _getTitle()
    {
        $title = mRequest('title');
        if (!$title) $this->ajaxReturn(1, '请填写课程标题！');

        return $title;
    }

    //获取课程分类
    private function _getClassid()
    {
        $classid = mRequest('classid');
        if (!$classid) $this->ajaxReturn(1, '请选择课程分类！');

        return $classid;
    }

    //获取搜索关键字
    private function _getKeywords()
    {
        $keywords = mRequest('keywords');
        $this->assign('keywords', $keywords);

        return $keywords;
    }

    //课程管理
    public function index()
    {
        $keywords = $this->_getKeywords();

        list($start, $length) = $this->_mkPage();
        $data = D('Course')->getCourse(null, null, $keywords, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $param = array(
            'keywords'   => $keywords,
        );
        $this->assign('param', $param);
        //解析分页数据
        $this->_mkPagination($total, $param);

        $this->display();
    }

    //上传课程示例图
    public function showimgupload()
    {
        
    }
    
    //上传视频封面图
    public function videoimgupload()
    {
        
    }

    //发布课程
    public function newcourse()
    {
        $this->display();
    }

    //编辑课程
    public function upcourse()
    {
        $this->display();
    }

    //保存课程
    public function coursesave()
    {

    }
}