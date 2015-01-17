<?php

class ModeloCartao extends Eloquent
{

    protected $table = 'modelos_cartao';

    protected $fillable = [
        'impressao_termica',
        'nome',
        'tem_cv',
        'tem_furo',
        'tipo_cartao_id',
        'tipo_codigo_cartao_id'
    ];

    public function tipoCartao()
    {
        return $this->belongsTo('TipoCartao');
    }

    public function tipoEntrega()
    {
        return $this->belongsTo('TipoEntrega');
    }
}