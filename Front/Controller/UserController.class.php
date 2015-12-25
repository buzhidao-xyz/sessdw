<?php
/**
 * 用户模块逻辑控制
 * buzhidao
 * 2015-12-08
 */
namespace Front\Controller;

use Any\Controller;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){}

    //登录
    public function login()
    {

    }

    //登录请求检查
    public function logincheck()
    {
        //登录用户信息
        $userinfo = array(
            'userid' => 1,
            'account' => 'test',
            'username' => '步知道',
        );
        $this->_GSUserinfo($userinfo);

        $this->_gotoIndex();
    }

    //退出
    public function logout()
    {
        session('userinfo',null);

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