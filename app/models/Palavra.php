<?php

class Palavra extends Eloquent
{
    protected $table = 'palavras';

    protected $fillable = [
        'palavra'
    ];

    public function frases()
    {
    	return $this->belongsToMany('Frase', 'frases_palavras');
    }
}