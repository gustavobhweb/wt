<?php

class UsuarioInstituicao extends Eloquent
{

    protected $table = 'usuarios_instituicao';

    public function instituicao()
    {
        return $this->belongsTo('Instituicao');
    }
}