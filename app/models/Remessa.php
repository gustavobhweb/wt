<?php

class Remessa extends Eloquent
{

    protected $fillable = [
        'deletado',
        'usuario_id',
        'baixado',
        'modelo_cartao_id'
    ];

    public function solicitacoes()
    {
        return $this->belongsToMany('Solicitacao', 'solicitacoes_remessa');
    }

    public function instituicaoEntrega()
    {
        try {
            return $this->solicitacoes()->first()->instituicao();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function usuario()
    {
        return $this->belongsTo('Usuario');
    }

    public function solicitacoesUsuario()
    {
        return $this->solicitacoes()->with('usuario');
    }

    public function protocolo()
    {
        return $this->hasOne('Protocolo');
    }

    public function modeloCartao()
    {
        return $this->belongsTo('ModeloCartao');
    }

    public function tipoCartao()
    {
        return $this->modeloCartao()->with('tipoCartao');
    }

    public function tipoEntrega()
    {
        return $this->modeloCartao()->with('tipoEntrega');
    }

    public static function gerarProtocoloPdf($id)
    {
        $remessa = Remessa::whereId($id)
                            ->with('solicitacoesUsuario', 'usuario', 'modeloCartao', 'tipoCartao', 'tipoEntrega')
                            ->first();
        
        return PDF::loadView('elements.producao.pdf_protocolo', compact('remessa'))->setPaper('a4')->stream();
    }

    public function responsavelStatus($status)
    {
       try {

           return $this->solicitacoes()
                       ->first()
                       ->solicitacoesStatus()
                       ->where('status_id', '=', $status)
                       ->first()
                       ->usuario;

       } catch (Exception $e) {
           return null;
       }
    }
}


