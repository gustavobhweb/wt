<?php

class Permissao extends Eloquent
{
	protected $table = 'permissoes';

	protected $fillable = [
        'name',
        'action',
        'type',
        'url',
        'glyphicon',
        'in_menu'
    ];
}