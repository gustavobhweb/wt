<?php

setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");

Route::group(['before' => 'guest'], function ()
{
    Route::any('/', [
        'as' => 'login_interno',
        'uses' => 'AuthController@anyGuest'
    ]);
});

Route::any('/logout', [
    'as' => 'logout',
    'uses' => 'AuthController@anyLogout'
]);

Route::controller('auth', 'AuthController');

Route::group(['before' => 'auth'], function(){
    if (Auth::check()) {
        foreach (Auth::user()->nivel->permissoes as $permissao) {

            $pos = strpos($permissao->url, '{');
            $newUrl = $permissao->url;
            if ($pos) {
                $newUrl = substr($permissao->url, 0, $pos) . '*';
            }

            if (!Session::has('submenu') && Request::is($newUrl) && !$permissao->in_menu && !Request::ajax()) {
                Session::put('submenu', [
                    $permissao->id => [
                        'name' => $permissao->name,
                        'url' => Request::url(),
                        'newUrl' => $newUrl
                    ]
                ]);
            } elseif (Session::has('submenu') && !isset(Session::get('submenu')[$permissao->id]) && Request::is($newUrl) && !$permissao->in_menu && !Request::ajax()) {
                Session::put('submenu', Session::get('submenu') + [
                    $permissao->id => [
                        'name' => $permissao->name,
                        'url' => Request::url(),
                        'newUrl' => $newUrl
                    ]
                ]);
            }

            Route::{$permissao->type}(
                $permissao->url, [
                    'uses' => $permissao->action
                ]
            );
        }
    }
});

Route::group(['before' => 'nivel_admin_cliente'], function (){
    if (Auth::check() && Auth::user()->nivel_id === 9) { 
        Route::controller('admin-cliente', 'AdminClienteController');
    }
});

Route::group(['before' => 'nivel_colaborador_cliente'], function ()
{

    Route::any('colaboradores', function ()
    {
        return Redirect::action('ColaboradoresController@getEnviarFoto');
    });


    Route::post('aluno/cropimage', ['uses' => 'AlunoController@postCropimage']);

    Route::controller('colaboradores', 'ColaboradoresController');
});


Route::get('json/cidades', function ()
{
    $cidade = Cidade::where('uf_id', '=', Input::get('uf_id'))->lists('nome');

    return Response::json($cidade);
});


Route::get('captcha', function()
{

    $word = [0 => null, 1 => null];
        
    for ($i = 0; $i < 4; $i++) {
        $word[0] .= chr(mt_rand(97, 122));
        $word[1] .= chr(mt_rand(97, 122));
    }

    $word = implode(' ', $word);
    
    Session::put('capcha_word', $word);

    $font = public_path('recaptcha/fonts/recaptchaFont.ttf');

    $image = imagecreatetruecolor(172, 50);

    $color = imagecolorallocate($image, 0, 0, 0);
    $white = imagecolorallocate($image, 255, 255, 255);

    imagefilledrectangle($image, 0, 0, 172, 99, $white);
    imagettftext($image, 22, 0, 5, 35, $color, $font, Session::get('capcha_word'));
        
    $tempCapcha = tempnam(null, 'capcha');

    imagepng($image, $tempCapcha);

    return Response::make(File::get($tempCapcha), 200, ['Content-Type' => 'image/png']);
});


Route::post('verify-captcha', function()
{
    return Response::json([
        'status' => Session::get('capcha_word') == Input::get('text')
    ]);
    
});

Route::get('close-tab/{permissao_id}', function($permissao_id)
{
    $submenu = Session::get('submenu');
    unset($submenu[$permissao_id]);
    Session::put('submenu', $submenu);
    return Redirect::to('/');
});

/**
* Rota para o web service
*/
Route::group(['before' => 'auth.basic'], function(){
    Route::controller('service', 'ServiceController');
});

