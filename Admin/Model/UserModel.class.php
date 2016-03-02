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
        $result = M('user')->where($where)->order('createtime desc')->limit($start, $length)->select();

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

    //获取党支部信息
    public function getDangzhibu($zhibuid=null)
    {
        $where = array();
        if ($zhibuid) $where['zhibuid'] = $zhibuid;

        $total = M('dangzhibu')->where($where)->count();
        $result = M('dangzhibu')->where($where)->order('zhibuid asc')->select();
        $data = array();
        if (is_array($result)&&!empty($result)) {
            foreach ($result as $d) {
                $data[$d['zhibuid']] = array(
                    'zhibuid' => $d['zhibuid'],
                    'zhibuname' => $d['zhibuname']
                );
            }
        }

        return array('total'=>$total, 'data'=>$data);
    }

    public function getDangzhibuByID($zhibuid=null)
    {
        if (!$zhibuid) return false;

        $zhibuinfo = $this->getDangzhibu($zhibuid);

        return $zhibuinfo['total'] ? array_shift($zhibuinfo['data']) : array();
    }

    //获取反馈留言
    public function getLvword($keyword=null, $start=0, $length=9999)
    {
        $where = array();
        if ($keyword) $where['_complex'] = array(
            '_logic'    => 'or',
            'a.title'   => array('like', '%'.$keyword.'%'),
            'a.content' => array('like', '%'.$keyword.'%'),
        );

        $total = M('lvword')->alias('a')->where($where)->count();
        $result = M('lvword')->alias('a')->field('a.*, b.account, b.username, b.department, b.position')->join(' left join __USER__ b on a.userid=b.userid ')->where($where)->order('a.createtime desc')->select();

        return array('total'=>$total, 'data'=>is_array($result)?$result:array());
    }
}