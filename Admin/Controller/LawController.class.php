<?php
/**
 * 法治讲堂
 * buzhidao
 * 2015-12-27
 */
namespace Admin\Controller;

use Any\Upload;

use Org\Util\Filter;
use Org\Util\String;

class LawController extends CommonController
{
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

    public function __construct()
    {
        parent::__construct();

        $this->_page_location = __APP__ . '?s=Law/index';

        $this->assign('newstypes', $this->newstypes);
        $this->assign('inouts', $this->inouts);
    }

    //获取搜索关键字
    private function _getKeywords()
    {
        $keywords = mRequest('keywords');
        $this->assign('keywords', $keywords);

        return $keywords;
    }

    //上传封面图
    public function coverupload()
    {
        //初始化上传类
        $Upload = new Upload();
        $Upload->maxSize  = $this->_upfilesize['image']['size'];
        $Upload->exts     = $this->_upfilesize['image']['exts'];
        $Upload->rootPath = UPLOAD_PATH;
        $Upload->savePath = 'law/cover/';
        $Upload->saveName = array('uniqid', array('', true));
        $Upload->autoSub  = true;
        $Upload->subName  = array('date', 'Ym');

        //上传
        $error = null;
        $msg = '上传成功！';
        $data = array();
        $info = $Upload->upload();
        if (!$info) {
            $error = 1;
            $msg = $Upload->getError();
        } else {
            $fileinfo = array_shift($info);
            $data = array(
                'filepath' => '/'.UPLOAD_PT.$fileinfo['savepath'],
                'filename' => $fileinfo['savename'],
            );
        }

        $this->ajaxReturn($error, $msg, $data);
    }

    //上传缩略图
    public function thumbimgupload()
    {
        //初始化上传类
        $Upload = new Upload();
        $Upload->maxSize  = $this->_upfilesize['image']['size'];
        $Upload->exts     = $this->_upfilesize['image']['exts'];
        $Upload->rootPath = UPLOAD_PATH;
        $Upload->savePath = 'image/';
        $Upload->saveName = array('uniqid', array('', true));
        $Upload->autoSub  = true;
        $Upload->subName  = array('date', 'Ym');

        //上传
        $error = null;
        $msg = '上传成功！';
        $data = array();
        $info = $Upload->upload();
        if (!$info) {
            $error = 1;
            $msg = $Upload->getError();
        } else {
            $fileinfo = array_shift($info);
            $data = array(
                'filepath' => '/'.UPLOAD_PT.$fileinfo['savepath'],
                'filename' => $fileinfo['savename'],
            );
        }

        $this->ajaxReturn($error, $msg, $data);
    }

