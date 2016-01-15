<?php
/**
 * 会员数据模型
 * 2015-12-22
 * buzhidao
 */
namespace Front\Model;

class UserModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //加密会员密码
    public function passwordEncrypt($password=null, $ukey=null)
    {
        return md5(md5($password).$ukey);
    }

    //获取党员信息
    public function getUser($userid=null, $account=null)
    {
        if (!$userid && !$account) return false;

        $where = array(
            'status' => 1,
        );
        if ($userid) $where['userid'] = $userid;
        if ($account) $where['account'] = $account;

        $result = M('user')->where($where)->find();

        return is_array($result) ? $result : array();
    }

    //获取党员总数
    public function getUsernum()
    {
        $where = array(
            'status' => 1,
        );
        $result = M('user')->where($where)->count();

        return $result>0 ? $result : 0;
    }

    //检查作业完成情况
    public function ckUserCourseWork($userid=null, $courseid=null)
    {
        if (!$userid) return false;

        //查出所有课程作业
        $where = array(
            'a.type' => 1
        );
        if ($courseid) $where['a.courseid'] = is_array($courseid) ? array('in', $courseid) : $courseid;
        $workcourse = M('work')->alias('a')->join(' left join __USER_WORK__ b on a.workid=b.workid and b.userid='.$userid.' and b.status=1 ')->field('a.*, b.userid, b.status, b.completetime')->where($where)->select();

        //筛选未完成的课程作业的课程id
        $courseids = array(0);
        $courseworkids = array();
        if (is_array($workcourse) && !empty($workcourse)) {
            foreach ($workcourse as $workinfo) {
                if ($workinfo['userid']&&$workinfo['status']) continue;

                $courseids[] = $workinfo['courseid'];
                $courseworkids[$workinfo['courseid']][] = $workinfo['workid'];
            }
        }

        //检查课程是否已完成(已学习并完成测评)
        $donecourse = M('user_course')->where(array('userid'=>$userid, 'courseid'=>array('in', $courseids), 'status'=>2))->select();
        if (is_array($donecourse) && !empty($donecourse)) {
            $data = array();
            foreach ($donecourse as $courseinfo) {
                foreach ($courseworkids[$courseinfo['courseid']] as $workid) {
                    $data[] = array(
                        'userid' => $userid,
                        'workid' => $workid,
                        'status' => 1,
                        'completetime' => $courseinfo['completetime'],
                    );
                }
            }

            //新增作业完成记录
            if (!empty($data)) M('user_work')->addAll($data);
        }

        return true;
    }
}