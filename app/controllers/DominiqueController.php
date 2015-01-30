<?php

class DominiqueController extends Controller
{
	private $IA = 'Dominique';
	private $me = 'Gustavo';

	private $fraseContext;

	public function anyInicio()
	{
		if (Request::isMethod('post')) {
			$answer = $this->process(Input::get('frase'))->answer();
		}

		return View::make('dominique.inicio', get_defined_vars());
	}

	/**
	* Método responsável por processaar a fala
	*/
	private function process($frase)
	{
		$fraseArray = explode(' ', $frase);

		/**
		* Verifica e salva frases
		*/
		$fraseObj = Frase::whereFrase($frase);
		if (!$fraseObj->count()) {
			$fraseObj = Frase::create([
				'frase' => $frase
			]);
		} else {
			$fraseObj = $fraseObj->first();
		}

		/**
		* Verifica e salva palavras e relação da palavra com a frase
		*/
		foreach ($fraseArray as $palavra) {

			$query = Palavra::wherePalavra($palavra)->first();
			if (!$query || !$query->count()) {
				$query = Palavra::create([
					'palavra' => str_replace('?', '', $palavra)
				]);
			}

			if (!$query->frases()->whereId($fraseObj->id)->count()) {
				$query->frases()->attach($fraseObj->id);
			}
		}

		/**
		* Salva frase no contexto
		*/

		$this->fraseContext = $fraseObj;

		return $this;
	}

	private function answer()
	{
		$frase = $this->me . ', não entendi';

		$fraseObj = $this->fraseContext;
		$fraseArray = explode(' ', $fraseObj->frase);
		if ((in_array('o', $fraseArray) && in_array('que', $fraseArray)) || (in_array('fala', $fraseArray) || in_array('fale', $fraseArray) && (in_array('sobre', $fraseArray) || in_array('de', $fraseArray)))) {
			$frase = $this->knowledge($fraseObj->frase);
		} elseif (in_array(str_replace(' ', '', str_replace(strtolower($this->me), '', $fraseObj->frase)), ['oi', 'olá', 'ola', 'oii', 'oiii', 'hi', 'hello'])) {
			$fraseArrRand = [
				'Oi ' . $this->me,
				'Olá',
				'Olá ' . $this->me,
				'Oii ' . $this->me
			];
			shuffle($fraseArrRand);
			$frase = $fraseArrRand[0];
		}

		return $frase;
	}

	private function knowledge($param)
	{
		$param = str_replace('o que', '', $param);
		$param = str_replace('é', '', $param);
		$param = str_replace('me fala sobre', '', $param);
		$param = str_replace('me fala de', '', $param);
		$param = str_replace('me fale sobre', '', $param);
		$param = str_replace('me fale de', '', $param);
		$param = str_replace('fala sobre', '', $param);
		$param = str_replace('fala de', '', $param);
		$param = str_replace('fale sobre', '', $param);
		$param = str_replace('fale de', '', $param);
		$param = str_replace(' ', '+', $param);
		$param = str_replace('?', '', $param);
		$param = str_replace('.', '', $param);
		$param = str_replace(',', '', $param);
		$param = str_replace(';', '', $param);
		$param = str_replace('um', '', $param);
		$param = str_replace('uma', '', $param);

		$param = strtolower($param);
		$param = str_replace(strtolower($this->IA), '', $param);
		$paramS = str_replace('+', '', $param);
		$frase = 'Ainda não sei o que é &ldquo;' . $paramS . '&rdquo;. Se ficar sabendo, me fala.';
		
		try
		{
			/**
			* Tenta encontrar na Wikipedia
			*/
			
			$results = $this->wiki($param);
			$results = (array)$results;
			$results = reset($results);

			if (!count($results) || !$results || is_null($results) || !isset($results->extract)) {
				$conhecimento = Conhecimento::whereTermo($paramS);
				if ($conhecimento->count()) {
					$frase = $conhecimento->first()->definicao;
				}
				$palavraKnown = Palavra::wherePalavra($paramS);
				if (!$palavraKnown->count()) {
					Palavra::create([
						'palavra' => $paramS,
						'aguardando_definicao' => 1
					]);
				} else {
					$palavraKnown->update([
						'aguardando_definicao' => 1
					]);
				}
			} else {
				$frase = $results->extract;
			}
			
			/**
			* Verifica se o que foi pesquisado existe no banco de dados,
			* senão salva
			*/
			if (!Conhecimento::whereTermo($paramS)->count()) {
				Conhecimento::create([
					'termo' => str_replace('+', '', $param),
					'definicao' => $frase
				]);
			}
			return $frase;
		} catch (Exception $ex){
			$conhecimento = Conhecimento::whereTermo($paramS);
			if ($conhecimento->count()) {
				$frase = $conhecimento->first()->definicao;
			}
			return $frase;
		}
	}

	private function wiki($param)
	{
		$url = "https://pt.wikipedia.org/w/api.php?action=query&titles={$param}&redirects=1&prop=extracts&exchars=1000&exintro&format=json";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result=curl_exec($ch);
		curl_close($ch);
		return json_decode($result)->query->pages;
	}

	public function postSpeakNew()
	{
		$text = strip_tags(Input::get('text'));
		$text = str_replace(' ', '+', $text);
		$url = "http://translate.google.com.br/translate_tts?ie=UTF-8&q={$text}&tl=pt_br&textlen=100&prev=input";
	    
	    $mp3 = file_get_contents($url);
	    file_put_contents(public_path('dominique/speak/temp.mp3'), $mp3);
	    return Response::json([
	    	'url' => URL::to('dominique/speak/temp.mp3')
	    ]);
	}
}	