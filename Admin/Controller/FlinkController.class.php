<?php
/**
 * 友情链接
 * buzhidao
 * 2015-12-27
 */
namespace Admin\Controller;

use Org\Util\Filter;
use Org\Util\String;

class FlinkController extends CommonController
{
    public function __construct()
    {
        parent::__construct();

        $this->_page_location = __APP__ . '?s=Flink/index';
    }

    public function index()
    {
        list($start, $length) = $this->_mkPage();
        $data = D('Flink')->getFlink(null, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        //解析分页数据
        $this->_mkPagination($total);

        $this->display();
    }

    //新增
    public function newflink()
    {
        $this->display();
    }

    //新增保存
    public function newflinksave()
    {
        $title = mRequest('title');
        $link = mRequest('link');

        $data = array(
            'title' => $title,
            'link' => $link,
            'createtime' => TIMESTAMP,
            'updatetime' => TIMESTAMP,
        );
        $result = M('flink')->add($data);
        if ($result) {
            $this->pageReturn(0, '保存成功！', $this->_page_location);
        } else {
            $this->pageReturn(1, '保存失败！', $this->_page_location);
        }
    }

    //编辑
    public function editflink()
    {
        $flinkid = mRequest('flinkid');
        $this->assign('flinkid', $flinkid);

        $flink = D('Flink')->getFlinkByID($flinkid);

        $this->assign('flink', $flink);
        $this->display();
    }

    //编辑保存
    public function editflinksave()
    {
        $flinkid = mRequest('flinkid');

        $title = mRequest('title');
        $link = mRequest('link');

        $data = array(
            'title' => $title,
            'link' => $link,
            'updatetime' => TIMESTAMP,
        );
        $result = M('flink')->where(array('flinkid'=>$flinkid))->save($data);
        if ($result) {
            $this->pageReturn(0, '保存成功！', $this->_page_location);
        } else {
            $this->pageReturn(1, '保存失败！', $this->_page_location);
        }
    }

    //删除
    public function deleteflink()
    {
        $flinkid = mRequest('flinkid');
        if (!$flinkid) $this->ajaxReturn(1, '未知链接！');

        $result = M('flink')->where(array('flinkid'=>$flinkid))->delete();
        if ($result) {
            $this->ajaxReturn(0, '删除成功！');
        } else {
            $this->ajaxReturn(1, '删除失败！');
        }
    }
}