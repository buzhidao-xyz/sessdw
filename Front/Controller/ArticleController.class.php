<?php
/**
 * 文章模型逻辑控制
 * buzhidao
 * 2015-12-14
 */
namespace Front\Controller;

use Any\Controller;

class ArticleController extends BaseController
{
    //导航栏目navflag标识
    public $navflag = 'Index';

    public function __construct()
    {
        parent::__construct();
    }

    //文章模型入口
    public function index(){}

    //获取arcid
    private function _getArcid()
    {
        $arcid = mRequest('arcid');

        return $arcid;
    }

    //新闻
    public function news()
    {
        $this->assign("resumenavflag", "news");

        $arcid = $this->_getArcid();

        if ($arcid) {
            $this->_newsprofile($arcid);
        } else {
            $this->_newsindex();
        }
    }

    //新闻列表
    private function _newsindex()
    {
        $where = array(
            'classid' => 1,
            'status' => 1,
        );
        $datalist = M('article')->where($where)->order('createtime desc')->limit(0,15)->select();
        
        $this->assign('datalist', $datalist);
        $this->display('Article/news_index');
    }
    
    //新闻内容
    private function _newsprofile($arcid=null)
    {
        $where = array(
            'arcid' => $arcid
        );
        $arcprofile = M('article')->where($where)->find();
        
        $this->assign('arcprofile', $arcprofile);
        $this->display('Article/news_profile');
    }

    //公告
    public function notice()
    {
        $this->assign("resumenavflag", "notice");
        
        $arcid = $this->_getArcid();

        if ($arcid) {
            $this->_noticeprofile($arcid);
        } else {
            $this->_noticeindex();
        }
    }

    //公告列表
    private function _noticeindex()
    {
        $this->display('Article/notice_index');
    }
    
    //公告内容
    private function _noticeprofile($arcid=null)
    {
        $this->display('Article/notice_profile');
    }
}