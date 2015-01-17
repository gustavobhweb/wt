<?php

class SolicitacoesRemessa extends Eloquent
{

    protected $table = 'solicitacoes_remessa';

    protected $fillable = [
        'solicitacao_id',
        'remessa_id'
    ];

    public function remessa()
    {
        return $this->belongsTo('Remessa');
    }

    public function solicitacao()
    {
        return $this->belongsTo('Solicitacao');
    }
}