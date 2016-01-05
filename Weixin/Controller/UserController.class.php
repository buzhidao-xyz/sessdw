<?php
/**
 * Weixin Module 会员累
 * buzhidao
 */
namespace Weixin\Controller;

use Org\Util\Filter;

class UserController extends CommonController
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

    //检测如果已登录 则跳转到主页面
    private function _CKGotoIndex()
    {
        $userinfo = session('userinfo');
        if (is_array($userinfo) && !empty($userinfo)) {
            $this->_gotoIndex();
        }
    }

    //获取邮箱
    private function _getEmail()
    {
        $Email = mRequest('Email');
        if (!Filter::F_Email($Email)) {
            $this->ajaxReturn(1, "请输入真实有效的邮箱！");
        }

        return $Email;
    }

    /**
     * 获取密码 规则：字母数字开始 字母数字下划线!@#$% 长度5-20
     */
    private function _getPassword()
    {
        $Password = mRequest('Password');
        if (!Filter::F_Password($Password)) {
            $this->ajaxReturn(1, "密码输入有误！");
        }

        return $Password;
    }

    //获取确认密码
    private function _getPasswordc()
    {
        $Password = $this->_getPassword();
        $Passwordc = mRequest('Passwordc');
        if ($Passwordc != $Password) {
            $this->ajaxReturn(1, "确认密码输入有误！");
        }

        return $Passwordc;
    }

    //获取真实姓名
    private function _getName()
    {
        $Name = mRequest('Name');
        if (!Filter::F_Truename($Name)) {
            $this->ajaxReturn(1, "真实姓名输入有误！");
        }

        return $Name;
    }

    //获取性别
    private function _getGender()
    {
        $Gender = mRequest('Gender');
        return $Gender;
    }

    //获取验证码
    private function _CKVCode()
    {
        $vcode = mRequest('vcode');
        if (!CR('Org')->CKVcode($vcode)) {
            $this->ajaxReturn(1, "验证码错误！");
        }
        return true;
    }

    //检查微信用户是否已录入系统或已记录session
    private function _CKWXUser()
    {
        $WXUserBase = session('WXUserBase');
        //如果没有openid 申请授权 获取微信用户基本信息 openid等
        if (empty($WXUserBase) || (isset($WXUserBase['ExpireTime'])&&$WXUserBase['ExpireTime']<=TIMESTAMP)) {
            $WXUserBase = CR('Weixin')->getWXSNSUserBase();
            session('WXUserBase', $WXUserBase);
        }

        //根据openid查询用户信息
        $userInfo = D('User')->getUserByOpenID($WXUserBase['OpenID']);
        //如果openid未查到用户信息 申请授权 获取微信用户详细信息
        if (!is_array($userInfo)||empty($userInfo)) {
            $WXUserInfo = CR('Weixin')->getWXSNSUserInfo();
            session('WXUserInfo', $WXUserInfo);

            //记录微信用户信息入数据库
            $this->_saveWXUserInfo($WXUserInfo);
        }

        //如果查到用户信息 表示之前已经取得授权
        //如果已绑定到系统注册用户 直接登录成功
        if (isset($userInfo['OpenID'])&&$userInfo['OpenID']
            && isset($userInfo['UserID'])&&$userInfo['UserID']
            && isset($userInfo['AutoLogin'])&&$userInfo['AutoLogin']) {
            $this->_loginSuccess($userInfo);
        }
    }

    //保存微信用户 信息
    private function _saveWXUserInfo($WXUserInfo=array())
    {
        if (!is_array($WXUserInfo) || empty($WXUserInfo)) return false;

        $data = array(
            'OpenID'     => $WXUserInfo['OpenID'],
            'NickName'   => $WXUserInfo['NickName'],
            'Sex'        => $WXUserInfo['Sex'],
            'Province'   => $WXUserInfo['Province'],
            'City'       => $WXUserInfo['City'],
            'Country'    => $WXUserInfo['Country'],
            'Avatar'     => $WXUserInfo['Avatar'],
            'Privilege'  => $WXUserInfo['Privilege'],
            'Unionid'    => $WXUserInfo['Unionid'],
            'CreateTime' => date('Y-m-d H:i:s', TIMESTAMP),
            'UserID'     => '',
            'AutoLogin'  => 1,
        );
        $result = D('User')->_saveWXUserInfo($data);
        return $result ? true : false;
    }

    //用户登录 AJAX
    public function login()
    {
        //如果已登录 跳转到首页
        $this->_CKGotoIndex();

        //检查是否已记录过微信用户openid
        $this->_CKWXUser();

        $this->display();
    }

    //执行登录检查逻辑
    public function loginck()
    {
        $this->_CKGotoIndex();

        $Email = $this->_getEmail();
        $Password = $this->_getPassword();

        //根据Email获取用户信息
        $userInfo = D('User')->getUserByEmail($Email);
        if (!is_array($userInfo) || empty($userInfo) || D('User')->passwordEncrypt($Password)!=$userInfo['Password']) {
            $this->ajaxReturn(1, '邮箱或密码错误！');
        }

        $WXUserBase = session('WXUserBase');
        $userInfo['OpenID'] = $WXUserBase['OpenID'];
        $location = $this->_loginSuccess($userInfo);

        $this->ajaxReturn(0, '登录成功！', array(
            'location' => $location,
        ));
    }

    //学生登录 AJAX
    public function slogin()
    {
        //如果已登录 跳转到首页
        $this->_CKGotoIndex();

        //检查是否已记录过微信用户openid
        $this->_CKWXUser();

        $CollegeList = D('Org')->getCollege();
        $this->assign('CollegeList', $CollegeList['data']);

        $this->display();
    }

    //执行学生登录检查逻辑
    public function sloginck()
    {
        $this->_CKGotoIndex();

        $OrganizationID = mRequest('OrganizationID');
        if (!$OrganizationID) $this->ajaxReturn(1, '未知院校！');

        $Number = mRequest('Number');
        if (!$Number) $this->ajaxReturn(1, '未知学号！');

        $Password = $this->_getPassword();

        //根据院校、学号获取用户信息
        $userInfo = D('User')->getStudentByOrganizationIDAndNumber($OrganizationID, $Number);
        if (!is_array($userInfo) || empty($userInfo) || D('User')->passwordEncrypt($Password)!=$userInfo['Password']) {
            $this->ajaxReturn(1, '学号或密码错误！');
        }

        $WXUserBase = session('WXUserBase');
        $userInfo['OpenID'] = $WXUserBase['OpenID'];
        $location = $this->_loginSuccess($userInfo);

        $this->ajaxReturn(0, '登录成功！', array(
            'location' => $location,
        ));
    }

    //用户 执行登录成功
    private function _loginSuccess($userInfo=array())
    {
        $sessionUserInfo = array(
            'OpenID' => $userInfo['OpenID'],
            'UserID' => $userInfo['UserID'],
            'Email'  => $userInfo['Email'],
            'Name'   => $userInfo['Name'],
            'Gender' => $userInfo['Gender'],
            'RoleID' => $userInfo['RoleID'],
        );
        $this->GSUserinfo($sessionUserInfo);
        // session('userinfo', $sessionUserInfo);

        //关联WX_USER表并设置自动登录
        D('User')->linkWXUser($sessionUserInfo['OpenID'], $sessionUserInfo['UserID']);

        $location = session('location');
        !$location ? $location = __APP__.'?s=Index/index' : null;

        if (IS_AJAX) return $location;

        header('location:'.$location);
        session('location', null);
        exit;
    }

    //执行登录失败
    private function _loginFailed()
    {
        $this->_gotoLogin();
    }

    //退出
    public function logout()
    {
        D('User')->WXUserAutoLoginDisabled($this->userinfo['OpenID']);

        session('userinfo', null);
        session('location', null);

        // session_destroy();

        $this->_gotoLogin();
    }

    //注册用户
    public function regist()
    {
        //如果已登录 跳转到首页
        $this->_CKGotoIndex();

        //检查是否已记录过微信用户openid
        $this->_CKWXUser();

        $this->display();
    }

    //保存注册用户
    public function rsave()
    {
        $Email = $this->_getEmail();
        $Password = $this->_getPassword();
        $Passwordc = $this->_getPasswordc();
        $Name = $this->_getName();
        $Gender = $this->_getGender();

        //如果邮箱已存在 返回错误
        if (D('User')->CKEmailExist(null, $Email)) {
            $this->ajaxReturn(1, '邮箱已存在！');
        }

        //用户角色
        $UserRole = C('USER.Role');

        $Password = D('User')->passwordEncrypt($Password);
        $data = array(
            'Email' => $Email,
            'Name' => $Name,
            'Gender' => (int)$Gender,
            'Password' => $Password,
            'RoleID' => (int)$UserRole['Person']['id'],
            'FrozenStatus' => 1,
            'Status' => 1,
            'CreateDate' => date('Y-m-d H:i:s'),
        );
        //保存用户信息
        $UserID = D('User')->saveUser(null, $data);

        if ($UserID) {
            $WXUserBase = session('WXUserBase');

            //登录成功
            $location = $this->_loginSuccess(array(
                'OpenID' => $WXUserBase['OpenID'],
                'UserID' => $UserID,
                'Email'  => $data['Email'],
                'Name'   => $data['Name'],
                'Gender' => $data['Gender'],
                'RoleID' => $data['RoleID'],
            ));

            $this->ajaxReturn(0, '注册成功！', array(
                'location' => $location,
            ));
        } else {
            $this->ajaxReturn(1, '注册失败！');
        }
    }

    //企业用户
    public function company()
    {
        //如果已登录 跳转到首页
        $this->_CKGotoIndex();

        //检查是否已记录过微信用户openid
        // $this->_CKWXUser();
        
        $PositionCategoryList = D('Org')->getPositionCategory();
        $this->assign('PositionCategoryList', $PositionCategoryList['data']);

        $this->display();
    }

    //保存企业用户营业执照
    private function _uploadLicence()
    {
        $upload = new \Any\Upload();
        $upload->maxSize  = 204800; //200K
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = UPLOAD_PATH;
        $upload->savePath = 'Image/Licence/';
        // 上传文件
        $info = $upload->upload();
        if(!$info) {
            // 上传错误 提示错误信息
            $msg = $upload->getError();
            if (IS_AJAX) $this->ajaxReturn(1, $msg);
            return false;
        } else {
            // 上传成功 返回文件名
            $file = array_pop($info);
            return $file['savepath'].$file['savename'];
        }
    }

    //保存企业用户
    public function csave()
    {
        $PositionCategoryID = mRequest('PositionCategoryID');
        if (!$PositionCategoryID) $this->ajaxReturn(1, '请选择行业！');

        $ApplyOrgName = mRequest('ApplyOrgName');
        if (!$ApplyOrgName) $this->ajaxReturn(1, '请填写企业名称！');

        $Name = mRequest('Name');
        if (!$Name) $this->ajaxReturn(1, '请填写联系人！');

        $Gender = mRequest('Gender');

        $Phone = mRequest('Phone');
        if (!$Phone) $this->ajaxReturn(1, '请填写联系电话！');

        $Email = mRequest('Email');
        if (!$Email) $this->ajaxReturn(1, '请填写联系邮箱！');
        //如果邮箱已存在 返回错误
        if (D('User')->CKEmailExist(null, $Email)) {
            $this->ajaxReturn(1, '邮箱已存在！');
        }

        $LicenceFile = $this->_uploadLicence();
        if (!$LicenceFile) $this->ajaxReturn(1, '企业营业执照上传失败！');

        $data = array(
            'PositionCategoryID' => $PositionCategoryID,
            'ApplyOrgName' => $ApplyOrgName,
            'Name' => $Name,
            'Gender' => $Gender,
            'Phone' => $Phone,
            'Email' => $Email,
        );
        $OrganizationID = D('User')->saveOrganization($data);

        if ($OrganizationID) {
            if ($LicenceFile) {
                //保存企业营业执照
                $imgdata = array(
                    'RelativeID'    => $OrganizationID,
                    'ImageType'     => 4,
                    'ImgDetailType' => 1,
                    'ImgUrl'        => $LicenceFile,
                    'Status'        => 1,
                );
                D('Org')->saveImage($imgdata);
            }

            $location = __APP__.'?s=User/company';
            $this->ajaxReturn(0, '您的申请提交成功！请等待审核！', array(
                'location' => $location,
            ));
        } else {
            $this->ajaxReturn(1, '您的申请提交失败！');
        }
    }
}