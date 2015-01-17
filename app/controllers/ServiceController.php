<?php

class ServiceController extends BaseController
{
    public function __construct()
    {
        if (Auth::user()->username !== 'newtonuserapi') {
            return App::abort(403);
        }
    }

    public function getGet($remessa_id)
    {
        $select = [
            'u.matricula as matricula',
            'u.nome as nome',
            'u.turno as turno',
            'u.curso as curso',
            'solicitacoes.codigo_w as codigo_w',
        ];

        $solicitacoes = Solicitacao::select($select)
                              ->where('sr.remessa_id', '=', $remessa_id)
                              ->join('solicitacoes_remessa as sr', 'sr.solicitacao_id', '=', 'solicitacoes.id')
                              ->join('solicitacoes as s', 's.id', '=', 'sr.solicitacao_id')
                              ->join('usuarios as u', 'u.id', '=', 's.usuario_id')
                              ->get();

        return Response::json($solicitacoes);
    }

    public function postSet()
    {
        $alunos = Input::get('alunos');

        foreach ($alunos as $aluno) {
            unset($aluno['instituicao']);
            $aluno['nivel_id'] = 1;
            Usuario::create($aluno);
        }
    }
}