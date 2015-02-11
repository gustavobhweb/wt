<?php

class FumecController extends Controller
{
	public function anyExercicios()
	{
		$vars = [];
		return View::make('fumec.exercicios', $vars);
	}

}