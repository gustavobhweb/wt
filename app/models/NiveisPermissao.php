<?php

class NiveisPermissao extends Eloquent
{
	protected $table = "niveis_permissoes";

	protected $fillable = [
		'nivel_id',
		'permissao_id',
		'is_home'
	];

	public function permissao()
	{
		return $this->belongsTo('Permissao');
	}
}