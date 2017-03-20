<?php
/**
 * 支部建设模型
 * 2015-12-22
 * buzhidao
 */
namespace Admin\Model;

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

    //获取特色支部
    public function getTszb($zhibuid=null, $keywords=null, $start=0, $length=9999)
    {
        $where = array();
        if ($zhibuid) $where['zhibuid'] = $zhibuid;
        if ($keywords) $where['_complex'] = array(
            '_logic'    => 'or',
            'zhibuname'   => array('like', '%'.$keywords.'%'),
            'desc' => array('like', '%'.$keywords.'%'),
        );

        $total = M('dangzhibu')->where($where)->count();
        $result = M('dangzhibu')->where($where)->order('zhibuid asc')->select();
        $data = array();
        if (is_array($result)&&!empty($result)) {
            foreach ($result as $d) {
                $data[$d['zhibuid']] = $d;
            }
        }

        return array('total'=>$total, 'data'=>$data);
    }

    //获取特色支部
    public function getTszbByID($zhibuid=null)
    {
        if (!$zhibuid) return false;

        $zhibuinfo = $this->getZhibu($zhibuid);

        return $zhibuinfo['total'] ? array_shift($zhibuinfo['data']) : array();
    }

    //获取特色支部会议
    public function getTszbBuilt($builtid=null, $zhibuid=null, $keywords=null, $classid=null, $start=0, $length=9999)
    {
        $where = array();
        if ($builtid) $where['a.builtid'] = $builtid;
        if ($zhibuid) $where['a.zhibuid'] = $zhibuid;
        if ($keywords) $where['_complex'] = array(
            '_logic'    => 'or',
            'a.title'   => array('like', '%'.$keywords.'%'),
            'a.shorttitle' => array('like', '%'.$keywords.'%'),
            'a.content' => array('like', '%'.$keywords.'%'),
        );
        if ($classid) $where['a.classid'] = $classid;

        $total = M('tszb_built')->alias('a')->where($where)->count();
        $data = M('tszb_built')->alias('a')->field('a.*, b.classname')->join('__TSZB_CLASS__ b on a.classid=b.classid')->where($where)->order('zhibuid,dt desc')->select();

        return array('total'=>$total, 'data'=>$data);
    }
}