<?php

/**
    *Controller do Administrador Cliente (Newton Paiva)
*/
class ClienteController extends BaseController
{

    public function anyEnviarCarga()
    {
        if (Request::isMethod('post') && Input::hasFile('excel')) {
            
            $allowedExtensions = [
                'xls',
                'xlsx',
                'csv'
            ];
            
            $fileInstance = Input::file('excel');
            
            $extension = strtolower($fileInstance->getClientOriginalExtension());
            
            if (! in_array($extension, $allowedExtensions)) {
                return Redirect::back()->withErrors([
                    'message' => 'A extensão de arquivo é inválido'
                ]);
            }
            
            $tmpFilename = $fileInstance->getRealPath();
            
            try {
                
                Excel::load($tmpFilename, function ($reader)
                {
                    
                    $requiredFields = [
                        'cpf',
                        'curso',
                        'instituicao',
                        'matricula',
                        'nome',
                        'protocolo',
                        'turno',
                    ];
                    
                    $reader = $reader->select($requiredFields)->all();
                    
                    $fields = array_keys($reader->first()->toArray());
                    
                    if ($missingFields = array_diff($requiredFields, $fields)) {
                        
                        throw new \UnexpectedValueException(sprintf('Alguns campos não foram incluídos no documento.A saber: %s', implode(', ', $missingFields)));
                    }
                    
                    $data = $reader->toArray();
                    
                    $separated = [
                        'not' => [],
                        'yes' => [],
                        'hasEmpty' => []
                    ];
                    
                    DB::transaction(function () use($data, &$separated)
                    {
                        
                        foreach ($data as $userData) {
                            
                            if (in_array(false, $userData)) {
                                $separated['hasEmpty'][] = $userData;
                                continue;
                            }
                            
                            $query = Usuario::where('matricula', '=', $userData['matricula']);
                            
                            if ($query->count()) {
                                $separated['not'][] = $userData;
                                $user = $query->first();
                            } else {
                                $userData['nivel_id'] = 1;
                                $user = Usuario::create($userData + [
                                    'username' => DB::raw('UUID()')
                                ]);
                                $separated['yes'][] = $userData;
                            }
                            
                            Credito::create([
                                'usuario_id' => $user->id,
                                'status' => '0'
                            ]);
                        }
                    });
                    
                    return Redirect::back()->with([
                        'messageSuccess' => 'Os registros foram inseridos com successo',
                        'uploadedData' => $separated
                    ]);
                }, 'UTF-8');
            } catch (\Exception $e) {
                return Redirect::back()->withErrors([
                    'message' => $e->getMessage()
                ]);
            }
        }
        
        return View::make('cliente.enviar_carga');
    }

