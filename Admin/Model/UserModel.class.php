<?php
/**
 * 会员数据模型
 * 2015-12-22
 * buzhidao
 */
namespace Admin\Model;

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

    //获取会员
    public function getUser($userid=null, $status=null, $keyword=null, $start=0, $length=9999)
    {
        $where = array();
        if ($userid) $where['userid'] = $userid;
        if ($status !== null) $where['status'] = $status;
        if ($keyword) $where['_complex'] = array(
            '_logic' => 'or',
            'account'  => array('like', '%'.$keyword.'%'),
            'username'   => array('like', '%'.$keyword.'%'),
        );

        $total = M('user')->where($where)->count();
        $result = M('user')->where($where)->order('createtime desc')->select();

        return array('total'=>$total, 'data'=>is_array($result)?$result:array());
    }

    //获取会员信息 通过ID
    public function getUserByID($userid=null)
    {
        if (!$userid) return false;

        $userinfo = $this->getUser($userid);

        return $userinfo['total'] ? $userinfo['data'][0] : array();
    }

    //保存会员信息
    public function usersave($userid=null, $data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        if ($userid) {
            $result = M('user')->where(array('userid'=>$userid))->save($data);
            $result = $result ? $userid : false;
        } else {
            $userid = M('user')->add($data);
        }

        return $userid ? $userid : false;
    }
}