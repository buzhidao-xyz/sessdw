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
        
    ),
);