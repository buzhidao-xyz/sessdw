<?php
/**
 * 支部数据模型
 * 2015-12-22
 * buzhidao
 */
namespace Front\Model;

class TszbModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取特色支部建设分类
    public function getTszbClass()
    {
        $result = M('tszb_class')->order('classid asc')->select();
        $data = array();
        if (is_array($result) && !empty($result)) {
            foreach ($result as $d) {
                if ($d['pclassid']) {
                    $data[$d['pclassid']]['subclass'][$d['classid']] = $d;
                } else {
                    $d['subclass'] = array();
                    $data[$d['classid']] = $d;
                }
            }
        }

        return $data;
    }

    //获取特色支部建设
    public function getTszbBuilt($builtid=null, $zhibuid=null, $zhibutype=null, $classid=null, $begintime=null, $endtime=null)
    {
        $where = array();
        if ($builtid) $where['a.builtid'] = $builtid;
        if ($zhibuid) $where['a.zhibuid'] = is_array($zhibuid) ? array('in', $zhibuid) : $zhibuid;
        if ($zhibutype) $where['d.zhibutype'] = $zhibutype;
        if ($classid) $where['a.classid'] = is_array($classid) ? array('in', $classid) : $classid;

        if ($begintime) $where['a.dt'] = array('egt', $begintime);
        if ($endtime) $where['a.dt'] = array('elt', $endtime);
        if ($begintime && $endtime) $where['a.dt'] = array('between', array($begintime, $endtime));

        $data = M('tszb_built')->alias('a')->field('a.*, d.zhibuname, d.desc as zhibudesc, d.type as zhibutype, d.icon as zhibuicon, c.classname, c.pclassid')
                               ->join(' __DANGZHIBU__ d on d.zhibuid=a.zhibuid ')
                               ->join(' __TSZB_CLASS__ c on c.classid=a.classid ')
                               ->where($where)
                               ->order('zhibuid, classid asc')
                               ->select();

        return is_array($data) ? $data : array();
    }
}