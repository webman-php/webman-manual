<?php
namespace app\controller;

use GuzzleHttp\Psr7\Response;
use support\Request;

class Index
{
    public function index(Request $request)
    {
        return redirect('/doc/en/');
    }
}