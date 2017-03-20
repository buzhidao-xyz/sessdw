<?php
/**
 * 支部建设
 * buzhidao
 * 2015-12-27
 */
namespace Admin\Controller;

use Org\Util\Filter;
use Org\Util\String;

class TszbController extends CommonController
{
    public function __construct()
    {
        parent::__construct();

        $this->_page_location = __APP__ . '?s=Tszb/index';

        $this->tszb = D('Tszb')->getTszb();
        $this->assign('tszb', $this->tszb['data']);

        $this->tszbclass = D('Tszb')->getTszbClass();
        $this->assign('tszbclass', $this->tszbclass);
    }

    private function _getZhibuid()
    {
        $zhibuid = mRequest('zhibuid');
        $this->assign('zhibuid', $zhibuid);

        return $zhibuid;
    }

    //获取搜索关键字
    private function _getKeywords()
    {
        $keywords = mRequest('keywords');
        $this->assign('keywords', $keywords);

        return $keywords;
    }

    private function _getClassid()
    {
        $classid = mRequest('classid');
        $this->assign('classid', $classid);

        return $classid;
    }

    //支部建设
    public function index()
    {
        $keywords = $this->_getKeywords();

        list($start, $length) = $this->_mkPage();
        $data = D('Tszb')->getTszb(null, $keywords, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $param = array(
            'keywords'   => $keywords,
        );
        $this->assign('param', $param);
        $this->_mkPagination($total, $param);

        $this->display();
    }

    //会议管理
    public function built()
    {
        $zhibuid = $this->_getZhibuid();

        $keywords = $this->_getKeywords();

        $classid = $this->_getClassid();

        list($start, $length) = $this->_mkPage();
        $data = D('Tszb')->getTszbBuilt(null, $zhibuid, $keywords, $classid, $start, $length);
        $total = $data['total'];
        $datalist = $data['data'];

        $this->assign('datalist', $datalist);

        $param = array(
            'classid'   => $classid,
            'zhibuid'   => $zhibuid,
            'keywords'   => $keywords,
        );
        $this->assign('param', $param);
        $this->_mkPagination($total, $param);

        $this->display();
    }

    //会议内容
    public function profile()
    {
        $builtid = mRequest('builtid');
        $this->assign('builtid', $builtid);

        $tszbbuilt = D('Tszb')->getTszbBuilt($builtid);

        $this->assign('tszbbuilt', current($tszbbuilt['data']));
        $this->display();
    }

    //新增会议
    public function newbuilt()
    {
        $this->display();
    }

    //新增会议-保存
    public function newbuiltsave()
    {
        $zhibuid = mRequest('zhibuid');
        $classid = mRequest('classid');
        $title = mRequest('title');
        $shorttitle = mRequest('shorttitle');
        $dt = mRequest('dt');
        $place = mRequest('place');
        $usernum = mRequest('usernum');
        $userdesc = mRequest('userdesc');
        $content = mRequest('content');

        $data = array(
            'zhibuid' => $zhibuid,
            'classid' => $classid,
            'title' => $title,
            'shorttitle' => $shorttitle,
            'dt' => strtotime($dt),
            'place' => $place,
            'usernum' => $usernum,
            'userdesc' => $userdesc,
            'status'  => 1,
            'content' => $content,
            'createtime' => TIMESTAMP,
            'updatetime' => TIMESTAMP,
        );
        $builtid = M('tszb_built')->add($data);

        if ($builtid) {
            $this->pageReturn(0, '保存成功！', $this->_page_location);
        } else {
            $this->pageReturn(1, '保存失败！', $this->_page_location);
        }
    }

    //会议内容-编辑
    public function editbuilt()
    {
        $builtid = mRequest('builtid');
        $this->assign('builtid', $builtid);

        $tszbbuilt = D('Tszb')->getTszbBuilt($builtid);

        $this->assign('tszbbuilt', current($tszbbuilt['data']));
        $this->display();
    }

    //新增会议-编辑保存
    public function editbuiltsave()
    {
        $builtid = mRequest('builtid');
        if (!$builtid) $this->pageReturn(1, '未知信息！', $this->_page_location);

        $zhibuid = mRequest('zhibuid');
        $classid = mRequest('classid');
        $title = mRequest('title');
        $shorttitle = mRequest('shorttitle');
        $dt = mRequest('dt');
        $place = mRequest('place');
        $usernum = mRequest('usernum');
        $userdesc = mRequest('userdesc');
        $content = mRequest('content');

        $data = array(
            'zhibuid' => $zhibuid,
            'classid' => $classid,
            'title' => $title,
            'shorttitle' => $shorttitle,
            'dt' => strtotime($dt),
            'place' => $place,
            'usernum' => $usernum,
            'userdesc' => $userdesc,
            'content' => $content,
            'updatetime' => TIMESTAMP,
        );
        $result = M('tszb_built')->where(array('builtid'=>$builtid))->save($data);

        if ($result) {
            $this->pageReturn(0, '保存成功！', $this->_page_location);
        } else {
            $this->pageReturn(1, '保存失败！', $this->_page_location);
        }
    }
}