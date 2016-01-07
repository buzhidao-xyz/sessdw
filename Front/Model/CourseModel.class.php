<?php
/**
 * 会员数据模型
 * 2015-12-22
 * buzhidao
 */
namespace Front\Model;

class CourseModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取课程信息
    public function getCourse($courseid=null, $classid=null, $userid=null, $start=0, $length=9999)
    {
        $where = array(
            'a.isshow' => 1,
        );
        if ($courseid) $where['a.courseid'] = $courseid;
        if ($classid) $where['a.classid'] = $classid;

        $count = M('course')->alias('a')->where($where)->count();
        if ($userid) {
            $result = M('course')->alias('a')->field('a.*, b.status, b.begintime, b.completetime')->join(' LEFT JOIN __USER_COURSE__ b ON a.courseid=b.courseid AND b.userid= '.$userid)
                                 ->where($where)->order('createtime desc')->limit($start, $length)->select();
        } else {
            $result = M('course')->alias('a')->field('a.*, b.status, b.begintime, b.completetime')->join(' LEFT JOIN __USER_COURSE__ b ON a.courseid=b.courseid ')
                                 ->where($where)->order('createtime desc')->limit($start, $length)->select();
        }

        return array('total'=>$count, 'data'=>is_array($result)?$result:array());
    }

    //获取课程详情
    public function getCourseByID($courseid=null)
    {
        if (!$courseid) return false;

        $datainfo = $this->getCourse($courseid);
        $courseinfo = $datainfo['total'] ? $datainfo['data'][0] : array();

        //获取复习资料
        if (!empty($courseinfo)) {
            $reviewinfo = M('course_review')->where(array('courseid'=>$courseinfo['courseid']))->find();
            $courseinfo['reviewinfo'] = is_array($reviewinfo)&&!empty($reviewinfo) ? $reviewinfo : array();
        }

        return $courseinfo;
    }

    //获取课程总数
    public function getCoursenum()
    {
        $where = array(
            'isshow' => 1,
        );
        $result = M('course')->where($where)->count();

        return $result>0 ? $result : 0;
    }

    //获取上一课程、下一课程
    public function getPrevNextCourse($courseid=null)
    {
        if ($courseid === null) return false;

        $where = array(
            'a.isshow' => 1,
            'a.courseid' => array('LT', $courseid),
        );
        $prevcourseinfo = M('course')->alias('a')->field('a.*, b.status, b.begintime, b.completetime')->join(' LEFT JOIN __USER_COURSE__ b ON a.courseid=b.courseid ')
                                 ->where($where)->order('a.courseid desc')->limit(0, 1)->find();

        $where = array(
            'a.isshow' => 1,
            'a.courseid' => array('GT', $courseid),
        );
        $nextcourseinfo = M('course')->alias('a')->field('a.*, b.status, b.begintime, b.completetime')->join(' LEFT JOIN __USER_COURSE__ b ON a.courseid=b.courseid ')
                                 ->where($where)->order('a.courseid asc')->limit(0, 1)->find();

        return array(
            'prev' => is_array($prevcourseinfo) ? $prevcourseinfo : array(),
            'next' => is_array($nextcourseinfo) ? $nextcourseinfo : array(),
        );
    }

    //获取党员已学习的课程 最大的课程id
    public function getLearnedCourseidMax($userid=null)
    {
        if (!$userid) return false;

        $result = M('user_course')->where(array('userid'=>$userid, 'status'=>array('in', array(1,2))))->order('courseid desc')->limit(0,1)->find();

        return is_array($result) ? $result : array();
    }
}