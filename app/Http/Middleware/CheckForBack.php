<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckForBack
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	    if ($request->hasHeader('ct')) {
		    $request['token']=$request->header('ct');
		    if(!Auth::guard('back')->user()){
			    return fail('','token无效,请重新登录',INVALID_TOKEN);
		    }
	    }else{
	    	return fail('','token失效,请重新登录',NOT_LOGIN);
	    }
        return $next($request);
    }
}
