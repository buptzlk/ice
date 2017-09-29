<?php
/**
 * Created by PhpStorm.
 * Author by a super man
 * Email <17888835130@163.com>
 * Date: 17/9/9
 * Time: 13:52
 */

interface Framework_Library_Contracts_Request_Request {


    /**
     * @brief 获取get请求参数
     * @param array()
     * @return
     */
    public function get();

    /**
     * @brief 获取post请求参数
     * @param array()
     * @return
     */
    public function post() ;

    /**
     * @brief 获取request请求参数
     * @param array()
     * @return
     */
    public function request();

    /**
     * @brief 获取uri
     * @param
     * @return
     */
    public function uri() ;

    /**
     * @brief 获取路由信息
     * @param
     * @return string
     */
    public function action();
    /**
     * @brief 文件路径
     * @param
     * @return
     */
    public function path() ;
    /**
     * @brief 获取请求方式
     * @param array()
     * @return
     */
    public function method();
}