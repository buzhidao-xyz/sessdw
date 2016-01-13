<?php
/**
 * 用户数据模型
 * buzhidao
 */
namespace Weixin\Model;

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

    //查询用户信息 根据openid
    public function getUserByOpenID($openid=null)
    {
        if (!$openid) return false;

        $where = array(
            'openid' => $openid,
        );
        $wxuserinfo = M('wx_user')->where($where)->find();

        $userinfo = $wxuserinfo;
        if (is_array($userinfo) && !empty($userinfo)) {
            //获取系统用户信息 通过UserID
            $suserinfo = $this->getUser($userinfo['UserID']);
            if (is_array($suserinfo)&&!empty($suserinfo)) $userinfo = array_merge($userinfo, $suserinfo);
        }

        return is_array($userinfo) ? $userinfo : array();
    }

    //保存微信用户信息
    public function saveWXUserInfo($data=array())
    {
        if (!is_array($data)||empty($data)) return false;

        $result = M('wx_user')->add($data);

        return $result ? true : false;
    }

    //关联微信用户并设置自动登录
    public function linkWXUser($openid=null, $userid=null)
    {
        if (!$openid || !$userid) return false;

        $result = M('wx_user')->where(array('openid'=>$openid))->save(array(
            'userid'    => $userid,
            'autologin' => 1,
        ));

        return $result ? true : false;
    }

    //取消微信用户自动登录
    public function WXUserAutoLoginDisabled($openid=null)
    {
        if (!$openid) return false;

        $result = M('wx_user')->where(array('openid'=>$openid))->save(array(
            'autologin' => 0
        ));

        return $result ? true : false;
    }

    //开启微信用户自动登录
    public function WXUserAutoLoginEnabled($openid=null)
    {
        if (!$openid) return false;

        $result = M('wx_user')->where(array('openid'=>$openid))->save(array(
            'autologin' => 1
        ));

        return $result ? true : false;
    }

    //统计课程学习情况
    public function gcUserCourseLearn($userid=null, $courseclass=array())
    {
        if (!$userid || !is_array($courseclass) || empty($courseclass)) return false;

        //课程学习情况
        $usercourselearninfo = array();
        
        //课程试卷完成情况 按分类统计
        foreach ($courseclass as $classinfo) {
            $subquery = M('testing')->alias('a')->join(' __COURSE__ b on a.courseid=b.courseid and b.isshow=1 and b.classid='.$classinfo['id'])->field('a.*, b.title, b.classid')->where(array('a.status'=>1))->buildSql();
            //该分类总课程数
            $coursetotalnum = M('testing')->table($subquery.' sub')->count();
            //已学习课程数
            $courselearnnum = M('user_course')->alias('uc')->join(' inner join '.$subquery.' sub on uc.courseid=sub.courseid ')->where(array('uc.userid'=>$userid, 'uc.status'=>array('in', array(1,2))))->count();
            //课程测评总得分数
            $totalscore = M('user_testing')->alias('ut')->join(' inner join '.$subquery.' sub on ut.testingid=sub.testingid ')->where(array('ut.userid'=>$userid))->sum('ut.gotscore');
            $totalscore = $totalscore>0 ? $totalscore : 0;

            //计算该类平均分
            $avgscore = $coursetotalnum>0 ? floor($totalscore/$coursetotalnum) : 0;
            //计算权重分
            $weightscore = floor($avgscore*$classinfo['weight']);
            
            $usercourselearninfo[$classinfo['id']] = array(
                'coursetotalnum' => $coursetotalnum,
                'courselearnnum' => $courselearnnum,
                'totalscore'     => $totalscore,
                'avgscore'       => $avgscore,
                'weightscore'    => $weightscore,
            );
        }

        //合计
        

        return $usercourselearninfo;
    }

    //获取用户学习课程的学习经历
    public function getUserCourse($userid=null, $courseid=null, $start=0, $length=9999)
    {
        if (!$userid) return false;

        $where = array(
            'a.userid' => $userid,
        );
        if ($courseid) $where['a.courseid'] = $courseid;

        $total = M('user_course')->alias('a')->join(' __COURSE__ b on a.courseid=b.courseid and b.isshow=1 ')->where($where)->count();
        $result = M('user_course')->alias('a')->join(' __COURSE__ b on a.courseid=b.courseid and b.isshow=1 ')->field('a.*, b.title, b.classid')->where($where)->order('a.completetime desc')->limit($start, $length)->select();

        return array('total'=>$total, 'data'=>is_array($result)?$result:array());
    }
}