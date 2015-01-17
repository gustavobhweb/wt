@extends('layouts.default')


@section('title') Enviar Carga @stop

@section('content')

<div class="clearfix">
	{{ Form::open(['files' => true]) }}
	<div class="wm-input-container">
		<div class="warning">
			<p>
				Selecione um arquivo <strong>XLS</strong> ou <strong>XLSX</strong> e
				clique no botão <strong>enviar</strong>
			</p>
			<p>
				Os arquivos deverão ter todos os campos de acordo com o <a
					target="_blank" class="link"
					href="{{ URL::to('xls/modelo/default.xls') }}">modelo</a>
			</p>
		</div>

		{{ Form::file('excel', ['class' => 'wm-input']) }} 
            
        {{ 
        
            Form::button(
                'Selecione um arquivo ...', 
                ['id' => 'fake-file-excel', 'class' => 'wm-btn']
            ) 
        }} 
           
        {{  Form::button('Enviar', ['class' => 'wm-btn', 'type' => 'submit'])  }}
	</div>


	@if($errors->has('message'))
        <div class="j-alert-error">{{ $errors->first('message') }}</div>
	@endif 
    
    {{ Form::close() }}
</div>
@if(Session::has('messageSuccess'))
    <div class="j-alert-error">{{ Session::get('messageSuccess') }}</div>
@endif

    @if($excelData = Session::get('uploadedData'))
        <?php
            $countYes = count($excelData['yes']);
            $countNot = count($excelData['not']);
            $countEmpty = count($excelData['hasEmpty']);
            $countAll = $countYes + $countNot + $countEmpty;
        ?>
<div class="j-alert-error" style="font-size: 14px">
	<table class="table">
		<tr>
			<td>Número de linhas do arquivo</td>
			<th>{{ $countAll }}</th>
		</tr>
		<tr>
			<td>Alunos cadastrados</td>
			<th>{{ $countYes }}</th>
		</tr>
		<tr>
			<td>Alunos existentes no banco</td>
			<th>{{ $countNot }}</th>
		</tr>
		<tr>
			<td>Créditos inseridos</td>
			<th>{{ $countYes + $countNot }}</th>
		</tr>
		<tr>
			<td>Linhas com campos em branco</td>
			<th>{{ $countEmpty }}</th>
		</tr>
	</table>
</div>

    @if(!empty($excelData['yes']))
        <h3 class="h-title blue">Dados inseridos</h3>
        <table class="wm-table big">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Matricula</th>
                    <th>Curso</th>
                    <th>CPF</th>
                    <th>Instituição</th>
                </tr>
            </thead>
            <tbody>
                @foreach($excelData['yes'] as $index => $value)
                <tr>
                    <td>{{ $value['nome'] or '?' }}</td>
                    <td>{{ $value['matricula'] or '?' }}</td>
                    <td>{{ $value['curso'] or '?' }}</td>
                    <td>{{ $value['cpf'] or '?' }}</td>
                    <td>{{ $value['instituicao'] or '?' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    @if(!empty($excelData['not']))
        <table class="wm-table big error">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Matricula</th>
                    <th>Curso</th>
                    <th>CPF</th>
                    <th>Instituição</th>
                </tr>
            </thead>
            @foreach($excelData['not'] as $index => $value)
            <tr>
                <td>{{ $value['nome'] or '?' }}</td>
                <td>{{ $value['matricula'] or '?' }}</td>
                <td>{{ $value['curso'] or '?' }}</td>
                <td>{{ $value['cpf'] or '?' }}</td>
                <td>{{ $value['instituicao'] or '?' }}</td>
            </tr>
            @endforeach
        </table>


   @endif 
   
   @if(!empty($excelData['hasEmpty']))
    <h3 class="h-title">Registro com campos em branco</h3>
    <table class="wm-table big error">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Matricula</th>
                <th>Curso</th>
                <th>CPF</th>
                <th>Instituição</th>
            </tr>
        </thead>
        @foreach($excelData['hasEmpty'] as $index => $value)
        <tr>
            <td>{{ $value['nome'] or '?' }}</td>
            <td>{{ $value['matricula'] or '?' }}</td>
            <td>{{ $value['curso'] or '?' }}</td>
            <td>{{ $value['cpf'] or '?' }}</td>
            <td>{{ $value['instituicao'] or '?' }}</td>
        </tr>
        @endforeach
    </table>
    @endif 

@endif 

@stop 

@section('scripts') 

{{ HTML::script('js/cliente/enviar_carga.js') }} 

@stop
