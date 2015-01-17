<?php

class SolicitacoesStatus extends Eloquent
{
    protected $table = 'solicitacoes_status';

    protected $fillable = ['solicitacao_id', 'status_id', 'usuario_id'];

    public function status()
    {
        return $this->belongsTo('Status');
    }

    public function solicitacao()
    {
        return $this->belongsTo('Solicitacao');
    }

    public function usuario()
    {
        return $this->belongsTo('Usuario');
    }
}