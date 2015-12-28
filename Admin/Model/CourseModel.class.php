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

    //保存复习资料信息
    public function saveReview($data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        $reviewid = M('course_review')->add($data);
        return $reviewid ? $reviewid : false;
    }

    //获取课程
    public function getCourse($courseid=null, $classid=null, $keyword=null, $start=0, $length=9999)
    {
        $where = array();
        if ($courseid) $where['courseid'] = $courseid;
        if ($classid) $where['classid'] = $classid;
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

        return $datainfo['total'] ? $datainfo['data'][0] : array();
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
}