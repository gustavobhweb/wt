<?php

class AuthController extends BaseController
{
    private $manutencao = false;

    private $rules = [
        'username' => 'required|min:5',
        'password' => 'required'
    ];

    private $messages = [
        'required' => 'Esse campo é de preenchimento obrigatório',
        'min' => 'Esse campo deve conter pelo menos 5'
    ];

    const URL_API_LOGIN = 'http://newton.newtonpaiva.br/api/Login';

    protected $routesByRole = [
        1 => 'aluno',
        2 => 'producao',
        3 => 'financeiro',
        4 => 'cliente',
        5 => 'expedicao',
        6 => 'fabricacao',
        7 => 'producao/gerenciar-fotos',
        8 => 'producao/baixar-carga',
        9 => 'admin-cliente',
        10 => 'cliente/entregas',
        11 => 'cliente/inicial',
        12 => 'colaboradores'
    ];

    public function anyGuest()
    {
        if (Request::isMethod('post')) {

            $username = filter_var(Input::get('username'));
            $password = filter_var(Input::get('password'));

            $usuario = Usuario::whereUsername($username)->first();

            if ($usuario instanceof Usuario && Hash::check($password, $usuario->password)) {

                if ($usuario->nivel_id == 1) {
                    return Redirect::back()
                                    ->withInput()
                                    ->withErrors([
                                        'message' => 'Nivel de usuário inválido para essa página'
                                    ]);
                }

                Auth::login($usuario);

                return Redirect::guest('auth/router');
            } else {
                return Redirect::back()
                                ->withErrors(['message' => 'Usuário ou senha inválidos'])
                                ->withInput();
            }

        }

        if (!$this->manutencao) {
            return View::make('auth.guest');
        } else {
            return View::make('auth.manutencao');
        }
    }


    public function getRouter()
    {
        if (Auth::check() && ($auth = Auth::user())) {
            $permissoes = Auth::user()->nivel
                                         ->permissoes;
            $paginaInicial = $permissoes->first()->url;
            foreach ($permissoes as $permissao) {
                $is_home = NiveisPermissao::wherePermissaoId($permissao->id)
                                          ->whereNivelId($permissao->pivot->nivel_id)
                                          ->first()
                                          ->is_home;

                if ($is_home) {
                    $paginaInicial = $permissao->url;
                }
            }
            return Redirect::to($paginaInicial);
        } else {
            return Redirect::to('login');
        }
    }


    public function anyLogout()
    {
        Auth::logout();
        return Redirect::to('/');
        
    }

    public function anyLoginAluno()
    {
        if (Request::isMethod('post')) {

            try{

                $matricula = Input::get('matricula');
                $senha = Input::get('senha');

                // Stream passado por parâmetro no file_get_contents
                $context = stream_context_create([
                    'http' => [
                        'method'  => 'POST',
                        'content' => http_build_query([
                            'username' => $matricula,
                            'password' => $senha
                        ]),
                        'header'  => [
                            0 => 'Content-Type: application/x-www-form-urlencoded',
                            2 => sprintf('Authorization: Basic %s', base64_encode("tmt:5fGCxwqEryhfH7fLBYBaN9zN69dEK9e2"))
                        ]
                    ]
                ]);

                try {
                    $response = json_decode(file_get_contents(self::URL_API_LOGIN, false, $context));
                } catch(\Exception $e) {
                    throw new \Exception('Matrícula e/ou senha não conferem.');
                }


                $usuario = Usuario::whereMatricula($matricula)->first();

                /**
                * $response, nesse caso, vem como [true], por isso temos que capturá-lo pelo índice
                **/

            
                if (isset($response[0]) && $response[0] === true) {

                    if ($usuario instanceof Usuario) {

                        if ($usuario->nivel_id != 1) {

                            throw new Exception(sprintf(
                                'O seu nível de usuário exige que o login seja feito %s',
                                HTML::link('/login', 'nessa página', ['class' => 'link'])
                            ));
                        }

                        Auth::login($usuario);
                        return Redirect::to('auth/router');

                    }
                    
                    throw new \Exception("Você não possui créditos. <a class='saiba-mais' target='_blank' href='http://newtonpaiva.br/mkt/arquivos/manual_solicitacao_carteirinha_nov2014.pdf'>Saiba mais</a>");

                } else {
                    throw new \Exception('Matrícula e/ou senha incorretos.');
                }



            } catch(\Exception $e) {
                return Redirect::back()
                                ->withErrors(['message' => $e->getMessage()])
                                ->withInput();
            }
        }

        if (!$this->manutencao) {
            return View::make('auth.login-aluno');
        } else {
            return View::make('auth.manutencao');
        }
    }


    public function anyMeusDados()
    {
        if (Auth::user()->nivel_id === 1) {
            return Redirect::action('AlunoController@getMeusDados');
        }

        $aluno = Auth::user();

        $estado = Uf::orderBy('titulo')->lists('titulo', 'titulo');

        $cidade = [
            ''  => '(Selecione)',
            $aluno->cidade => $aluno->cidade,
        ];

        if (!$this->manutencao) {
            return View::make('auth.meus_dados', get_defined_vars());
        } else {
            return View::make('auth.manutencao');
        }
    }


    public function anyLoginColaborador()
    {
        if (Request::isMethod('post')) {

            $cpf   = filter_var(Input::get('cpf'));
            $senha = filter_var(Input::get('senha')); 

            try{

                $context = stream_context_create([
                    'http' => [
                        'method'  => 'POST',
                        'content' => http_build_query([
                            'username' => $cpf,
                            'password' => $senha
                        ]),
                        'header'  => [
                            0 => 'Content-Type: application/x-www-form-urlencoded',
                            1 => sprintf('Authorization: Basic %s', base64_encode("tmt:5fGCxwqEryhfH7fLBYBaN9zN69dEK9e2"))
                        ]
                    ]
                ]);

                try {
                    $response = json_decode(file_get_contents(self::URL_API_LOGIN, false, $context));
                } catch(\Exception $e) {
                    throw new \Exception('CPF e/ou senha não conferem.');
                }

                $usuario = Usuario::whereCpf($cpf)->first();
                
                if (isset($response[0]) && $response[0] === true) {

                    if ($usuario instanceof Usuario) {

                        if ($usuario->nivel_id != 12) {

                            throw new Exception(sprintf(
                                'O seu nível de usuário exige que o login seja feito %s',
                                HTML::link('/login', 'nessa página', ['class' => 'link'])
                            ));
                        }

                        Auth::login($usuario);
                        return Redirect::to('auth/router');

                    }
                    
                    throw new \Exception("Você não possui créditos. <a class='saiba-mais' target='_blank' href='http://newtonpaiva.br/mkt/arquivos/manual_solicitacao_carteirinha_nov2014.pdf'>Saiba mais</a>");

                } else {
                    throw new \Exception('CPF e/ou senha incorretos.');
                }



            } catch(\Exception $e) {

                return Redirect::back()
                                ->withErrors(['message' => $e->getMessage()])
                                ->withInput();
            }
        }

        if (!$this->manutencao) {
            return View::make('auth.login_colaboradores');
        } else {
            return View::make('auth.manutencao');
        }
    }
}