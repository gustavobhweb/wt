<ul>
    <li>
        <a {{ (Request::is('admin-cliente/editar-usuario')) ? 'class="on"' : '' }} href="{{ URL::to('admin-cliente/editar-usuario') }}">
            <span class="glyphicon glyphicon-pencil"></span>
            Gerenciar usuários
        </a>
    </li>
	<li>
        <a href='{{ URL::to("admin-cliente") }}' {{ (Request::is('admin-cliente')) ? 'class="on"' : '' }} >
            <span class="glyphicon glyphicon-user"></span>
            Cadastrar usuários
        </a>
    </li>
</ul>