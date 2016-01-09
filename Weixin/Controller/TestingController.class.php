<?php
/**
 * 随堂测评逻辑层
 * buzhidao
 */
namespace Weixin\Controller;

class TestingController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->display();
    }
}