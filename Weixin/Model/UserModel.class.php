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
}