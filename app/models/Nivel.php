<?php

class Nivel extends Eloquent
{
    protected $table = 'niveis';

    protected $fillable = [
    	'titulo'
    ];

    public function permissoes()
    {
    	return $this->belongsToMany('Permissao', 'niveis_permissoes');
    }
}
