<?php
/**
 * 基类模型
 * 2015-07-12
 * buzhidao
 */
namespace Front\Model;

use Any\Model;

class BaseModel extends Model
{
    //Config处理函数数组
    protected $cfgfuncs = array(
        'headerslogan' => 'configFenhao',
    );

    public function __construct()
    {
//        parent::__construct();
    }

    //获取配置信息Config
    public function getConfig($cfgkey=null, $cfggroup=null)
    {
        $where = array();
        if ($cfgkey) $where['cfgkey'] = $cfgkey;
        if ($cfggroup) $where['cfggroup'] = $cfggroup;

        $data = M('config')->where($where)->select();
        $config = array();
        if (is_array($data) && !empty($data)) {
            foreach ($data as $d) {
                $d['cfgvalues'] = $this->{$this->cfgfuncs[$d['cfgkey']]}($d['cfgvalue']);

                $config[$d['cfgkey']] = $d;
            }
        }

        return is_array($config) ? $config : array();
    }

    //config分号处理
    private function configFenhao($cfgvalue=null)
    {
        if (!$cfgvalue) return false;

        $cfgvalues = explode(';', $cfgvalue);

        return $cfgvalues;
    }

    //获取党支部列表
    public function getDangzhibu($zhibuid=null, $type=null)
    {
        $where = array();
        if ($zhibuid) $where['zhibuid'] = $zhibuid;
        if ($type) $where['type'] = $type;

        $data = M('dangzhibu')->where($where)->order('zhibuid asc')->select();
        $zhibu = array();
        if (is_array($data) && !empty($data)) {
            foreach ($data as $d) {
                $zhibu[$d['zhibuid']] = $d;
            }
        }

        return $zhibu;
    }

    //获取友情链接
    public function getFlink()
    {
        $data = M('flink')->order('flinkid asc')->select();

        return $data;
    }
}