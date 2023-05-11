<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Administrator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()){

            if (Auth::user()->role == 'admin' || Auth::user()->role == 'user'){
                return $next($request);
            } else{
                return redirect('home')->with('warning', 'You are not authorize to access the page.');
            }

         } else{
            return redirect('/login');
        }
        return $next($request);
    }

}
