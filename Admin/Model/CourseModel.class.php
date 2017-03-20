<?php
/**
 * 课程数据模型
 * 2015-12-22
 * buzhidao
 */
namespace Admin\Model;

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

    //保存复习资料信息
    public function saveReview($data=array(), $reviewid=null)
    {
        if (!is_array($data) || empty($data)) return false;

        if ($reviewid) {
            M('course_review')->where(array('courseid'=>$data['courseid']))->save(array('courseid'=>0));
            $result = M('course_review')->where(array('reviewid'=>$reviewid))->save($data);
            if (!$result) $reviewid = false;
        } else {
            $reviewid = M('course_review')->add($data);
        }
        return $reviewid ? $reviewid : false;
    }

    //获取课程
    public function getCourse($courseid=null, $typeid=null, $classid=null, $istesting=null, $keyword=null, $start=0, $length=9999)
    {
        $where = array();
        if ($courseid) $where['courseid'] = $courseid;
        if ($typeid) $where['typeid'] = $typeid;
        if ($classid) $where['classid'] = $classid;
        if ($istesting !== null) $where['istesting'] = $istesting;
        if ($keyword) $where['title'] = array('like', '%'.$keyword.'%');

        $count = M('course')->where($where)->count();
        $result = M('course')->where($where)->order('createtime desc')->limit($start, $length)->select();

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

    //保存课程
    public function saveCourse($courseid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($courseid) {
            $result = M('course')->where(array('courseid'=>$courseid))->save($data);
            $result = $result ? $courseid : false;
        } else {
            $courseid = M('course')->add($data);
        }

        return $courseid ? $courseid : false;
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
//                $user = $this->getCourseBanUser($d['banid']);
//                $d['user'] = $user;

                $data[$d['banid']] = $d;
            }
        }

        return is_array($data) ? $data : array();
    }

    //金鸡湖班用户
    public function getCourseBanUser($banid=null, $zhibuid=array())
    {
        if (!$banid) return false;

        //获取班级信息
        $ban = M('course_ban')->where(array('banid'=>$banid))->find();

        $where = array(
            'ucb.banid' => $banid
        );
        if ($zhibuid) $where['u.dangzhibu'] = is_array($zhibuid) ? array('in', $zhibuid) : $zhibuid;

        $result = M('user_course_ban')->alias('ucb')->field('ucb.userid, u.username, u.dangzhibu')
            ->join(' __USER__ u on ucb.userid=u.userid and u.status=1 ')
            ->where($where)
            ->order('userid asc')
            ->select();
        $data = array();
        if (is_array($result) && !empty($result)) {
            foreach ($result as $d) {
                $data[$d['userid']] = $d;
            }
        }

        return is_array($data) ? $data : array();
    }
}