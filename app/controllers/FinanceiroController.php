<?php

class FinanceiroController extends BaseController
{

    public function anyIndex()
    {
        $select = [
            'remessas.id',
            'remessas.created_at as data_criacao',
            'responsavel.nome'
        ];
        $vars['remessas'] = Remessa::select($select)->join('solicitacoes_remessa AS sr', 'remessas.id', '=', 'sr.remessa_id')
            ->join('solicitacoes AS s', 's.id', '=', 'sr.solicitacao_id')
            ->join('usuarios AS u', 'u.id', '=', 's.usuario_id')
            ->join('usuarios AS responsavel', 'responsavel.id', '=', 'remessas.usuario_id')
            ->where('s.status_atual', '=', 9)
            ->groupBy('remessas.id')
            ->paginate(15);
        $vars['currentPage'] = (Input::get('page')) ? Input::get('page') : 1;
        return View::make('financeiro.index', $vars);
    }

    public function getInfoRemessa($remessa, $pageBack, $pageBackUrl = false)
    {
        $vars['remessa'] = zero_fill($remessa, 4);
        $vars['solicitacoes'] = Remessa::find($remessa)->solicitacoes()->paginate(15);
        $vars['pageBack'] = $pageBack;
        $vars['pageBackUrl'] = ($pageBackUrl) ? 'financeiro/historico' : 'financeiro';
        return View::make('financeiro.info-remessa', $vars);
    }

    public function postAjaxLiberar()
    {
        $remessa = Input::get('remessa');
        $status = 3;
        
        try {
            DB::transaction(function () use($remessa, $status)
            {
                
                $solicitacoesIDs = Remessa::find($remessa)->solicitacoes()->lists('solicitacao_id');
                
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
				'status' => 'true',
				'message' => 'Alterado com sucesso.'
			]);
		} catch(\Exception $e) {
			return Response::json([
				'status' => 'false',
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

		$vars['remessas'] = Remessa::select($select)
	                           ->join('solicitacoes_remessa AS sr', 'remessas.id', '=', 'sr.remessa_id')
	                           ->join('solicitacoes AS s', 's.id', '=', 'sr.solicitacao_id')
	                           ->join('solicitacoes_status AS ss', 'ss.solicitacao_id', '=', 's.id')
	                           ->join('usuarios AS u', 'u.id', '=', 's.usuario_id')
	                           ->join('usuarios AS responsavel', 'responsavel.id', '=', 'ss.usuario_id')
	                           ->where('s.status_atual', '!=', 9)
	                           ->where('s.status_atual', '!=', 2)
	                           ->groupBy('remessas.id')
	                           ->paginate(15);

	    $vars['currentPage'] = (Input::get('page')) ? Input::get('page') : 1;

		return View::make('financeiro.historico', $vars);
	}
    
}