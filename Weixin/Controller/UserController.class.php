<?php
/**
 * Weixin Module 会员累
 * buzhidao
 */
namespace Weixin\Controller;

use Org\Util\Filter;

class UserController extends BaseController
{
    //登录结果
    private $_loginlog_result = array(
        'FAILED'  => 0,
        'SUCCESS' => 1,
    );

    //对象初始化
    public function __construct()
    {
        parent::__construct();
    }

    //检测如果已登录 则跳转到home页
    private function _CKGotoHome()
    {
        if (is_array($this->userinfo) && !empty($this->userinfo)) {
            header('location:'.__APP__.'?s=User/home');
            exit;
        }
    }

    //获取账号
    private function _getAccount()
    {
        $account = mRequest('account');
        if (!Filter::F_Account($account)) {
            $this->ajaxReturn(1, "账号或密码错误！");
        }

        return $account;
    }

    /**
     * 获取密码 规则：字母数字开始 字母数字下划线!@#$% 长度5-20
     */
    private function _getPassword()
    {
        $password = mRequest('password');
        if (!Filter::F_Password($password)) {
            $this->ajaxReturn(1, "账号或密码错误！");
        }

        return $password;
    }

    //获取用户姓名
    private function _getUsername()
    {
        $username = mRequest('username');
        if (!Filter::F_Truename($username)) {
            $this->ajaxReturn(1, "用户姓名输入有误！");
        }

        return $username;
    }

    //检查微信用户是否已录入系统或已记录session
    private function _CKWXUser()
    {
        $WXUserBase = session('WXUserBase');
        //如果没有openid 申请授权 获取微信用户基本信息 openid等
        if (empty($WXUserBase) || (isset($WXUserBase['expiretime'])&&$WXUserBase['expiretime']<=TIMESTAMP)) {
            $WXUserBase = CR('Weixin')->getWXSNSUserBase();
            session('WXUserBase', $WXUserBase);
        }

        //根据openid查询用户信息
        $userInfo = D('User')->getUserByOpenID($WXUserBase['openid']);
        //如果openid未查到用户信息 申请授权 获取微信用户详细信息
        if (!is_array($userInfo)||empty($userInfo)) {
            $WXUserInfo = CR('Weixin')->getWXSNSUserInfo();
            session('WXUserInfo', $WXUserInfo);

            //记录微信用户信息入数据库
            $this->_saveWXUserInfo($WXUserInfo);
        }

        //如果查到用户信息 表示之前已经取得授权
        //如果已绑定到系统注册用户 直接登录成功
        if (isset($userInfo['openid'])&&$userInfo['openid']
            && isset($userInfo['userid'])&&$userInfo['userid']
            && isset($userInfo['autologin'])&&$userInfo['autologin']) {
            $this->_loginSuccess($userInfo);
        }
    }

    //保存微信用户 信息
    private function _saveWXUserInfo($WXUserInfo=array())
    {
        if (!is_array($WXUserInfo) || empty($WXUserInfo)) return false;

        $data = array(
            'openid'     => $WXUserInfo['openid'],
            'nickname'   => $WXUserInfo['nickname'],
            'sex'        => $WXUserInfo['sex'],
            'province'   => $WXUserInfo['province'],
            'city'       => $WXUserInfo['city'],
            'country'    => $WXUserInfo['country'],
            'avatar'     => $WXUserInfo['avatar'],
            'privilege'  => $WXUserInfo['privilege'],
            'unionid'    => $WXUserInfo['unionid'],
            'createtime' => TIMESTAMP,
            'userid'     => '',
            'autologin'  => 1,
        );
        $result = D('User')->saveWXUserInfo($data);
        return $result ? true : false;
    }

    //用户登录 AJAX
    public function login()
    {
        $this->_CKGotoHome();

        //检查是否已记录过微信用户openid
        // $this->_CKWXUser();

        $this->display();
    }

    //执行登录检查逻辑
    public function logincheck()
    {
        $this->_CKGotoHome();

        $account = $this->_getAccount();
        $password = $this->_getPassword();

        //获取用户信息
        $userInfo = D('User')->getUser(null, $account);
        if (!is_array($userInfo) || empty($userInfo) || D('User')->passwordEncrypt($password,$userInfo['ukey'])!=$userInfo['password']) {
            $this->ajaxReturn(1, '账号或密码错误！');
        }

        // $WXUserBase = session('WXUserBase');
        // $userInfo['openid'] = $WXUserBase['openid'];
        $location = $this->_loginSuccess($userInfo);

        $this->ajaxReturn(0, '登录成功！', array(
            'location' => $location,
        ));
    }

    //用户 执行登录成功
    private function _loginSuccess($userInfo=array())
    {
        $sessionUserInfo = array(
            // 'openid'   => $userInfo['openid'],
            'userid'   => $userInfo['userid'],
            'account'  => $userInfo['account'],
            'username' => $userInfo['username'],
        );
        $this->_GSUserinfo($sessionUserInfo);

        //关联WX_USER表并设置自动登录
        // D('User')->linkWXUser($sessionUserInfo['openid'], $sessionUserInfo['userid']);

        $location = session('location');
        !$location ? $location = __APP__.'?s=Index/index' : null;

        if (IS_AJAX) return $location;

        header('location:'.$location);
        session('location', null);
        exit;
    }

    //退出
    public function logout()
    {
        //注销微信用户自动登录
        // D('User')->WXUserAutoLoginDisabled($this->userinfo['openid']);
        //注销登录用户信息
        $this->_USUserinfo();

        $this->_gotoLogin();
    }

    //个人中心
    public function home()
    {
        //记录location
        $this->_setLocation();
        //检查登录
        $this->_CKUserLogon();
        
        $this->display();
    }
}