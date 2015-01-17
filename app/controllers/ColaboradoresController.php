<?php

class ColaboradoresController extends BaseController
{
    public function getIndex()
    {
        $creditos = Credito::select('id')
                            ->whereUsuarioId(Auth::user()->id)
                            ->whereStatus(0)
                            ->count();

        $auth = Auth::user();

        $vars['creditos'] = $creditos;
        $vars['emandamento'] = Solicitacao::whereUsuarioId($auth->id)
                                               ->where('status_atual', '!=', 7)
                                               ->where('status_atual', '!=', 8)
                                               ->count();

        $vars['statusAtual'] = ($vars['emandamento']) ? Solicitacao::select('status_atual')
                                              ->whereUsuarioId($auth->id)
                                              ->where('status_atual', '!=', 7)
                                              ->limit(1)
                                              ->orderBy('id', 'desc')
                                              ->first()->status : 0;

        $vars['solicitacoes'] = Solicitacao::whereUsuarioId($auth->id)->get();
        
        $vars['avisos']       = Aviso::whereUsuarioId($auth->id)
                                           ->whereLido(0)
                                           ->limit(2)
                                           ->get();

        $vars['avisosCount']  = Aviso::whereUsuarioId($auth->id)
                                           ->whereLido(0)
                                           ->count();
        
        return View::make('colaboradores/index', $vars);
    }

    public function getAvisos()
    {
        $auth = Auth::user();

        $avisos = Aviso::whereUsuarioId($auth->id)->get();

        return View::make('colaboradores/avisos', compact('avisos'));
    }


    public function getLerAviso($id = 1)
    {
        $aviso = Aviso::read($id);

        return View::make('colaboradores/ler_aviso', compact('aviso'));
    }

    public function anyAcompanhar()
    {

        $auth = Auth::user();

        $vars['solicitacoes'] = Solicitacao::with('solicitacoesStatus')
                                            ->whereUsuarioId($auth->id)
                                            ->orderBy('id', 'DESC')
                                            ->get();
                                            
        $vars['letters'] = ['A','B','B','C','D','E','F','G','H','I','J'];

        return View::make('colaboradores/acompanhar', $vars);
    }

    public function anyEnviarFoto()
    {

        $auth = Auth::user();

        try {

            // Se não existir, a consulta lança uma Exception

            $credito = Credito::select('id')
                                 ->whereUsuarioId($auth->id)
                                 ->whereStatus(0)
                                 ->firstOrFail();    

        } catch (Exception $e) {
            
            return Redirect::to('/');
        }
        

        $emAndamento = Solicitacao::whereUsuarioId($auth->id)
                                       ->whereNotIn('status_atual', [7, 8])
                                       ->count();

        /*
            Se já existe uma solicitação em andamento,
            o aluno é redirecionado para a acompanhamento
        */

        if ($emAndamento) {

            return Redirect::to('colaboradores/acompanhar');
        }

        $vars['instituicoes'] = Instituicao::all();

        $vars['ufs'] = Uf::lists('titulo', 'id');
        
        $vars['instituicaoPadrao'] = UsuarioInstituicao::whereUsuarioId($auth->id)->pluck('instituicao_id') ?: 1;

        $tempFile = public_path("/imagens/colaboradores/{$auth->id}/temp.png");

        $nameFile = md5(microtime()) . '.png';

        $newName = public_path("/imagens/colaboradores/{$auth->id}/{$nameFile}");
       
        if (Request::isMethod('post')) {

            File::move($tempFile, $newName);

            try {

                DB::transaction(function () use($nameFile, $auth, $credito) {

                    $usuarioUpdate = [
                        'bairro'      => Input::get('bairro'),
                        'cep'         => preg_replace('/[^0-9]/', '', Input::get('cep')),
                        'cidade'      => Input::get('cidade'),
                        'complemento' => Input::get('complemento'),
                        'email'       => Input::get('email'),
                        'endereco'    => Input::get('endereco'),
                        'numero'      => Input::get('numero'),
                    ];

                    /*
                        Atualiza as informações do usuário
                    */

                    Usuario::whereId($auth->id)->update($usuarioUpdate);
            
                    /*
                        Cria a solicitação
                    */

                    $solicitacaoCriada = Solicitacao::create([
                        'credito_id'             => $credito->id,
                        'foto'                   => $nameFile,
                        'instituicao_entrega_id' => 1, // Instituição padrão 
                        'status_atual'           => 2,
                        'usuario_id'             => $auth->id,
                    ]);


                    SolicitacoesStatus::create([
                        'solicitacao_id' => $solicitacaoCriada->id,
                        'status_id'      => 2
                    ]);

                    Aviso::create([
                        'assunto'    => 'Solicitação realizada',
                        'remetente'  => 'Newton Paiva',
                        'mensagem'   => 'A sua solicitação de carteira, estudantil foi enviada com sucesso! Aguarde pela aprovação da sua foto.',
                        'usuario_id' =>  $auth->id
                    ]);

                    /*
                        Atualiza o status do crédito para usado
                    */

                    $credito->fill(['status' => 1])->save();

                });

                return Redirect::to('colaboradores/acompanhar');

            } catch(\Exception $e) {

                return Redirect::back()
                                ->withInput()
                                ->withErrors(['message' => $e->getMessage()]);
            }
        }

        return View::make('colaboradores/enviar-foto', $vars);    
    }

