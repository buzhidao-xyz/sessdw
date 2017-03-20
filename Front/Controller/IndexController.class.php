<?php
/**
 * 系统入口逻辑控制
 * buzhidao
 * 2015-12-08
 */
namespace Front\Controller;

class IndexController extends BaseController
{
    //导航栏目navflag标识
    public $navflag = 'Index';

    //支部信息
    public $zhibu = array();

    public function __construct()
    {
        parent::__construct();

        $this->zhibu = D('Common')->getDangzhibu(null, 1);
        $this->assign('zhibu', $this->zhibu);
    }

    //系统首页
    public function index()
    {
        //轮播图片
        $simglist = D('Advert')->getSimg();
        $this->assign('simglist', $simglist);

        //党建新闻
        $djarclist = D('Article')->getArc(null, CR('Article')->arcclass['news']['id'], null, 0, 6);
        $this->assign('djarclist', $djarclist['data']);

        //推荐文章-平台公告
        $recomarclist = D('Article')->getArc(null, CR('Article')->arcclass['notice']['id'], null, 0, 6);
        $this->assign('recomarclist', $recomarclist['data']);

        //获取课程总数
        $coursenum = D('Course')->getCoursenum();
        $this->assign('coursenum', $coursenum);

        //获取党员总数
        $usernum = D('User')->getUsernum();
        $this->assign('usernum', $usernum);

    	$this->display();
    }
}