<form method="post">
	<input type="text" autofocus name="frase" />
	<button>Talk</button>
</form>

{{ (isset($answer)) ? '<h4>Dominique: ' . $answer . '</h4>' : '' }}