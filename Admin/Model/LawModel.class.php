<?php
/**
 * 法治讲堂
 * 2015-12-22
 * buzhidao
 */
namespace Admin\Model;

class LawModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取法治讲堂
    public function getLawCourse($lawcourseid=null, $keywords=null, $start=0, $length=9999)
    {
        $where = array();
        if ($lawcourseid) $where['lawcourseid'] = $lawcourseid;
        if ($keywords) $where['_complex'] = array(
            '_logic'  => 'or',
            'a.title' => array('like', '%'.$keywords.'%'),
            'a.desc'  => array('like', '%'.$keywords.'%'),
        );

        $count = M('law_course')->where($where)->count();
        $result = M('law_course')->where($where)->order('lawcourseid desc')->limit($start, $length)->select();

        return array('total'=>$count, 'data'=>is_array($result)?$result:array());
    }

    //获取法治讲堂BYID
    public function getLawCourseByID($lawcourseid=null)
    {
        if (!$lawcourseid) return false;

        $lawcourse = $this->getLawCourse($lawcourseid);

        return !empty($lawcourse['data']) ? current($lawcourse['data']) : array();
    }

    //获取法治新闻
    public function getLawNews($newsid=null, $keywords=null, $newstype=null, $inout=null, $start=0, $length=9999)
    {
        $where = array();
        if ($newsid) $where['newsid'] = $newsid;
        if ($keywords) $where['_complex'] = array(
            '_logic'  => 'or',
            'a.title' => array('like', '%'.$keywords.'%'),
            'a.desc'  => array('like', '%'.$keywords.'%'),
        );
        if ($newstype) $where['newstype'] = $newstype;
        if ($inout) $where['inout'] = $inout;

        $count = M('law_news')->where($where)->count();
        $result = M('law_news')->where($where)->order('newsid desc')->limit($start, $length)->select();

        return array('total'=>$count, 'data'=>is_array($result)?$result:array());
    }

    //获取法治新闻BYID
    public function getLawNewsByID($newsid=null)
    {
        if (!$newsid) return false;

        $news = $this->getLawNews($newsid);

        return !empty($news['data']) ? current($news['data']) : array();
    }
}