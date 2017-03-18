<?php
/**
 * 红色阅览室
 * User: bzd
 * Date: 2017/3/10
 * Time: 12:50
 */
namespace Front\Controller;

class BooksController extends CommonController
{
    //导航栏目navflag标识
    public $navflag = 'Books';

    protected $_pagesize = 12;

    public function __construct()
    {
        parent::__construct();

        $this->booksclass = D('Books')->getBooksClass();
        $this->assign('booksclass', $this->booksclass);
    }

    //阅览室主页
    public function index()
    {
        //sess电子党刊
        $sessbooks = D('Books')->getBooks(null, null, 1, 0, 10);
        $this->assign('sessbooks', $sessbooks['data']);

        $this->display();
    }

    //图书列表
    public function booklist()
    {
        $classid = mRequest('classid');
        $this->assign('classid', $classid);

        $booktype = mRequest('booktype');
        $this->assign('booktype', $booktype);

        //电子书
        list($start, $length) = $this->_mkPage();
        $booklist = D('Books')->getBooks(null, $classid, $booktype, $start, $length);
        $total = $booklist['total'];
        $datalist = $booklist['data'];

        $this->assign('datalist', $datalist);

        $this->_mkPagination($total);
        $this->display();
    }
}