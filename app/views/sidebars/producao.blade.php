<ul>
	<li>
		<a {{ (Request::is('producao/gerenciar-fotos')) ? 'class="on"' : '' }} href='{{ URL::to("producao/gerenciar-fotos") }}'>
			<span class="glyphicon glyphicon-film"></span> Gerenciar Fotos
		</a>
	</li>

    <li>
        <a {{ (Request::is('producao/baixar-carga')) ? 'class="on"' : '' }} href="{{ URL::to('producao/baixar-carga') }}">
            <span class="glyphicon glyphicon-download-alt"></span>
            Baixar Carga
        </a>
    </li>
    <li>
        <a {{ (Request::is('producao/lista-remessas-conferencia')) ? 'class="on"' : '' }} href="{{ URL::to('producao/lista-remessas-conferencia') }}">
            <span class='glyphicon glyphicon-barcode'></span>
            Conferência
        </a>
    </li>
    <li>
        <a {{ (Request::is('producao/expedicao')) ? 'class="on"' : '' }} href="{{ URL::to('expedicao') }}">
            <span class='glyphicon glyphicon-arrow-up'></span>
            Expedição
        </a>
    </li>
    <li>
        <a  class="{{ Request::is('producao/protocolos') ? 'on' : '' }}" 
            href="{{ URL::to('producao/protocolos') }}">
            <span class='glyphicon glyphicon-barcode'></span>
            Protocolos
        </a>
    </li>
    <li>
        <a  class="{{ Request::is('producao/pesquisar-remessa') ? 'on' : '' }}" 
            href="{{ URL::to('producao/pesquisar-remessa') }}">
            <span class='glyphicon glyphicon-search'></span>
            Pesquisar Remessa
        </a>
    </li>


</ul>