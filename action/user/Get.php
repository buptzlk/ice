<?php

/**
 * Created by PhpStorm.
 * Author by a super man
 * Email <17888835130@163.com>
 * Date: 17/8/24
 * Time: 16:16
 */
class Action_User_Get extends Framework_ActionBase
{
    public function index(){
        print_r($this->request->get());
    }
}