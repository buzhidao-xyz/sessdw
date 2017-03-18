<?php
/**
 * 红色阅览室数据模型
 * 2015-12-22
 * buzhidao
 */
namespace Front\Model;

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
    public function getBooks($bookid=null, $classid=null, $booktype=null, $start=0, $length=9999)
    {
        $where = array(
            'status' => 1,
        );
        if ($bookid) $where['bookid'] = $bookid;
        if ($classid) $where['classid'] = is_array($classid) ? array('in', $classid) : $classid;
        if ($booktype) $where['booktype'] = is_array($booktype) ? array('in', $booktype) : $booktype;

        $count = M('books')->where($where)->count();
        $result = M('books')->where($where)->order('createtime desc')->limit($start, $length)->select();

        return array('total'=>$count, 'data'=>is_array($result)?$result:array());
    }
}