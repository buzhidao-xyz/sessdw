<?php
/**
 * 试卷数据模型
 * 2015-12-22
 * buzhidao
 */
namespace Admin\Model;

class TestingModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取试卷信息
    public function getTesting($courseid=null, $testingid=null, $keyword=null, $start=0, $length=9999)
    {
        $wheresub = array();
        if ($courseid) $wheresub['a.courseid'] = $courseid;
        if ($testingid) $wheresub['a.testingid'] = $testingid;
        if ($keyword) $wheresub['b.title'] = array('like', '%'.$keyword.'%');
        $subQuery = M('testing')->alias('a')
                                ->field('a.*, b.title, b.classid, b.viewnum, b.learnnum, b.isshow')
                                ->join('__COURSE__ b on a.courseid=b.courseid')
                                ->where($wheresub)->order('a.createtime desc')->buildSql();

        $total = M('testing')->table($subQuery.' sub')->count();
        $result = M('testing')->table($subQuery.' sub')->limit($start, $length)->select();

        return array('total'=>$total, 'data'=>is_array($result)?$result:array());
    }

    //获取试卷信息 通过ID
    public function getTestingByID($courseid=null, $testingid=null)
    {
        if (!$courseid && !$testingid) return false;

        $testinginfo = $this->getTesting($courseid, $testingid);

        return $testinginfo['total'] ? $testinginfo['data'][0] : array();
    }

    //保存试卷
    public function testingsave($data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        $examsdata = $data['exams'];
        unset($data['exams']);

        //开始事务
        M('testing')->startTrans();

        $testingid = M('testing')->add($data);
        $examsresult = false;
        if ($testingid) {
            foreach ($examsdata as $k=>$d) {
                $examsdata[$k]['testingid'] = $testingid;
            }
            $examsresult = M('testing_exam')->addAll($examsdata);
        }

        //提交事务
        if ($testingid && $examsresult) {
            M('testing')->commit();
            return $testingid;
        } else {
            M('testing')->rollback();
            return false;
        }
    }

    //保存试题
    public function examsave($data=array())
    {
        if (!is_array($data) || empty($data)) return false;

        $examdata = array(
            'type' => $data['type'],
            'title' => $data['title'],
            'answer' => $data['answer'],
            'createtime' => $data['createtime'],
            'updatetime' => $data['updatetime'],
        );

        //开始事务
        M('exam')->startTrans();

        $examid = M('exam')->add($examdata);
        $optionsresult = false;
        if ($examid) {
            $optionsdata = $data['options'];
            foreach ($optionsdata as $k=>$d) {
                $optionsdata[$k]['examid'] = $examid;
                $optionsdata[$k]['updatetime'] = $data['updatetime'];
            }
            $optionsresult = M('exam_option')->addAll($optionsdata);
        }

        //提交事务
        if ($examid && $optionsresult) {
            M('exam')->commit();
            return $examid;
        } else {
            M('exam')->rollback();
            return false;
        }
    }
}