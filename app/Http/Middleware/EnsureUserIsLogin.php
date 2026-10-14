<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth;

class EnsureUserIsLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard= 'users'): Response
    {
        if (!auth()->guard($guard)->check()) {
            
            // 🌟 ប្រើប្រាស់ route() ជំនួសវិញ ដើម្បីឱ្យវាស្វ័យប្រវត្តិស្គាល់ URL ថ្មី (admin-portal)
            return redirect()->route('login');
            
        }
        return $next($request);
    }
}