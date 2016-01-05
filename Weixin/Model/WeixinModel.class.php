<?php
/**
 * 微信服务数据模型
 * buzhidao
 */
namespace Weixin\Model;

class WeixinModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //读取微信服务信息
    public function getServiceByAccount($account=null)
    {
        if (!$account) return false;

        $where = array(
            'ServiceAccount' => $account,
        );
        $result = M('WX_Service')->where($where)->find();

        return is_array($result)&&!empty($result) ? $result : array();
    }

    //设置微信服务信息 - access_token
    public function setServiceAccessTokenByAccount($account=null, $accesstoken=null, $expiretime=0)
    {
        if (!$account || !$accesstoken) return false;

        $where = array(
            'ServiceAccount' => $account,
        );
        $result = M('WX_Service')->where($where)->save(array(
            'AccessToken' => $accesstoken,
            'ExpireTime' => $expiretime,
        ));

        return $result ? true : false;
    }
}