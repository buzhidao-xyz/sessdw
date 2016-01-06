<?php
/**
 * 微信接口配置文件
 * buzhidao
 */

return array(
    //微信号
    'Account'        => 'sessdw',
    //应用ID
    'AppID'          => 'wx5dc76fc1b2f2b6cb',
    //应用密钥
    'AppSecret'      => '19417f7710d37f8e71714ccac228641d',
    //认证口令
    'Token'          => 'Szsamsungdw2016AWCVGHTK',
    //消息加密密钥
    'EncodingAESKey' => 'EyGpQEtN4j2XW8GplioQclxdRa1ZUqEqAzKBQm261KF',
    //微信API
    'API' => array(
        //获取access_token
        'GetAccessToken'    => 'https://api.weixin.qq.com/cgi-bin/token',
        //创建自定义菜单
        'CreateMenu'        => 'https://api.weixin.qq.com/cgi-bin/menu/create',
        //网页授权snsapi_base
        'SNSAPI_BASE'       => 'https://open.weixin.qq.com/connect/oauth2/authorize',
        //网页授权snsapi_userinfo
        'SNSAPI_USERINFO'   => 'https://open.weixin.qq.com/connect/oauth2/authorize',
        //网页获取access_token、openid等
        'Oauth2AccessToken' => 'https://api.weixin.qq.com/sns/oauth2/access_token',
        //网页获取用户详细信息
        'Oauth2User'        => 'https://api.weixin.qq.com/sns/userinfo',
    ),
    //自定义菜单
    'Menu' => array(
        'button' => array(
            array(
                'name' => '课程中心',
                'key'  => 'M1001_COURSE',
                'sub_button' => array(
                    array(
                        'type' => 'view',
                        'name' => '学党章',
                        'url'  => 'http://120.26.102.28/samsung/index.php?s=Course/rlist',
                    ),
                    array(
                        'type' => 'view',
                        'name' => '学讲话',
                        'url'  => 'http://120.26.102.28/samsung/index.php?s=Course/rview',
                    ),
                    array(
                        'type' => 'view',
                        'name' => '学条例',
                        'url'  => 'http://120.26.102.28/samsung/index.php?s=Course/company',
                    ),
                )
            ),
            array(
                'name' => '随堂测评',
                'key'  => 'M2001_TESTING',
                'sub_button' => array(
                    array(
                        'type' => 'view',
                        'name' => '全部职位',
                        'url'  => 'http://wx.szsecp.com/index.php?s=Job/jlist&jcate=all',
                    ),
                    array(
                        'type' => 'view',
                        'name' => '最热职位',
                        'url'  => 'http://wx.szsecp.com/index.php?s=Job/jlist&jcate=hot',
                    ),
                    array(
                        'type' => 'view',
                        'name' => '推荐职位',
                        'url'  => 'http://wx.szsecp.com/index.php?s=Job/jlist&jcate=recom',
                    )
                )
            ),
            array(
                'name' => '党员中心',
                'key'  => 'M3001_UCENTER',
                'sub_button' => array(
                    array(
                        'type' => 'view',
                        'name' => '学历类',
                        'url'  => 'http://wx.szsecp.com/index.php?s=Training/tlist&tcate=graduate',
                    ),
                    array(
                        'type' => 'view',
                        'name' => '技能类',
                        'url'  => 'http://wx.szsecp.com/index.php?s=Training/tlist&tcate=skill',
                    ),
                    array(
                        'type' => 'view',
                        'name' => '管理类',
                        'url'  => 'http://wx.szsecp.com/index.php?s=Training/tlist&tcate=manage',
                    ),
                    array(
                        'type' => 'view',
                        'name' => '认证类',
                        'url'  => 'http://wx.szsecp.com/index.php?s=Training/tlist&tcate=cert',
                    )
                )
            )
        )
    ),
);