    public function postCropimage()
    {
        $imgstr = file_get_contents(Input::file('img')->getRealPath());
        $ext = Input::get('ext');

        $ds = DIRECTORY_SEPARATOR;
        $matricula = Auth::user()->matricula;
        $dir = "imagens{$ds}{$matricula}{$ds}";
        $filename = 'temp.png';
        $fullpath = $dir .  $filename;

        if (!is_dir($dir)) mkdir($dir);


        list($width, $height) = getimagesizefromstring($imgstr);

        $widthVar = 358;
        $heightVar = 478;
        $dest = imagecreatetruecolor($widthVar, $heightVar);

        $im = null;
        $tempbmppngfile = md5(uniqid() . time()) . '.bmp';
        if (!is_dir(public_path('/imagens/tempbmp/'))) mkdir(public_path('/imagens/tempbmp/'));
        if ($ext == 'bmp') {
            file_put_contents(public_path('/imagens/tempbmp/') . $tempbmppngfile, $imgstr);
            $im = $this->imagecreatefrombmp(public_path('/imagens/tempbmp/') . $tempbmppngfile);
            @unlink(public_path('/imagens/tempbmp/') . $tempbmppngfile);
        } else {
            $im = imagecreatefromstring($imgstr);
        }

        $x = Input::get('x');
        $y = Input::get('y');
        $w = Input::get('w');
        $h = Input::get('h');

        $nh = 215;
        $nw = ($nh * $width) / $height;

        imagecopyresampled($dest, $im, 0, 0, $x, $y, $widthVar, $heightVar, $w, $h);

        imagepng($dest, $fullpath);

        imagedestroy($dest);

        return Response::json(['url' => URL::to($fullpath)]);
    }

    public function postSnapwebcam()
    {
        $id = Auth::user()->id;

        $dir = public_path("imagens/colaboradores/{$id}/");

        if (! is_dir($dir)) {

            mkdir($dir, 0777);

        }

        $filename = 'temp.png';
        $fullurl = $dir . $filename;

        $imgstr = base64_decode(Input::get('file'));

        list($width, $height) = getimagesizefromstring($imgstr);

        $newW = 358;
        $newH = 478;

        $dest = imagecreatetruecolor($newW, $newH);
        $im = imagecreatefromstring($imgstr);

        $nw = ($newH * $width) / $height;

        if (Input::has('flash')) {

            imagecopyresampled($dest, $im, 0, 0, 70, 0, $nw, $newH, $nw, $newH);

        } else {

            imagecopyresampled($dest, $im, 0, 0, 138, 0, $nw, $newH, $width, $height);

        }

        imagepng($dest, $fullurl);

        return Response::json(true);
    }

    public function postSaveFacebookPhoto()
    {
        $auth = Auth::user();

        $directory = public_path("imagens/{$auth->matricula}/");
        
        $idfacebook = filter_var(Input::get('idfacebook'));

        $url = 'https://graph.facebook.com/'.$idfacebook.'/picture?type=large';

        !is_dir($directory) && mkdir($directory);

        return Response::json([
            'url' => $url,
            'base64' => base64_encode(file_get_contents($url))
        ]);   
    }
    
    public function getMeusDados()
    {
        $ufs = Uf::lists('titulo', 'titulo');

        $aluno = Auth::user();

        $cidade = [
            ''  => '(Selecione)',
            $aluno->cidade => $aluno->cidade,
        ];

        $estado = [
            $aluno->uf => $aluno->uf
        ];


        return View::make(
            'colaboradores/meus-dados',
            get_defined_vars()
        );
    }


    public function postMeusDados()
    {
        $rules = [
            'email'         => 'required|email',
            'confirmaEmail' => 'required|same:email',
            'cep'           => 'required|regex: /\d{5}-\d{3}/',
            'cidade'        => 'required',
            'uf'            => 'required',
            'endereco'      => 'required',
            'numero'        => 'required',
            'bairro'        => 'required'
        ];

        $message = [
            'required' => 'O campo ":attribute" é obrigatório',
            'match'    => 'O campo ":attribute" está com formato inválido',
            'same'     => 'O campo ":attribute" não confere'
        ];

        $validation = Validator::make(Input::all(), $rules, $message);

        if (! $validation->passes()) {

            return Redirect::action('AlunoController@getMeusDados') 
                            ->withInput()
                            ->withErrors([
                                'message' => $validation->messages()->first()
                            ]);
        } else {

            $input = Input::only(
                'email',
                'cep',
                'endereco',
                'numero', 
                'complemento',
                'estado',
                'bairro'
            );

            $input['cep'] = preg_replace('/[^0-9]/', '', $input['cep']);

            Auth::user()->fill($input)->save();

            return Redirect::action('AlunoController@getMeusDados')
                            ->withErrors(['message' => 'Os dados foram salvos com sucesso']);
        }
    }
}

