<?php

/**
 * Created by PhpStorm.
 * Author by a super man
 * Email <17888835130@163.com>
 * Date: 17/9/25
 * Time: 16:39
 */
class Http_Pipe_Login
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
        print_r($request);
        echo __FUNCTION__;

        return $next($request);
    }

}