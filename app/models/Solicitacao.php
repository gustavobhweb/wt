<?php

class Solicitacao extends Eloquent
{

    protected $table = 'solicitacoes';

    protected $fillable = [
        'usuario_id',
        'credito_id',
        'foto',
        'instituicao_entrega_id',
        'status_atual'
    ];


    public function solicitacoesStatus()
    {
        return $this->hasMany('SolicitacoesStatus', 'solicitacao_id');
    }

    public function usuario()
    {
        return $this->belongsTo('Usuario');
    }

    public function status()
    {
        return $this->belongsTo('Status', 'status_atual');
    }

    public function solicitacaoRemessa()
    {
        return $this->belongsTo('SolicitacoesRemessa', 'id', 'solicitacao_id');
    }

    public function credito()
    {
        return $this->belongsTo('Credito');
    }

    public function instituicao()
    {
        return $this->belongsTo('Instituicao', 'instituicao_entrega_id');
    }
}