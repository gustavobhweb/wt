@extends('layouts.defaultWt')

@section('content')
<form method="post" id="talk">
	<input type="text" class="wm-input frase" autofocus name="frase" />
	<button class="btn medium green">Talk <i class="glyphicon glyphicon-bullhorn"></i></button>
</form>
<br>
{{ (isset($answer)) ? '<h4 id="text">' . $answer . '</h4>' : '' }}
@stop

@section('scripts')
{{ 	
	HTML::script('js/dominique/annyang.min.js'),
	HTML::script('js/dominique/inicio.js')
}}
@append