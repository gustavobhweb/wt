@extends('layouts.default') 

@section('title') <i class="glyphicon glyphicon-film"></i> Gerenciar Fotos @stop

@section('content')

@if(count($solicitacoes))
<section class="jtable">


    <div data-section='header' style="padding:10px 0 30px 10px">
        <p>Fotos para aprovação</p>
        {{ $errors->first('message', '<div class="pull-right j-alert-error">:message</div>') }}

        @if(Session::has('message'))
            <div class="pull-right j-alert-error">{{ Session::get('message') }}</div>
        @endif

        <button type='button' id='select-all' class='wm-btn wm-btn-blue pull-right' style='margin:0px 10px 10px 0'>
            <i class='glyphicon glyphicon-ok'></i> Selecionar todos
        </button>
    </div>
    <div class="clearifx"></div>

    @if($solicitacoes->count())

    {{ Form::open() }}
    <table>
        <tbody>

            @foreach($solicitacoes as $solicitacao)

            <tr class="selectable" data-id="{{ $solicitacao->id }}">
                <td style="position:relative" width="40">
                    <img class="mini-image" data-id='{{ $solicitacao->id }}' title="{{ $solicitacao->usuario->id }}" src='{{ URL::to("imagens/{$solicitacao->usuario->matricula}/{$solicitacao->foto}") }}' width='53' height='70' />
                </td>
                <td>{{{ $solicitacao->usuario->nome }}}</td>
                <td>{{ $solicitacao->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <button type='button' data-id='{{ $solicitacao->id }}' class='wm-btn wm-btn-red pull-right btn-reprovar-foto'>Reprovar foto</button>
                    <button type='button' data-id='{{ $solicitacao->id }}' class='wm-btn wm-btn-blue pull-right btn-editar-foto' style="margin-right:10px"><i class='glyphicon glyphicon-picture'></i> Editar foto</button>
                </td>
                <td width="40">
                    {{ 
                        Form::checkbox(
                            "solicitacoes[{$solicitacao->usuario->nivel_id}][{$solicitacao->instituicao_entrega_id}][]",
                            $solicitacao->id
                        )
                    }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div data-section='footer'>
        {{ 
            Form::button(
                'Aprovar', 
                [
                    'class' => 'btn-conf-iza',
                    'name'  => 'action',
                    'value' => 'aprovar',
                    'type'  => 'submit',
                    'style' => 'margin:0 10px'
                ]
            ) 
        }}
        <div class='clear'></div>
    </div>
    {{ Form::close() }}
    @else
        <div class='j-alert-error'>Não existem solicitações a serem analisadas</div>
    @endif
</section><!-- . jtable -->
@else
    <div class='j-alert-error'>Nenhuma foto para conferir.</div>
@endif


<div id='imageShow'>
	<div class="box">
		<img width="280" height="370" id='imageShowImg' />
	</div>
</div>


@stop 

@include('elements.common-alert')


@section('styles') 
{{

    HTML::style('css/jtable.css'),
    HTML::style('css/producao/aprovar_foto.css'),
    HTML::style('css/jquery-ui.css'),
    HTML::style('css/wm-modal.css'),
    HTML::style('css/jquery.ui.rotatable.css'),
    HTML::style('css/crop.css')

}}

@stop 

@section('scripts') 
{{
    HTML::script('js/jquery-ui.js'),
    HTML::script('js/jquery.ui.rotatable.js'),
    HTML::script('js/producao/aprovar_foto.js'),
    HTML::script('js/wm-modal.js'),
    HTML::script('js/crop.js')
}}
@stop

