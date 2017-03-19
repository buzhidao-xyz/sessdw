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

    //获取课程类型
    public function getCourseType()
    {
        $where = array(
            'status' => 1,
        );
        $result = M('course_type')->where($where)->order('typeid asc')->select();
        $data = array();
        if (is_array($result) && !empty($result)) {
            foreach ($result as $d) {
                $data[$d['typeid']] = $d;
            }
        }

        return $data;
    }

    //获取课程分类
    public function getCourseClass()
    {
        $where = array(
            'status' => 1,
        );
        $result = M('course_class')->where($where)->order('classid asc')->select();
        $data = array();
        if (is_array($result) && !empty($result)) {
            foreach ($result as $d) {
                $data[$d['classid']] = $d;
            }
        }

        return $data;
    }

    //获取课程信息
    public function getCourse($courseid=null, $typeid=null, $classid=null, $userid=null, $start=0, $length=9999)
    {
        $where = array(
            'a.isshow' => 1,
        );
        if ($courseid) $where['a.courseid'] = $courseid;
        if ($typeid) $where['a.typeid'] = is_array($typeid) ? array('in', $typeid) : $typeid;
        if ($classid) $where['a.classid'] = is_array($classid) ? array('in', $classid) : $classid;

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
    public function getCourseByID($courseid=null, $userid=null)
    {
        if (!$courseid) return false;

        $datainfo = $this->getCourse($courseid, null, $userid);
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
    public function getPrevNextCourse($courseid=null, $typeid=null, $classid=null)
    {
        if ($courseid === null || (!$typeid && !$classid)) return false;

        $where = array(
            'a.isshow' => 1,
            'a.courseid' => array('LT', $courseid),
        );
        if ($typeid) $where['a.typeid'] = $typeid;
        if ($classid) $where['a.classid'] = $classid;
        $prevcourseinfo = M('course')->alias('a')->field('a.*, b.status, b.begintime, b.completetime')->join(' LEFT JOIN __USER_COURSE__ b ON a.courseid=b.courseid ')
                                 ->where($where)->order('a.courseid desc')->limit(0, 1)->find();

        $where = array(
            'a.isshow' => 1,
            'a.courseid' => array('GT', $courseid),
        );
        if ($typeid) $where['a.typeid'] = $typeid;
        if ($classid) $where['a.classid'] = $classid;
        $nextcourseinfo = M('course')->alias('a')->field('a.*, b.status, b.begintime, b.completetime')->join(' LEFT JOIN __USER_COURSE__ b ON a.courseid=b.courseid ')
                                 ->where($where)->order('a.courseid asc')->limit(0, 1)->find();

        return array(
            'prev' => is_array($prevcourseinfo) ? $prevcourseinfo : array(),
            'next' => is_array($nextcourseinfo) ? $nextcourseinfo : array(),
        );
    }

    public function getCLearnCourseid($userid=null, $typeid=null, $classid=null)
    {
        if (!$userid || (!$typeid && !$classid)) return false;

        $where = array();
        if ($typeid) $where['a.typeid'] = $typeid;
        if ($classid) $where['a.classid'] = $classid;
        $learnedcoursemax = M('course')->alias('a')->join(' __USER_COURSE__ b on a.courseid=b.courseid and b.userid='.$userid.' and b.status in (1,2) ')->where($where)->order('a.courseid desc')->limit(0,1)->find();
        $ccourseid = !empty($learnedcoursemax) ? $learnedcoursemax['courseid'] : 0;

        $where = array(
            'a.isshow' => 1,
            'a.courseid' => array('GT', $ccourseid),
        );
        if ($typeid) $where['a.typeid'] = $typeid;
        if ($classid) $where['a.classid'] = $classid;
        $nextcourseinfo = M('course')->alias('a')->field('a.*, b.status, b.begintime, b.completetime')->join(' LEFT JOIN __USER_COURSE__ b ON a.courseid=b.courseid and b.userid='.$userid)
                                 ->where($where)->order('a.courseid asc')->limit(0, 1)->find();

        return !empty($nextcourseinfo) ? $nextcourseinfo['courseid'] : ($ccourseid ? $ccourseid : 0);
    }

    //获取班级信息
    public function getCourseBan($banid=null)
    {
        $where = array();
        if ($banid) $where['banid'] = is_array($banid) ? array('in', $banid) : $banid;

        $result = M('course_ban')->where($where)->order('banid asc')->select();
        $data = array();
        if (is_array($result) && !empty($result)) {
            foreach ($result as $d) {
                $data[$d['banid']] = $d;
            }
        }

        return is_array($data) ? $data : array();
    }
}