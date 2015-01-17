<?php

class ProducaoController extends BaseController
{
    public function getGerenciarFotos($action = null)
    {
        $solicitacoes = Solicitacao::with('usuario')->whereStatusAtual(2)->get();

        return View::make('producao/gerenciar_fotos', compact('solicitacoes'));
    }

    public function postGerenciarFotos()
    {
        $action = Input::get('action');

        if (Input::has('solicitacoes')) {

            $grupoSolicitacoes = (array) Input::get('solicitacoes');

            try {

                switch ($action) {
                    case 'aprovar':
                        return $this->aprovarFotos($grupoSolicitacoes);
                        break;
                    case 'reprovar':
                        return $this->reprovarFotos($grupoSolicitacoes);
                        break;
                    default:
                        throw new \InvalidArgumentException('Parâmetro inválido');
                        break;
                }
            } catch (\Exception $e) {
                return Redirect::back()->withErrors([
                    'message' => $e->getMessage()
                ]);
            }
        }

        return Redirect::back()->withErrors([
            'message' => 'Nenhuma soliciação foi marcada para criar a remessa'
        ]);
    }

    /**
     * Quando a foto é aprovada, o status é alterado para "9 - Financeiro"
     */
    protected function aprovarFotos(array $grupoSolicitacoes)
    {
        try {

            return DB::transaction(function () use($grupoSolicitacoes)
            {

                $auth = Auth::user();

                // Atualiza as solicitações encontradas no grupo

                Solicitacao::whereIn('id', array_flatten($grupoSolicitacoes))
                            ->update(['status_atual' => 9]);


                foreach($grupoSolicitacoes as $grupoNiveis) {

                    foreach ($grupoNiveis as $solicitacoes) {


                        $remessa = Remessa::create([
                            'baixado'          => 0,
                            'deletado'         => 0,
                            'modelo_cartao_id' => 1,
                            'usuario_id'       => $auth->id
                        ]);

                        foreach ($solicitacoes as $solicitacao) {

                            SolicitacoesStatus::create([
                                'solicitacao_id' => $solicitacao,
                                'status_id'      => 9,
                                'usuario_id'     => $auth->id,

                            ]);

                            SolicitacoesRemessa::create([
                                'remessa_id'     => $remessa->id,
                                'solicitacao_id' => $solicitacao,
                                'usuario_id'     => $remessa->usuario_id
                            ]);

                            Aviso::solicitacao($solicitacao, 'Sua foto foi aprovada com sucesso', 'Aprovação da foto');
                        }
                    }
                }




                return Redirect::action('ProducaoController@getGerenciarFotos')->withMessage(sprintf('%s remessa(s) foram cadastradas com sucesso', count($grupoSolicitacoes)));
            });
        } catch (\Exception $e) {
            return Redirect::action('ProducaoController@getGerenciarFotos')->withMessage($e->getMessage());
        }
    }

