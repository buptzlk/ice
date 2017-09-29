<?php

/**
 * Created by PhpStorm.
 * Author by a super man
 * Email <17888835130@163.com>
 * Date: 17/9/25
 * Time: 16:40
 */
class Http_Pipe_AgeAuth
{
    /**
     * Run the request filter.
     *
     * @param  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        echo __FUNCTION__;

        return $next($request);
    }
}