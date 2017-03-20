<?php
/**
 * 电子书
 * 2015-12-22
 * buzhidao
 */
namespace Admin\Model;

class BooksModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取电子书类型
    public function getBooksClass()
    {
        $where = array(
            'status' => 1,
        );
        $result = M('books_class')->where($where)->order('classid asc')->select();
        $data = array();
        if (is_array($result) && !empty($result)) {
            foreach ($result as $d) {
                $data[$d['classid']] = $d;
            }
        }

        return $data;
    }

    //获取电子书
    public function getBooks($bookid=null, $keywords=null, $classid=null, $booktype=null, $start=0, $length=9999)
    {
        $where = array(
            'status' => 1,
        );
        if ($bookid) $where['bookid'] = $bookid;
        if ($keywords) $where['_complex'] = array(
            '_logic'    => 'or',
            'bookname'   => array('like', '%'.$keywords.'%'),
            'bookdesc' => array('like', '%'.$keywords.'%'),
        );
        if ($classid) $where['classid'] = is_array($classid) ? array('in', $classid) : $classid;
        if ($booktype) $where['booktype'] = is_array($booktype) ? array('in', $booktype) : $booktype;

        $count = M('books')->where($where)->count();
        $result = M('books')->where($where)->order('createtime desc')->limit($start, $length)->select();

        return array('total'=>$count, 'data'=>is_array($result)?$result:array());
    }

    //获取电子书BYID
    public function getBooksByID($bookid=null)
    {
        if (!$bookid) return false;

        $book = $this->getBooks($bookid);

        return !empty($book['data']) ? current($book['data']) : array();
    }
}