    public function getPesquisarAlunos()
    {
        $filters = [
            'usuarios.cpf' => 'CPF',
            'instituicoes.nome' => 'Instituição',
            'usuarios.matricula' => 'Matrícula',
            'usuarios.nome' => 'Nome',
            'solicitacoes.status_atual' => 'Status do protocolo',
            's_remessa.remessa_id' => 'Nº Remessa'
        ];
        
        $filtro = filter_var(Input::get('filtro'));
        $valor = trim(filter_var(Input::get('valor'), FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        
        if ($filtro && $valor && array_key_exists($filtro, $filters)) {
            
            try {
                
                $pesquisa = $valor;
                
                switch ($filtro) {
                    case 'usuarios.nome':
                    case 'instituicoes.instituicao':
                        $operator = 'LIKE';
                        $pesquisa = "%{$pesquisa}%";
                        break;
                    case 'usuarios.cpf':
                        $operator = '=';
                        $pesquisa = preg_replace('/[^\d+]/', '', $pesquisa);
                        break;
                    case 'usuarios.matricula':
                        $operator = '=';
                        break;
                    default:
                        $operator = '=';
                        break;
                }
                
                $select = [
                    'usuarios.id AS id',
                    'instituicoes.nome AS instituicao',
                    's_remessa.remessa_id AS remessa',
                    'usuarios.nome',
                    'usuarios.matricula',
                    'usuarios.cpf',
                    'usuarios.curso',
                    'solicitacoes.codigo_w'
                ];
                
                $query = Usuario::select($select)->where('nivel_id', '=', 1)
                    ->where($filtro, $operator, $pesquisa)
                    ->leftJoin('usuarios_instituicao AS ui', 'ui.usuario_id', '=', 'usuarios.id')
                    ->leftJoin('instituicoes', 'ui.instituicao_id', '=', 'instituicoes.id')
                    ->leftjoin('solicitacoes', function ($query)
                {
                    $query->on('solicitacoes.usuario_id', '=', 'usuarios.id');
                })
                    ->leftJoin('solicitacoes_remessa AS s_remessa', 's_remessa.solicitacao_id', '=', 'solicitacoes.id')
                    ->orderBy('solicitacoes.created_at', 'desc')
                    ->groupBy('solicitacoes.usuario_id');
                
                Session::put('download.pesquisarAlunos', $query->get()->toArray());
                
                $usuarios = $query->paginate(20)->appends(Input::except('page'));
            } catch (Exception $e) {
                
                return Redirect::action('ClienteController@getPesquisarAlunos')->withErrors([
                    'message' => 'Parâmetro inválido para consulta',
                    'errorDetail' => $e->getMessage()
                ]);
            }
        }
        
        return View::make('cliente/pesquisar_alunos', compact('filters', 'usuarios', 'valor', 'filtro'));
    }

    

    public function postPesquisarAlunos()
    {
        Excel::create('download', function ($excel)
        {
            
            $excel->setTitle('Pesquisa de Alunos');
            
            $excel->sheet('alunos', function ($sheet)
            {
                $sheet->fromArray(Session::get('download.pesquisarAlunos'));
                
                $sheet->row(1, function ($row){
                    $row->setFontColor('#ffffff')
                        ->setBackground('#00458B');
                });
            });
        })->download('xlsx');
    }

    public function anyBaixarCarga()
    {
        $remessas = Remessa::select('remessas.*')->join('solicitacoes_remessa AS sr', 'sr.remessa_id', '=', 'remessas.id')
            ->join('solicitacoes AS s', 's.id', '=', 'sr.solicitacao_id')
            ->where('remessas.deletado', '=', 0)
            ->whereIn('s.status_atual', [
            10,
            5,
            6,
            7
        ]);
        
        if (Request::isMethod('post') && Input::has('remessa_id')) {
            
            $remessaId = (int) Input::get('remessa_id');
            $remessas->where('remessas.id', 'like', "{$remessaId}%");
        }
        
        $remessas = $remessas->paginate(20);
        
        return View::make('cliente.baixar_carga', compact('remessas'));
    }

    public function getAjaxListarRemessas()
    {
        if (Request::ajax()) {
            
            return array_map(function ($value)
            {
                return zero_fill($value, 4);
            }, Remessa::orderBy('created_at', 'desc')->take(1000)->lists('id'));
        }
    }

    public function getAjaxListarInstituicoes()
    {
        if (Request::ajax()) {
            return array_map(function ($value)
            {
                return zero_fill($value, 4);
            }, Instituicao::orderBy('created_at', 'desc')->take(1000)->lists('nome'));
        }
    }

    public function getDownloadCargaRemessa($id = null)
    {
        try {
            
            $remessa = Remessa::findOrFail($id);
            
            Excel::create('download', function ($excel) use($remessa)
            {
                
                $excel->setTitle('Remessa');
                
                $excel->sheet('alunos', function ($sheet) use($remessa)
                {
                    
                    $sheet->appendRow([
                        'Nome',
                        'Instituição de Entrega',
                        'Foto',
                        'Codigo W'
                    ]);
                    
                    foreach ($remessa->solicitacoes as $solicitacao) {
                        
                        $sheet->appendRow([
                            $solicitacao->usuario->nome,
                            $solicitacao->instituicao_entrega_id,
                            $solicitacao->foto,
                            $solicitacao->codigo_w
                        ]);
                    }
                    
                    $sheet->row(1, function ($row)
                    {
                        $row->setFontColor('#ffffff')
                            ->setBackground('#00458B');
                    });
                });
                
                $remessa->fill([
                    'baixado' => 1
                ])->save();
            })->download('xlsx');
        } catch (\Exception $e) {
            return Redirect::back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function anyEntregas()
    {        
        $select = [
            'remessas.id',
            'remessas.created_at as data_criacao',
            'responsavel.nome'
        ];

        $vars['entregas'] = Remessa::select($select)
                               ->join('solicitacoes_remessa AS sr', 'remessas.id', '=', 'sr.remessa_id')
                               ->join('solicitacoes AS s', 's.id', '=', 'sr.solicitacao_id')
                               ->join('usuarios AS u', 'u.id', '=', 's.usuario_id')
                               ->join('usuarios AS responsavel', 'responsavel.id', '=', 'remessas.usuario_id')
                               ->where('s.status_atual', '=', 5)
                               ->groupBy('remessas.id')
                               ->paginate(15);  
                               
        $vars['currentPage'] = (Input::get('page')) ? Input::get('page') : 1;
        return View::make('cliente.entregas', $vars);
    }

    public function getInfoRemessa($remessa, $pageBack)
    {
        $vars['remessa'] = zero_fill($remessa, 4);
        $vars['solicitacoes'] = Remessa::find($remessa)->solicitacoes()->paginate(15);
        $vars['pageBack'] = $pageBack;
        return View::make('cliente.info-remessa', $vars);
    }

    public function postAjaxConferirCartao()
    {
        $matricula = Input::get('matricula');
        $status = 6;

        $solicitacao = Usuario::whereMatricula($matricula)->with('ultimaSolicitacao')->first();

        if (!$solicitacao) {
            return Response::json([
                'status' => false,
                'message' => 'Aluno não encontrado.'
            ]);
        }

        $solicitacao_id = $solicitacao->ultimaSolicitacao->id;

        try {
            DB::transaction(function() use($solicitacao_id, $status){
                /**
                * Atualiza o status_atual de todas as solicitações
                */
                $solicitacao = Solicitacao::whereId($solicitacao_id)
                             ->whereStatusAtual(5)
                             ->update([
                                'status_atual' => $status
                             ]);

                /**
                * Cria um status na tabela solicitacoes_status
                */
                if (!$solicitacao) {
                    throw new Exception('Este aluno já foi conferido.');
                }

                SolicitacoesStatus::create([
                    'solicitacao_id' => $solicitacao_id,
                    'status_id' => $status,
                    'usuario_id' => Auth::user()->id
                ]);
            });
            return Response::json([
                'status' => true,
                'message' => 'Conferido com sucesso.'
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function anyRetirada()
    {
        return View::make('cliente.retirada');
    }

    public function postAjaxBuscarAluno()
    {
        $matricula = Input::get('matricula');
        
        $usuario = Usuario::whereMatricula($matricula)->first();
        
        if (! $usuario) {
            return Response::json([
                'status' => false,
                'message' => "O aluno de matrícula '{$matricula}' não foi encontrado!"
            ]);
        } else {
            $usuario = $usuario->toArray();
            $verificar = Solicitacao::whereUsuarioId($usuario['id'])->whereStatusAtual(6)->count();
            
            if (! $verificar) {
                return Response::json([
                    'status' => false,
                    'message' => "A carteira estudantil ainda não está disponível para entrega no campus!"
                ]);
            }

            $solicitacao = Usuario::whereMatricula($matricula)->first()->ultimaSolicitacao->toArray();

            return Response::json([
                'status' => true,
                'message' => '',
                'nome' => $usuario['nome'],
                'foto' => URL::to('imagens/' . $usuario['matricula'] . '/' . $solicitacao['foto']),
                'solicitacao_id' => $solicitacao['id']
            ]);
        }
    }

    public function postAjaxConfirmarRetirada()
    {
        $solicitacao_id = Input::get('solicitacao_id');
        $status_id = 7;
        
        try {
            DB::transaction(function () use($solicitacao_id, $status_id)
            {
                Solicitacao::whereId($solicitacao_id)->update([
                    'status_atual' => 7
                ]);
                
                SolicitacoesStatus::create([
                    'solicitacao_id' => $solicitacao_id,
                    'status_id' => $status_id,
                    'usuario_id' => Auth::user()->id
                ]);
            });
            return Response::json([
                'status' => true,
                'message' => 'Retirada do cartão registrada com sucesso.'
            ]);
        } catch (Exception $e) {
            return Response::json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function postAjaxDataGraph()
    {
        $solicitacoesNum = Solicitacao::select([
                                            'status_atual',
                                            DB::raw('COUNT(id) as count')
                                        ])
                                        ->groupBy('status_atual')
                                        ->get();
        return Response::json($solicitacoesNum);
    }

    public function anyInicial()
    {
        return View::make('cliente.incial');
    }

    public function getRelatorio($status = 'analise')
    {
        $vars['solicitacoes'] = [];
        switch ($status) {
            default:
            case 'analise':
                $vars['solicitacoes'] = Solicitacao::whereIn('status_atual', [1, 2])->paginate(15);
                $vars['title'] = 'Solicitações em análise';
                break;
            case 'fabricacao':
                $vars['solicitacoes'] = Solicitacao::whereIn('status_atual', [3, 4, 9, 10])->paginate(15);
                $vars['title'] = 'Cartões em fabricação';
                break;
            case 'expedido':
                $vars['solicitacoes'] = Solicitacao::whereIn('status_atual', [5])->paginate(15);
                $vars['title'] = 'Cartões que sairam para entrega';
                break;
            case 'disponível':
                $vars['solicitacoes'] = Solicitacao::whereIn('status_atual', [6])->paginate(15);
                $vars['title'] = 'Cartões disponíveis para entrega';
                break;
            case 'entregue':
                $vars['solicitacoes'] = Solicitacao::whereIn('status_atual', [7])->paginate(15);
                $vars['title'] = 'Cartões entregues';
                break;
            case 'reprovada':
                $vars['solicitacoes'] = Solicitacao::whereIn('status_atual', [8])->paginate(15);
                $vars['title'] = 'Solicitações com foto reprovada';
                break;
        }

        return View::make('cliente.relatorio', $vars);
    }

    public function getConferir($pageBack = 1, $remessa = null)
    {
        $vars['remessa'] = $remessa;
        $vars['pageBack'] = $pageBack;
        $select = [
            'solicitacoes.id as id',
            'solicitacoes.foto as foto',
            'aluno.matricula as matricula',
            'aluno.curso as curso',
            'aluno.nome as nome',
            'solicitacoes.created_at as created_at',
            'solicitacoes.status_atual as status_atual'
        ];
        $vars['solicitacoes'] = Solicitacao::select($select)
                                             ->join('solicitacoes_remessa as sr', 'sr.solicitacao_id', '=', 'solicitacoes.id')
                                             ->join('remessas as r', 'r.id', '=', 'sr.remessa_id')
                                             ->join('usuarios as aluno', 'aluno.id', '=', 'solicitacoes.usuario_id')
                                             ->where('r.id', '=', $remessa)
                                             ->get();

        return View::make('cliente.conferir', $vars);
    }

    public function getPesquisarSolicitacao()
    {
        return View::make('cliente.pesquisar_solicitacao');
    }

    public function postPesquisarSolicitacao()
    {
        $matricula = Input::get('matricula');

        $usuario = Usuario::whereMatricula($matricula)->first();
        $vars['usuario'] = $usuario;
        if (count($usuario)) {
            if (count($usuario->ultimaSolicitacao)) {
                $vars['solicitacoes'] = Solicitacao::whereUsuarioId($usuario->id)->orderBy('id', 'DESC')->get();
            } else {
                $vars['error'] = "O aluno de matrícula <b>{$matricula}</b> ainda não realizou uma solicitação.";
            }
        } else {
            $vars['error'] = "O aluno de matrícula <b>{$matricula}</b> não encontrado.";
        }

        $vars['letters'] = [
            'A',
            'B',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
        ];

        return View::make('cliente.pesquisar_solicitacao', $vars);
    }

    public function postAjaxPesquisarSolicitacao()
    {
        $matricula = Input::get('matricula');

        $usuario = Usuario::whereMatricula($matricula)->first();
        if (count($usuario)) {
            if (count($usuario->ultimaSolicitacao)) {
                $usuario->status = true;
                $usuario->foto = URL::to('imagens/' . $usuario->matricula . '/' . $usuario->ultimaSolicitacao->foto);
                foreach ($usuario->ultimaSolicitacao->solicitacoesStatus as $k => $v) {
                    $usuario->ultimaSolicitacao->solicitacoesStatus[$k]->status;
                    $usuario->ultimaSolicitacao->solicitacoesStatus[$k]->data = date('d/m/Y H:i:s', strtotime($usuario->ultimaSolicitacao->solicitacoesStatus[$k]->created_at));
                }                
                return Response::json($usuario);
            } else {
                return Response::json([
                    'status' => false,
                    'message' => "O aluno de matrícula {$matricula} ainda não realizou uma solicitação."
                ]);
            }
        } else {
            return Response::json([
                'status' => false,
                'message' => "O aluno de matrícula {$matricula} não encontrado."
            ]);
        }
    }

    public function anyCadastrarColaborador()
    {
        if (Request::isMethod('post') && Input::hasFile('excel')) {
            
            $fileInstance = Input::file('excel');
            
            $extension = strtolower($fileInstance->getClientOriginalExtension());
            
            if (! preg_match('/\.(xls|xlsx|csv)$/i', $fileInstance->getClientOriginalName())) {
                return Redirect::back()->withErrors([
                    'message' => 'A extensão de arquivo é inválido'
                ]);
            }
            
            $tmpFilename = $fileInstance->getRealPath();
            
            try {
                
                Excel::load($tmpFilename, function ($reader)
                {
                    
                    $requiredFields = [
                        'cpf',
                        'nome',
                    ];
                    
                    $reader = $reader->select($requiredFields)->all();
                    
                    $fields = array_keys($reader->first()->toArray());
                    
                    if ($missingFields = array_diff($requiredFields, $fields)) {
                        
                        throw new \UnexpectedValueException(sprintf(
                            'Alguns campos não foram incluídos no documento.A saber: %s', 
                            implode(', ', $missingFields))
                        );
                    }
                    
                    $data = $reader->toArray();
                    
                    $separated = [
                        'not'      => [],
                        'yes'      => [],
                        'hasEmpty' => []
                    ];
                    
                    DB::transaction(function () use($data, &$separated)
                    {
                        
                        foreach ($data as $userData) {
                            
                            // Verifica se contém valores vazios
                            if (in_array(false, $userData)) {

                                $separated['hasEmpty'][] = $userData;
                                continue;

                            }
                            
                            $query = Usuario::whereCpf($userData['cpf']);
                            
                            if ($query->count()) {

                                $separated['not'][] = $userData;

                                $user = $query->first();

                            } else {

                                $userData['nivel_id'] = 12;
                                
                                $userData['username'] = uniqid(strtolower(array_get(explode(' ', $userData['nome']), 0)));
                                
                                $userData['senha'] = Hash::make((string) mt_rand(10000, 99999));

                                $user = Usuario::create($userData);

                                $separated['yes'][] = $userData;
                            }


                            Credito::create([
                                'status'     => '0',
                                'usuario_id' => $user->id,
                            ]);
                        }
                    });
                    
                    return Redirect::back()->with([

                        'messageSuccess' => 'Os registros foram inseridos com successo',
                        'uploadedData'   => $separated
                    ]);

                }, 'UTF-8');

            } catch (\Exception $e) {

                return Redirect::back()->withErrors([
                    'message' => $e->getMessage()
                ]);

            }
        }


        return View::make('cliente.enviar_carga');
    }
}   
