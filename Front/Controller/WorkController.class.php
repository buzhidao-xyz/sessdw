<?php
/**
 * 作业模型逻辑控制
 * buzhidao
 * 2015-12-08
 */
namespace Front\Controller;

use Any\Controller;

class WorkController extends CommonController
{
    //导航栏目navflag标识
    public $navflag = 'Work';

    public function __construct()
    {
        parent::__construct();

        $this->_work_class = C('USER.work_class');
        $this->assign('workclass', $this->_work_class);
    }

    //获取分类Id
    private function _getClassid()
    {
        $classid = mRequest('classid');

        return $classid;
    }

    //获取作业id
    private function _getWorkid()
    {
        $workid = mRequest('workid');

        return $workid;
    }

    //作业首页
    public function index()
    {
        $classid = $this->_getClassid();
        $this->assign('classid', $classid);

        list($start, $length) = $this->_mkPage();
        $data = D('Work')->getWork(null, $classid, $this->userinfo['userid'], $start, $length);
        $total = $data['total'];
        $worklist = $data['data'];

        $this->assign('worklist', $worklist);

        $param = array(
            'classid' => $classid,
        );
        $this->assign('param', $param);
        //解析分页数据
        $this->_mkPagination($total, $param);

        $this->display();
    }
}