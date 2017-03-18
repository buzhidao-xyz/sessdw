<?php
/**
 * 法治大讲堂
 * buzhidao
 * 2017-03-11
 */
namespace Front\Controller;

class LawController extends CommonController
{
    //导航栏目navflag标识
    public $navflag = 'Law';

    //法制文章类型
    public $newstypes = array(
        1 => array('id'=>1, 'title'=>'法制新闻'),
        2 => array('id'=>2, 'title'=>'法律法规'),
    );
    //内部外部
    protected $inouts = array(
        1 => array('id'=>1, 'title'=>'内部'),
        2 => array('id'=>2, 'title'=>'外部')
    );

    public function  __construct()
    {
        parent::__construct();

        $this->assign('newstypes', $this->newstypes);
        $this->assign('inouts', $this->inouts);
    }

    //主页
    public function index()
    {
        //最新法治课程
        $lawcourselatest = D('Law')->getLawCourseLatest();

        //法治课程
        $lawcourse = D('Law')->getLawCourse();
        $lawcourse = $lawcourse['data'];

        //法治新闻
        $lawnews1 = D('Law')->getLawNews(null, $this->newstype[1]['id'], null, 0, 6);
        //法律法规
        $lawnews2 = D('Law')->getLawNews(null, $this->newstype[2]['id'], null, 0, 7);

        $this->assign('lawcourselatest', $lawcourselatest);
        $this->assign('lawcourse', $lawcourse);
        $this->assign('lawnews1', $lawnews1['data']);
        $this->assign('lawnews2', $lawnews2['data']);
        $this->display();
    }

    //新闻
    public function news()
    {
        $newstype = mRequest('newstype');
        $this->assign('newstype', $newstype);

        list($start, $length) = $this->_mkPage();
        $arclist = D('Law')->getLawNews(null, $newstype, null, $start, $length);
        $total = $arclist['total'];
        $datalist = $arclist['data'];

        $this->assign('datalist', $datalist);

        $this->_mkPagination($total);
        $this->display();
    }

    //详情
    public function profile()
    {
        $newsid = mRequest('newsid');
        $this->assign('newsid', $newsid);

        if (!$newsid) $this->_gotoIndex();

        $newsinfo = D('Law')->getLawNewsByID($newsid);
        $this->assign('newsinfo', $newsinfo);

        //浏览量+1
        M('law_news')->where(array('newsid'=>$newsid))->save(array('clicknum'=>$newsinfo['clicknum']+1));

        //上一新闻、下一新闻
        $prevnextnews = D('Law')->getPrevNextLawNews($newsid, $newsinfo['newstype']);
        $this->assign('prevnextnews', $prevnextnews);

        $this->assign('newstype', $newsinfo['newstype']);
        $this->display();
    }
}