<?php

interface Framework_Library_Contracts_Pipeline_Pipeline
{
    /**
     * 发送原始请求 
     *
     * @param  mixed  $traveler
     * @return $this
     */
    public function send($traveler);

    /**
     * 经过的中间件 
     *
     * @param  dynamic|array  $stops
     * @return $this
     */
    public function through($stops);

    /**
     *
     *
     * @param  string  $method
     * @return $this
     */
    public function via($method);

    /**
     * 管道处理完后最后的回调
     *
     * @param  \Closure  $destination
     * @return mixed
     */
    public function then(Closure $destination);
}

