<?php

class ExpedicaoController extends BaseController
{

    public function getIndex()
    {
        $select = [
            'remessas.id',
            'remessas.created_at as data_criacao',
            'responsavel.nome'
        ];
        
        $vars['entregas'] = Remessa::select($select)->join('solicitacoes_remessa AS sr', 'remessas.id', '=', 'sr.remessa_id')
            ->join('solicitacoes AS s', 's.id', '=', 'sr.solicitacao_id')
            ->join('usuarios AS u', 'u.id', '=', 's.usuario_id')
            ->join('usuarios AS responsavel', 'responsavel.id', '=', 'remessas.usuario_id')
            ->where('s.status_atual', '=', 10)
            ->groupBy('remessas.id')
            ->paginate(15);
           
	    $vars['currentPage'] = (Input::get('page')) ? Input::get('page') : 1;

		return View::make('expedicao.index', $vars);
	}

	public function getInfoRemessa($remessa, $pageBack)
	{
		$vars['remessa'] = zero_fill($remessa, 4);
		$vars['solicitacoes'] = Remessa::findOrFail($remessa)->solicitacoes()->paginate(15);
		$vars['pageBack'] = $pageBack;
		return View::make('expedicao.info-remessa', $vars);
	}

	public function postAjaxSairParaEntrega()
	{
		$remessa = Input::get('remessa');
		$status = 5;

		try {

			DB::transaction(function() use($remessa, $status) {
		
				$solicitacoesIDs = Remessa::find($remessa)
										->solicitacoes()
										->lists('solicitacao_id');

				/**
				* Atualiza o status_atual de todas as solicitações
				*/
				Solicitacao::whereIn('id', $solicitacoesIDs)->update([
					'status_atual' => $status
				]);

				/**
				* Cria um status na tabela solicitacoes_status
				*/
				foreach ($solicitacoesIDs as $solicitacaoId) {
					SolicitacoesStatus::create([
						'solicitacao_id' => $solicitacaoId,
						'status_id' => $status,
						'usuario_id' => Auth::user()->id
					]);
				}
			});

			return Response::json([
				'status' => true,
				'message' => 'Alterado com sucesso.'
			]);

		} catch(\Exception $e) {
			return Response::json([
				'status' => false,
				'message' => $e->getMessage()
			]);
		}
	}

	public function anyHistorico()
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
	                           ->whereIn('s.status_atual', [5,6,7])
	                           ->groupBy('remessas.id')
	                           ->paginate(15);

	    $vars['currentPage'] = (Input::get('page')) ? Input::get('page') : 1;

		return View::make('expedicao.historico', $vars);
	}

}