    protected function reprovarFotos(array $solicitacoes)
    {
        $status = 8;

        try {

            return DB::transaction(function () use($solicitacoes, $status)
            {

                Solicitacao::whereIn('id', $solicitacoes)->update([
                    'status_atual' => $status
                ]);

                foreach ($solicitacoes as $solicitacao) {

                    $solicitacoesStatus = SolicitacoesStatus::create([

                        'solicitacao_id'    => $solicitacao,
                        'status_id'         => $status,
                        'usuario_id'        => Auth::user()->id
                    ]);

                    $solicitacoesStatus->solicitacao->credito->fill([
                        'status' => 0
                    ])->save();
                }

                return Response::json(sprintf('%s solicitações foram reprovadas', count($solicitacoes)));
            });
        } catch (\Exception $e) {
            return Response::json($e->getMessage());
        }
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

    public function anyBaixarCarga()
    {
        $select = [
            'remessas.created_at AS data_criacao',
            'remessas.id',
            'remessas.usuario_id'
        ];

        $remessas = Remessa::select($select)
                            ->join('solicitacoes_remessa AS sr', 'remessas.id', '=', 'sr.remessa_id')
                            ->join('solicitacoes AS s', 's.id', '=', 'sr.solicitacao_id')
                            ->where('s.status_atual', '=', 3)
                            ->groupBy('remessas.id')
                            ->orderBy('remessas.id', 'DESC')
                            ->paginate(15);

        return View::make('producao.baixar_carga', compact('remessas'));
    }

    public function getDownloadExcelRemessa($id = null)
    {
        try {

            $remessa = Remessa::findOrFail($id);

            $downloadName = sprintf('remessa_%04s', $id);

            Excel::create($downloadName, function ($excel) use($remessa)
            {

                $excel->setTitle('Remessa');

                $excel->sheet('alunos', function ($sheet) use($remessa)
                {
                    $sheet->appendRow([
                        'Aluno',
                        'Matricula',
                        'Curso',
                        'Foto',
                        'Turno'
                    ]);

                    foreach ($remessa->solicitacoes as $solicitacao) {

                        $usuario = $solicitacao->usuario;

                        $sheet->appendRow([
                            $usuario->nome,
                            $usuario->matricula,
                            $usuario->curso,
                            $solicitacao->foto,
                            $usuario->turno
                        ]);

                        $usuario = $solicitacao = null;
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

    public function getDownloadFotosRemessa($id = null)
    {
        $solicitacoes = Remessa::find($id)->solicitacoes;

        try {

            $zip = new \ZipArchive();

            $zipName = tempnam(sys_get_temp_dir(), 'remessa_');

            if ($zip->open($zipName, ZipArchive::CREATE) === true) {

                $filesWithProblems = [];

                $dir = public_path('imagens');

                $remessaId = zero_fill($id, 4);

                foreach ($solicitacoes as $solicitacao) {

                    $filename = $dir . "/{$solicitacao->usuario->matricula}/{$solicitacao->foto}";
                    $filenameInZip = "$remessaId/$solicitacao->foto";

                    if (file_exists($filename)) {
                        $zip->addFile($filename, $filenameInZip);
                    } else {
                        $filesWithProblems[] = $filename;
                    }
                }

                if (($count = count($filesWithProblems)) > 0) {

                    $errorMessage = $count . ' foto não foram encontrados no sistema:' . PHP_EOL . implode(PHP_EOL, $filesWithProblems);

                    $zip->addFromString('log_de_erros.txt', $errorMessage);
                }

                $zip->close();

                return Response::download($zipName, "remessa_{$remessaId}.zip");
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getListaRemessasConferencia()
    {
        $select = [
            'remessas.created_at AS data_criacao',
            'remessas.id',
            'responsavel.nome AS responsavel'
        ];


        $remessas = Remessa::select($select)
                            ->join('solicitacoes_remessa AS sr', 'remessas.id', '=', 'sr.remessa_id')
                            ->join('solicitacoes AS s', 's.id', '=', 'sr.solicitacao_id')
                            ->join('usuarios AS responsavel', 'responsavel.id', '=', 'remessas.usuario_id')
                            ->where('s.status_atual', '=', 4)
                            ->groupBy('remessas.id')
                            ->orderBy('remessas.id', 'DESC')
                            ->paginate(15);

        return View::make(
            'producao.lista_remessas_conferencia',
            compact('remessas')
        );
    }

    public function getConferirRemessa($id = null)
    {
        try {

            $remessa = Remessa::findOrFail($id);
            $solicitacoes = $remessa->solicitacoes()
                ->whereIn('status_atual', [
                4,
                10
            ])
                ->get();
        } catch (\Exception $e) {}

        return View::make('producao.conferir_remessa', compact('solicitacoes', 'remessa'));
    }

    public function postConferirRemessa($id = null)
    {
        $allowedExtensions = [
            'xls',
            'xlsx'
        ];

        try {

            if (! Input::hasFile('excel')) {
                throw new \UnexpectedValueException('Nenhum arquivo foi selecionado');
            }

            $file = Input::file('excel');

            $tmpFilename = $file->getRealPath();

            if (! in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
                throw new \InvalidArgumentException('Extensão de arquivo é inválida');
            }

            Excel::load($tmpFilename, function ($reader) use(&$missingInserts)
            {

                $requiredFields = [
                    'matricula',
                    'codigo_w'
                ];

                $reader = $reader->select($requiredFields)->all();

                $fields = array_keys($reader->first()->toArray());

                $lines = $reader->toArray();

                if ($missingFields = array_diff($requiredFields, $fields)) {

                    throw new \UnexpectedValueException(sprintf('Alguns campos não foram incluídos no documento.A saber: %s', implode(', ', $missingFields)));
                }

                $missingInserts = [];

                DB::transaction(function () use($lines, &$missingInserts)
                {

                    $status = 10;

                    foreach ($lines as $line) {

                        $solicitacoes = DB::table('solicitacoes AS s')->select([
                            's.id'
                        ])
                            ->join('usuarios AS u', 'u.id', '=', 's.usuario_id')
                            ->where('u.matricula', '=', $line['matricula'])
                            ->where('s.status_atual', '=', 4);

                        if ($result = $solicitacoes->first()) {

                            Solicitacao::whereId($result->id)->update([
                                'status_atual' => $status,
                                'codigo_w' => $line['codigo_w']
                            ]);

                            SolicitacoesStatus::create([
                                'solicitacao_id' => $result->id,
                                'status_id'      => $status,
                                'usuario_id'     => Auth::user()->id
                            ]);
                        } else {

                            $missingInserts[] = $line['matricula'];
                        }
                    }
                });

                if (($countLines = count($missingInserts)) > 0) {
                    throw new \Exception("Os dados foram conferidos, porém {$countLines} linhas não foram processadas");
                }
            });

            return Redirect::back()->with([
                'successMessage' => 'Os dados foram conferidos com sucesso'
            ]);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors([
                'message' => (string) $e
            ]);
        }
    }

    public function postAjaxEnviarParaConferencia()
    {
        try {

            if (! Input::has('remessa_id')) {
                throw new \UnexpectedValueException('O parâmetro "remessa_id" é obrigatório para essa requisição');
            }

            $remessaId = Input::get('remessa_id');

            $solicitacoesIDs = Remessa::findOrFail($remessaId)->solicitacoes()->lists('solicitacao_id');

            DB::transaction(function () use($solicitacoesIDs)
            {

                $status = 4;

                Solicitacao::whereIn('id', $solicitacoesIDs)->update([
                    'status_atual' => $status
                ]);

                foreach ($solicitacoesIDs as $solicitacaoId) {

                    SolicitacoesStatus::create([
                        'status_id'      => $status,
                        'solicitacao_id' => $solicitacaoId,
                        'usuario_id'     => Auth::user()->id
                    ]);
                }
            });

            return Response::json([
                'message' => 'A remessa foi enviada para conferência',
                'solicitacoes' => $solicitacoesIDs,
                'error' => false
            ]);
        } catch (\Exception $e) {

            return Response::json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function postAjaxReprovarFoto()
    {
        try {

            $reprovacao = $this->reprovarFotos([
                Input::get('solicitacao_id')
            ]);

            Aviso::solicitacao(Input::get('solicitacao_id'), Input::get('motivo'), 'Foto reprovada');

            if ($reprovacao) {
                return Response::json([
                    'status' => true,
                    'message' => $reprovacao
                ]);
            }
        } catch (\Exception $e) {

            return Response::json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAjaxVerificarImpressaoProtocolo()
    {
        try {

            $id = filter_var(Input::get('remessa_id'));

            $protocolo = Protocolo::whereRemessaId($id);

            $auth = Auth::user();

            if (! $protocolo->count()) {

                $protocolo = Protocolo::create([
                    'usuario_id' => $auth->id,
                    'remessa_id' => $id
                ]);
            } else {

                if ($protocolo->pluck('usuario_id') != $auth->id) {
                    throw new Exception('Você não pode trabalhar nessa remessa');
                }
            }

            return Response::json([
                'error' => false
            ]);
        } catch (\Exception $e) {

            return Response::json([
                'error' => $e->getMessage()
            ]);
        }
    }


    public function getImprimirProtocolo($id)
    {
        $protocolo = Protocolo::whereRemessaId($id)->whereUsuarioId(Auth::user()->id)->count();
        
        if ($protocolo) {
            return Remessa::gerarProtocoloPdf($id);
        } else {
            return App::abort(403, 'Acesso não autorizado');
        }
    }

    public function getImprimirProtocoloRemessa($id)
    {
        return Remessa::gerarProtocoloPdf($id);
    }

    public function postAjaxRotacionar()
    {
        $im = imagecreatefrompng(Input::get('imgsrc'));
        $angle = -1 * Input::get('angle');

        $dest = imagerotate($im, $angle, 0xFFFFFF);

        $index = Str::length(URL::to('/'));
        $dir = substr(Input::get('imgsrc'), $index);
        imagepng($dest, public_path($dir));
        return Response::json([
            'status' => 1,
            'message' => 'Sucesso.'
        ]);
    }

    public function postAjaxCrop()
    {
        $im = imagecreatefrompng(Input::get('image'));

        $index = Str::length(URL::to('/'));
        $dir = substr(Input::get('image'), $index);

        $indexQr = strpos($dir, "?");
        $dir = substr($dir, 0, $indexQr);

        $x = Input::get('x');
        $y = Input::get('y');
        $w = Input::get('w');
        $h = Input::get('h');

        list($width, $height) = getimagesize(Input::get('image'));
        $nh = 215;
        $nw = ($nh * $width) / $height;

        $widthVar = 358;
        $heightVar = 478;
        $dest = imagecreatetruecolor($widthVar, $heightVar);

        imagecopyresampled($dest, $im, 0, 0, $x, $y, $widthVar, $heightVar, $w, $h);
        imagepng($dest, public_path($dir));
        imagedestroy($dest);

        return Response::json([
            'status' => true,
            'message' => 'Cadastrado com sucesso.',
            'image' => URL::to($dir . '?' . time())
        ]);
    }

    public function getGraphView()
    {
        $vars['solicitacoes'] = Solicitacao::all();

        return View::make('producao.graph_view', $vars);
    }


    public function getProtocolos()
    {
        $protocolos = Protocolo::whereUsuarioId(Auth::user()->id)
                                ->paginate(15);

        return View::make('producao.protocolos', get_defined_vars());
    }

    public function getPesquisarRemessa()
    {

        if (Input::has('remessa_id')) {

            try {
                $id = Input::get('remessa_id');
                $solicitacoes = Remessa::findOrFail($id)->solicitacoes;

            } catch (Exception $e) {
               $message = 'Nenhum resultado encontrado';
               return Redirect::action('ProducaoController@getPesquisarRemessa')
                                ->withErrors(compact('message'));
            }
        }

        return View::make('producao.pesquisar_remessa', get_defined_vars());
    }

    public function anyPesquisarRemessas()
    {
        $select = [
            'remessas.id as id',
            'i.endereco as endereco',
            'i.nome as instituicao',
            'status.titulo as status',
            'status.id as status_id'
        ];

        $remessaSearch = '%%';
        $typeSearch = 1;
        if (Request::isMethod('post')) {
            $typeSearch = Input::get('txt-type-search');
            $remessaSearch = Input::get('txt-search');
            $remessaSearch1 = Input::get('txt-search1');
        }

        $remessas = Remessa::select($select)
                            ->join('solicitacoes_remessa as sr', 'sr.remessa_id', '=', 'remessas.id')
                            ->join('solicitacoes as s', 's.id', '=', 'sr.solicitacao_id')
                            ->join('instituicoes as i', 'i.id', '=', 's.instituicao_entrega_id')
                            ->join('status', 'status.id', '=', 's.status_atual');
        if ($typeSearch == 1) {
            $remessas->where('remessas.id', 'LIKE', $remessaSearch);
        } else {
            $remessas->whereBetween('remessas.id', [$remessaSearch, $remessaSearch1]);
        }

        $vars['remessas'] = $remessas->groupBy('remessas.id')
                            ->paginate(15);
        
        return View::make('producao.pesquisar_remessas', $vars);
    }

    public function getPdfRelatorioRemessa($remessa)
    {
        $vars['solicitacoes'] = Solicitacao::where('sr.remessa_id', '=', $remessa)
                                ->join('solicitacoes_remessa as sr', 'sr.solicitacao_id', '=', 'solicitacoes.id')
                                ->groupBy('sr.solicitacao_id')
                                ->get();
    
        return PDF::loadView('elements.producao.pdf_relatorio_remessa', $vars)
                    ->setPaper('a4')
                    ->stream();
    }


    

}