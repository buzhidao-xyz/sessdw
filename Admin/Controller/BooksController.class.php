<?php
/**
 * 电子书
 * buzhidao
 * 2015-12-27
 */
namespace Admin\Controller;

use Any\Upload;

use Org\Util\Filter;
use Org\Util\String;

class BooksController extends CommonController
{
    //电子书类型
    public $booktypes = array(
        1 => array('id'=>1, 'title'=>'三星电子党刊'),
        2 => array('id'=>2, 'title'=>'外链电子图书'),
    );

    public function __construct()
    {
        parent::__construct();

        $this->_page_location = __APP__ . '?s=Books/index';

        $this->assign('booktypes', $this->booktypes);

        $this->booksclass = D('Books')->getBooksClass();
        $this->assign('booksclass', $this->booksclass);
    }

    //获取搜索关键字
    private function _getKeywords()
    {
        $keywords = mRequest('keywords');
        $this->assign('keywords', $keywords);

        return $keywords;
    }

    //上传封面图
    public function bookcoverupload()
    {
        //初始化上传类
        $Upload = new Upload();
        $Upload->maxSize  = $this->_upfilesize['image']['size'];
        $Upload->exts     = $this->_upfilesize['image']['exts'];
        $Upload->rootPath = UPLOAD_PATH;
        $Upload->savePath = 'book/cover/';
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

    //上传电子书
    public function bookfileupload()
    {
        //初始化上传类
        $Upload = new Upload();
        $Upload->maxSize  = $this->_upfilesize['image']['size'];
        $Upload->exts     = $this->_upfilesize['image']['exts'];
        $Upload->rootPath = UPLOAD_PATH;
        $Upload->savePath = 'book/';
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

        $classid = mRequest('classid');
        $this->assign('classid', $classid);
        $booktype = mRequest('booktype');
        $this->assign('booktype', $booktype);

        list($start, $length) = $this->_mkPage();
        $data = D('Books')->getBooks(null, $keywords, $classid, $booktype, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $this->_mkPagination($total);

        $this->display();
    }

    //新增
    public function newbook()
    {
        $this->display();
    }

    //编辑
    public function editbook()
    {
        $bookid = mRequest('bookid');
        $this->assign('bookid', $bookid);

        $book = D('Books')->getBooksByID($bookid);

        $this->assign('book', $book);
        $this->display();
    }

    //保存
    public function booksave()
    {
        $bookid = mRequest('bookid');

        $bookname = mRequest('bookname');
        if (!$bookname) $this->ajaxReturn(1, '请填写名称！');

        $bookdesc = mRequest('bookdesc');

        $classid = mRequest('classid');
        if (!$classid) $this->ajaxReturn(1, '请选择分类！');

        $booktype = mRequest('booktype');

        $bookcover = mRequest('bookcover');
        if (!$bookcover) $this->ajaxReturn(1, '请上传封面图！');

        $bookfile = mRequest('bookfile');
        $booklink = mRequest('booklink');
        if (!$bookfile && !$booklink) $this->ajaxReturn(1, '请上传电子书pdf或填写电子期刊链接！');

        if ($bookid) {
            $msg = '编辑';
            $data = array(
                'bookname'  => $bookname,
                'bookdesc'  => $bookdesc,
                'classid'   => $classid,
                'booktype'  => $booktype,
                'bookcover' => $bookcover,
                'bookfile'  => $bookfile,
                'booklink'  => $booklink,
                'updatetime' => TIMESTAMP
            );
            $bookid = M('books')->where(array('bookid'=>$bookid))->save($data);
        } else {
            $msg = '发布';
            $data = array(
                'bookname'  => $bookname,
                'bookdesc'  => $bookdesc,
                'classid'   => $classid,
                'booktype'  => $booktype,
                'bookcover' => $bookcover,
                'bookfile'  => $bookfile,
                'booklink'  => $booklink,
                'status'    => 1,
                'createtime' => TIMESTAMP,
                'updatetime' => TIMESTAMP
            );
            $bookid = M('books')->add($data);
        }

        if ($bookid) {
            $this->ajaxReturn(0, '电子期刊'.$msg.'成功！');
        } else {
            $this->ajaxReturn(1, '电子期刊'.$msg.'失败！');
        }
    }

    //删除
    public function deletebook()
    {
        $bookid = mRequest('bookid');
        if (!$bookid) $this->ajaxReturn(1, '未知信息！');

        $result = M('books')->where(array('bookid'=>$bookid))->delete();
        if ($result) {
            $this->ajaxReturn(0, '删除成功！');
        } else {
            $this->ajaxReturn(1, '删除失败！');
        }
    }
}