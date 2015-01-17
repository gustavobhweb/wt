@extends('layouts.default')

@section('title') Criar Usuário @stop

@section('content')

<div class="conteudo-form">

    {{ Form::model($aluno, ['id' => 'form-meus-dados', 'autocomplete' => 'off']) }}

    <fieldset>
        <h5>Dados do usuário:</h5>

        {{ Session::get('message') }}

        <div class="wm-input-container per-2 icon-status">

            {{ Form::label('nome', 'Nome Completo') }}

            {{ 
                Form::text(
                    'nome',
                    Input::old('nome'), 
                    [ 
                        'placeholder' => 'Nome Completo',
                        'required' => 'required',
                        'pattern'   => "([\s\u00c0-\u01ffa-zA-Z'\-])+"
                    ] 
                )
            }}

            <div>{{ $errors->first('nome', '<div class="j-alert-error">:message</div>') }}</div>
        </div>

        <div class="wm-input-container per-2 icon-status">

            {{ Form::label('username', 'Login') }}

            {{ 
                Form::text(
                    'username',
                    Input::old('username'), 
                    [ 
                        'placeholder' => 'meu_login',
                        'title' => 'e-mail não confere',
                        'pattern'   => '[0-9a-zA-Z_@\.]+',
                        'required'  => 'required'
                    ] 
                )
            }}

            <div>{{ $errors->first('username', '<div class="j-alert-error">:message</div>') }}</div>
        </div>

        <div class="wm-input-container input-email-format">

        <div class="icon-status">
            {{ Form::text('cep', Input::old('cep'), ['style' => 'width:190px', 'id' => 'input-cep']) }}
        </div>

             <button class="btn-conf-iza btn-verificar-cep pull-left" style="height:47px">VERIFICAR</button>
             
             <a href="http://www.buscacep.correios.com.br/" target="_blank" id="nao-sabe-cpf">
                 <span class="btn_nao_sabe">Não sei meu cep</span>
             </a>
        </div>


        <div class="clearfix"></div>        

        <div class="wm-input-container per-2 icon-status input-email-format">
            <label>E-mail:</label> 

            {{ 
                Form::email(
                    'email', 
                    Input::old('email'),
                    [
                        'placeholder' => 'nome@dominio.com',
                        'id'          => 'email',
                        'required'    => 'required'
                    ]
                ) 
            }}



            <div class="glif">
                <div class="glyphicon glifEmail glyphicon-ok"></div>
            </div>

            <div>{{ $errors->first('email', '<div class="j-alert-error">:message</div>') }}</div>
        </div>

        <div class="wm-input-container per-2 icon-status input-email-format">
            <label>Confirmação e-mail:</label> 

            {{ 
                Form::email(
                    'confirmaEmail',
                    Input::old('confirmaEmail'), 
                    [ 
                        'placeholder' => 'nome@dominio.com',
                        'id' => 'confirmaEmail',
                        'required' => 'required'
                    ] 
                )
            }}

            <div class="glif">
                <div class="glyphicon glifEmail glyphicon-ok"></div>
            </div>

        </div>


        <div class="clearfix"></div>


        <div class="wm-input-container per-2 icon-status">
            <label>Senha:</label> 

            {{ 
                Form::password(
                    'password', 
                    [
                        'placeholder' => '******',
                        'required' => 'required',
                        'id'       => 'senha'
                    ]
                ) 
            }}

            <div class="glif">
                <div class="glyphicon glifEmail glyphicon-ok"></div>
            </div>

            <div>{{ $errors->first('senha', '<div class="j-alert-error">:message</div>') }}</div>
        </div> 

        <div class="wm-input-container per-2 icon-status">
            <label>Confirmação Senha:</label> 

            {{ 
                Form::password(
                    'confirmaSenha', 
                    [
                        'placeholder' => '******',
                        'required' => 'required',
                        'id'    => 'confirmaSenha'
                    ]
                ) 
            }}

            <div class="glif">
                <div class="glyphicon glifEmail glyphicon-ok"></div>
            </div>
        </div> 

        <div class='clearfix'></div>

        <div class="wm-input-container icon-status per-2">
            <div class="sub-container-input" style="width:40%">
                <label>UF</label>
                {{ Form::select('uf', $estado, false, ['id' => 'uf']) }}
            </div>
            <div class="sub-container-input" style="width:60%">
                <label>Cidade</label>
                {{ Form::select('cidade', $cidade, false, ['id' => 'cidade']) }}
            </div>
        </div>


        <div class="wm-input-container icon-status per-2">
            <div class="sub-container-input" style="width:80%">
                {{ Form::label('endereco', 'Endereço') }}
                {{ Form::text('endereco') }}
            </div>
            <div class="sub-container-input" style="width:20%">
                {{  Form::label('numero', 'Nº') }}
                {{  Form::text('numero', Input::old('numero')) }}
            </div>
        </div>

        <div class='clearfix'></div>


        <div class="pull-right wm-input-container" >
        {{ 
            Form::submit('Salvar', ['class' => 'btn-conf-iza', 'id' => 'submit']) 
        }}
        </div>

        
    </fieldset>
    {{ Form::close() }}

</div>

@stop


@section('styles') 
{{
    HTML::style('css/enviar-foto.css'), 
    HTML::style('css/wm-modal.css'),
    HTML::style('css/aluno/meus_dados.css'),
    HTML::style('css/colaborador/meus-dados.css')
}}
@stop 



@section('scripts')
{{ 
    HTML::script('js/auth/meus_dados.js'),
    HTML::script('js/jQueryObjectForm.js')
}}
@stop