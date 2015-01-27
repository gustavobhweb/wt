<?php

class Conhecimento extends Eloquent 
{
	protected $table = 'conhecimentos';

	protected $fillable = [
		'termo',
		'definicao'
	];
}