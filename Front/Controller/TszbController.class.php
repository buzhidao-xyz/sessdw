<?php
/**
 * 特色支部建设
 * buzhidao
 * 2017-03-11
 */
namespace Front\Controller;

class TszbController extends CommonController
{
    //导航栏目navflag标识
    public $navflag = 'Tszb';

    //支部信息
    public $zhibu = array();

    //支部建设分类
    public $tszbclass = array();

    public function  __construct()
    {
        parent::__construct();

        $this->zhibu = D('Common')->getDangzhibu(null, 1);
        $this->assign('zhibu', $this->zhibu);

        $this->tszbclass = D('Tszb')->getTszbClass();
        $this->assign('tszbclass', $this->tszbclass);
    }

    //获取支部ID
    private function _getZhibuid()
    {
        $zhibuid = mRequest('zhibuid');

        $this->assign('zhibuid', $zhibuid);
        return $zhibuid;
    }

    //获取支部ID
    private function _getBuiltid()
    {
        $builtid = mRequest('builtid');

        $this->assign('builtid', $builtid);
        return $builtid;
    }

    //初始化特色支部信息
    private function _initTszb($zhibuid=null)
    {
        $zhibu = $zhibuid ? array($zhibuid=>$this->zhibu[$zhibuid]) : $this->zhibu;
        foreach ($zhibu as $k=>$d) {
            foreach ($this->tszbclass as $c) {
                if (!empty($c['subclass'])) {
                    foreach ($c['subclass'] as $cc) {
                        $cc['built'] = array();
                        $zhibu[$k]['tszb'][$cc['classid']] = $cc;
                    }
                } else {
                    $c['built'] = array();
                    $zhibu[$k]['tszb'][$c['classid']] = $c;
                }
            }
        }

        $tszb = array(
            'quarter1' => array(
                'title' => '第一季度',
                'begintime' => mktime(0,0,0,1,1,date('Y')),
                'endtime' => mktime(23,59,59,3,31,date('Y')),
                'zhibu' => $zhibu
            ),
            'quarter2' => array(
                'title' => '第二季度',
                'begintime' => mktime(0,0,0,4,1,date('Y')),
                'endtime' => mktime(23,59,59,6,30,date('Y')),
                'zhibu' => $zhibu
            ),
            'quarter3' => array(
                'title' => '第三季度',
                'begintime' => mktime(0,0,0,7,1,date('Y')),
                'endtime' => mktime(23,59,59,9,30,date('Y')),
                'zhibu' => $zhibu
            ),
            'quarter4' => array(
                'title' => '第四季度',
                'begintime' => mktime(0,0,0,10,1,date('Y')),
                'endtime' => mktime(23,59,59,12,31,date('Y')),
                'zhibu' => $zhibu
            )
        );

        return $tszb;
    }

    //主页
    public function index()
    {
        $zhibuid = $this->_getZhibuid();

        $tszb = $this->_initTszb($zhibuid);

        $tszbbuilt = D('Tszb')->getTszbBuilt(null, $zhibuid);
        if (is_array($tszbbuilt) && !empty($tszbbuilt)) {
            foreach ($tszbbuilt as $d) {
                for ($i=1; $i<=4; $i++) {
                    if ($d['dt']>=$tszb['quarter'.$i]['begintime'] && $d['dt']<=$tszb['quarter'.$i]['endtime']) {
                        $tszb['quarter'.$i]['zhibu'][$d['zhibuid']]['tszb'][$d['classid']]['built'][] = $d;
                    }
                }
            }
        }

        $this->assign('tszb', $tszb);
        $this->display();
    }

    //详情
    public function profile()
    {
        $builtid = $this->_getBuiltid();
        if (!$builtid) $this->_gotoIndex();

        $tszbbuilt = D('Tszb')->getTszbBuilt($builtid);
        if (empty($tszbbuilt)) $this->_gotoIndex();

        $this->assign('tszbbuilt', current($tszbbuilt));
        $this->display();
    }
}