    public function index()
    {
        $keywords = $this->_getKeywords();

        list($start, $length) = $this->_mkPage();
        $data = D('Law')->getLawCourse(null, $keywords, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $param = array(
            'keywords'   => $keywords,
        );
        $this->assign('param', $param);
        $this->_mkPagination($total);

        $this->display();
    }

    //新增课程
    public function newcourse()
    {
        $this->display();
    }

    //新增课程保存
    public function newcoursesave()
    {
        $title = mRequest('title');
        if (!$title) $this->ajaxReturn(1, '请填写名称！');

        $link = mRequest('link');
        if (!$link) $this->ajaxReturn(1, '请填写链接！');

        $cover = mRequest('cover');
        if (!$cover) $this->ajaxReturn(1, '请上传封面图！');

        $data = array(
            'title' => $title,
            'desc' => $title,
            'cover' => $cover,
            'link' => $link,
            'createtime' => TIMESTAMP,
            'updatetime' => TIMESTAMP,
        );
        $result = M('law_course')->add($data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //编辑课程
    public function editcourse()
    {
        $lawcourseid = mRequest('lawcourseid');
        $this->assign('lawcourseid', $lawcourseid);

        $course = D('Law')->getLawCourseByID($lawcourseid);

        $this->assign('course', $course);
        $this->display();
    }

    //编辑课程保存
    public function editcoursesave()
    {
        $lawcourseid = mRequest('lawcourseid');
        if (!$lawcourseid) $this->ajaxReturn(1, '未知信息！');

        $title = mRequest('title');
        if (!$title) $this->ajaxReturn(1, '请填写名称！');

        $link = mRequest('link');
        if (!$link) $this->ajaxReturn(1, '请填写链接！');

        $cover = mRequest('cover');
        if (!$cover) $this->ajaxReturn(1, '请上传封面图！');

        $data = array(
            'title' => $title,
            'desc' => $title,
            'cover' => $cover,
            'link' => $link,
            'updatetime' => TIMESTAMP,
        );
        if ($cover) $data['cover'] = $cover;
        $result = M('law_course')->where(array('lawcourseid'=>$lawcourseid))->save($data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //删除课程
    public function deletecourse()
    {
        $lawcourseid = mRequest('lawcourseid');
        if (!$lawcourseid) $this->ajaxReturn(1, '未知信息！');

        $result = M('law_course')->where(array('lawcourseid'=>$lawcourseid))->delete();
        if ($result) {
            $this->ajaxReturn(0, '删除成功！');
        } else {
            $this->ajaxReturn(1, '删除失败！');
        }
    }

    //新闻
    public function news()
    {
        $keywords = $this->_getKeywords();

        $newstype = mRequest('newstype');
        $this->assign('newstype', $newstype);
        $inout = mRequest('inout');
        $this->assign('inout', $inout);

        list($start, $length) = $this->_mkPage();
        $data = D('Law')->getLawNews(null, $keywords, $newstype, $inout, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $param = array(
            'keywords'   => $keywords,
        );
        $this->assign('param', $param);
        $this->_mkPagination($total);

        $this->display();
    }

    //新增新闻
    public function newnews()
    {
        $this->display();
    }

    //编辑新闻
    public function editnews()
    {
        $newsid = mRequest('newsid');
        $this->assign('newsid', $newsid);

        $news = D('Law')->getLawNewsByID($newsid);

        $this->assign('news', $news);
        $this->display();
    }

    //新增新闻保存
    public function newssave()
    {
        $newsid = mRequest('newsid');

        $title = mRequest('title');
        if (!$title) $this->ajaxReturn(1, '请填写标题！');

        $desc = mRequest('desc');
        $newstype = mRequest('newstype');
        $inout = mRequest('inout');

        $thumbimg = mRequest('thumbimg');
        if (!$thumbimg) $this->ajaxReturn(1, '请上传缩略图！');

        $keyword = mRequest('keyword');

        $content = mRequest('content');
        if (!$content) $this->ajaxReturn(1, '请填写内容！');

        if ($newsid) {
            $msg = '编辑';
            $data = array(
                'title'    => $title,
                'desc'     => $desc,
                'newstype' => $newstype,
                'inout'    => $inout,
                'thumbimg' => $thumbimg,
                'keyword'  => $keyword,
                'content'  => $content,
                'updatetime' => TIMESTAMP
            );
            $newsid = M('law_news')->where(array('newsid'=>$newsid))->save($data);
        } else {
            $msg = '发布';
            $data = array(
                'title'    => $title,
                'desc'     => $desc,
                'newstype' => $newstype,
                'inout'    => $inout,
                'thumbimg' => $thumbimg,
                'keyword'  => $keyword,
                'content'  => $content,
                'clicknum' => 0,
                'status'   => 1,
                'createtime' => TIMESTAMP,
                'updatetime' => TIMESTAMP
            );
            $newsid = M('law_news')->add($data);
        }

        if ($newsid) {
            $this->ajaxReturn(0, '新闻'.$msg.'成功！');
        } else {
            $this->ajaxReturn(1, '新闻'.$msg.'失败！');
        }
    }

    //删除课程
    public function deletenews()
    {
        $newsid = mRequest('newsid');
        if (!$newsid) $this->ajaxReturn(1, '未知信息！');

        $result = M('law_news')->where(array('newsid'=>$newsid))->delete();
        if ($result) {
            $this->ajaxReturn(0, '删除成功！');
        } else {
            $this->ajaxReturn(1, '删除失败！');
        }
    }
}