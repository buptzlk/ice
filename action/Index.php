<?php
/**
 * Created by PhpStorm.
 * Author by a super man
 * Email <17888835130@163.com>
 * Date: 17/8/23
 * Time: 19:01
 */
class Action_Index extends Framework_ActionBase {

    public function index() {
        $getArray = $this->request->get();
        print_r($getArray);
    }
}
