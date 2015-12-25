<?php
/**
 * 系统入口逻辑控制
 * buzhidao
 * 2015-12-08
 */
namespace Front\Controller;

use Any\Controller;

class IndexController extends BaseController
{
    //导航栏目navflag标识
    public $navflag = 'Index';

    public function __construct()
    {
        parent::__construct();
    }

    //系统首页
    public function index()
    {
        //党建新闻
        $djarclist = $this->_getArc(1, 7);
        $this->assign('djarclist', $djarclist);

    	$this->display();
    }

    //获取新闻
    private function _getArc($classid=null)
    {
        $where = array(
            'classid' => $classid,
            'status' => 1,
        );
        $datalist = M('article')->where($where)->order('createtime desc')->limit(0,7)->select();

        return $datalist;
    }
}