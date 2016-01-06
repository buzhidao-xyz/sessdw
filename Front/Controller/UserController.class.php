<?php
/**
 * 用户模块逻辑控制
 * buzhidao
 * 2015-12-08
 */
namespace Front\Controller;

use Org\Util\Filter;
use Org\Util\String;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){}

    //获取账号
    private function _getAccount()
    {
        $account = mRequest('account');
        if (!Filter::F_Account($account)) $this->ajaxReturn(1, '账号或密码错误！');

        return $account;
    }

    //获取密码
    private function _getPassword()
    {
        $password = mRequest('password');
        if (!Filter::F_Password($password)) $this->ajaxReturn(1, '账号或密码错误！');

        return $password;
    }

    //登录
    public function login()
    {

    }

    //登录请求检查
    public function logincheck()
    {
        $account = $this->_getAccount();
        $password = $this->_getPassword();

        $userinfo = D('User')->getUser(null, $account);
        //登录失败
        if (!$userinfo || !is_array($userinfo) || empty($userinfo) || $userinfo['password']!=D('User')->passwordEncrypt($password,$userinfo['ukey'])) {
            $this->ajaxReturn(1, '账号或密码错误！');
        }

        $userid = $userinfo['userid'];

        //更新用户登录信息
        $ip = get_client_ip(0, true);
        M('user')->where(array('userid'=>$userid))->save(array(
            'lastlogintime' => TIMESTAMP,
            'lastloginip'   => $ip,
            'loginnum'      => $userinfo['loginnum']+1,
        ));

        //登录用户信息
        $userinfo = array(
            'userid'     => $userid,
            'account'    => $userinfo['account'],
            'username'   => $userinfo['username'],
            'department' => $userinfo['department'],
            'position'   => $userinfo['position'],
        );
        $this->_GSUserinfo($userinfo);

        $location = $this->_gotoIndex(false);
        $this->ajaxReturn(0, '', array(
            'location' => $location
        ));
    }

    //退出
    public function logout()
    {
        $this->_USUserinfo();

        $this->_gotoIndex();
    }

    //个人中心
    public function home()
    {
        $this->_CKUserLogon();
        $this->assign("homemenuflag", "home");

        $this->display();
    }

    //修改密码
    public function chpasswd()
    {
        $this->_CKUserLogon();
        $this->assign("homemenuflag", "chpasswd");

        $this->display();
    }

    //反馈留言
    public function lvword()
    {
        $this->_CKUserLogon();
        $this->assign("homemenuflag", "lvword");

        $this->display();
    }
}