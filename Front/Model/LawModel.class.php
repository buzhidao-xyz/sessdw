<?php
/**
 * 法律数据模型
 * 2015-12-22
 * buzhidao
 */
namespace Front\Model;

class LawModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取法治课程-最新一条
    public function getLawCourseLatest()
    {
        $result = M('law_course')->order('createtime desc')->find();

        return is_array($result) ? $result : array();
    }

    //获取法治课程
    public function getLawCourse($lawcourseid=null, $start=0, $length=9999)
    {
        $where = array();
        if ($lawcourseid) $where['lawcourseid'] = $lawcourseid;

        $count = M('law_course')->where($where)->count();
        $result = M('law_course')->where($where)->order('createtime desc')->limit($start, $length)->select();

        return array('total'=>$count, 'data'=>is_array($result)?$result:array());
    }

    //获取法制新闻、法律法规
    public function getLawNews($newsid=null, $newstype=null, $inout=null, $start=0, $length=9999)
    {
        $where = array(
            'status' => 1,
        );
        if ($newsid) $where['newsid'] = $newsid;
        if ($newstype) $where['newstype'] = is_array($newstype) ? array('in', $newstype) : $newstype;
        if ($inout) $where['inout'] = is_array($inout) ? array('in', $inout) : $inout;

        $count = M('law_news')->where($where)->count();
        $result = M('law_news')->where($where)->order('createtime desc')->limit($start, $length)->select();

        return array('total'=>$count, 'data'=>is_array($result)?$result:array());
    }

    //获取文章详情
    public function getLawNewsByID($newsid=null)
    {
        if (!$newsid) return false;

        $newsinfo = $this->getLawNews($newsid);

        return $newsinfo['total'] ? $newsinfo['data'][0] : array();
    }

    //获取上一新闻、下一新闻
    public function getPrevNextLawNews($newsid=null, $newstype=null)
    {
        if ($newsid === null || !$newstype) return false;

        $where = array(
            'a.status'  => 1,
            'a.newsid'   => array('LT', $newsid),
            'a.newstype' => $newstype,
        );
        $prevarcinfo = M('law_news')->alias('a')->field('a.*')->where($where)->order('a.newsid desc')->limit(0, 1)->find();

        $where = array(
            'a.status'  => 1,
            'a.newsid'   => array('GT', $newsid),
            'a.newstype' => $newstype,
        );
        $nextarcinfo = M('law_news')->alias('a')->field('a.*')->where($where)->order('a.newsid asc')->limit(0, 1)->find();

        return array(
            'prev' => is_array($prevarcinfo) ? $prevarcinfo : array(),
            'next' => is_array($nextarcinfo) ? $nextarcinfo : array(),
        );
    }
}