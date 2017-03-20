<?php
/**
 * 友情链接
 * 2015-12-22
 * buzhidao
 */
namespace Admin\Model;

class FlinkModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取友情链接
    public function getFlink($flinkid=null, $start=0, $length=9999)
    {
        $where = array();
        if ($flinkid) $where['flinkid'] = $flinkid;

        $count = M('flink')->where($where)->count();
        $result = M('flink')->where($where)->order('flinkid desc')->limit($start, $length)->select();

        return array('total'=>$count, 'data'=>is_array($result)?$result:array());
    }

    //获取友情链接BYID
    public function getFlinkByID($flinkid=null)
    {
        if (!$flinkid) return false;

        $flink = $this->getFlink($flinkid);

        return !empty($flink['data']) ? current($flink['data']) : array();
    }
}