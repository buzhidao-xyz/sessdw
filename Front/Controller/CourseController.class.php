<?php
/**
 * 课程模型逻辑控制
 * buzhidao
 * 2015-12-08
 */
namespace Front\Controller;

use Any\Controller;

class CourseController extends CommonController
{
    //导航栏目navflag标识
    public $navflag = 'Course';

    public function __construct()
    {
        parent::__construct();

        $this->_course_class = C('USER.course_class');
        $this->assign('courseclass', $this->_course_class);

        $this->_user_course_status = C('USER.user_course_status');
        $this->assign('usercoursestatus', $this->_user_course_status);
    }

    //获取课程分类Id
    private function _getClassid()
    {
        $classid = mRequest('classid');

        return $classid;
    }

    //课程首页
    public function index()
    {
        $classid = $this->_getClassid();
        $this->assign('classid', $classid);

        list($start, $length) = $this->_mkPage();
        $data = D('Course')->getCourse(null, $classid, $this->userinfo['userid'], $start, $length);
        $total = $data['total'];
        $courselist = $data['data'];

        $this->assign('courselist', $courselist);

        $param = array(
            'classid' => $classid,
        );
        $this->assign('param', $param);
        //解析分页数据
        $this->_mkPagination($total, $param);

        $this->display();
    }

    /**
     * 课程详细页 - 播放视频
     * 用户点击视频播放按钮，AJAX请求服务器courseticket，courseticket的有效期为当前时间节点A至当前时间节点+视频总时长(秒)之后的时间节点B
     * courseticket的有效期控制在时间节点B 左右差60秒内
     * 用户暂停视频，AJAX请求通知服务器记录暂停时间节点pausetimestamp
     * 用户播放视频，AJAX请求通知服务器记录开始时间节点starttimestamp
     * 中间时间节点加到courseticket的有效期上面
     * 只有在courseticket的有效期内的Course/scomplete请求才是合法的
     * Course/scomplete接口的请求必须带coursesign参数
     * coursesign=md5(courseid+courseticket+timestamp)
     */
    public function profile()
    {
        $this->display();
    }

    //课程视频接口
    public function video()
    {
        echo HOST_PATH.'Upload/course/video/xuedangzhang_1207.mp4';
    }

    //课程已学习完 接口
    public function scomplete()
    {
        echo true;
    }
}