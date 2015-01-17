<?php

App::before(function ($request)
{
    View::share('user', Auth::user());
    
    View::composer(['layouts.default', 'layouts/default'], function ($view)
    {
        $view->nest('sidebar', 'sidebars.default', [
            'permissoes' => Auth::user()->nivel->permissoes
        ]);
    });
});

Route::filter('guest', function()
{
    if (Auth::check()) {
        return Redirect::to('auth/router');
    }
});

Route::filter('auth', function()
{
    if (Auth::guest()) {
        if (Request::ajax()) {
            return Response::make('Unauthorized', 401);
        } else {
            return Redirect::guest('login');
        }
    }
});

Route::filter('csrf', function()
{    
    if (Session::token() != Input::get('_token'))
    {
        throw new \Illuminate\Session\TokenMismatchException;
    }
});

/**
* Filtro para o web service
*/
Route::filter('auth.basic', function()
{
    return Auth::basic('username');
});