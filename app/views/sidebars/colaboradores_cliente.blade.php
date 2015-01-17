<ul>
    <li>
        <a  href="{{ URL::to('colaboradores') }}" class="{{ Request::is('colaboradores') ? 'on' : '' }}" >
            <img src='{{ URL::to("img/icon-home.png") }}' />
            Página inicial
        </a>
    </li>

    <li>
        <a href='{{ URL::to("colaboradores/avisos") }}' class="{{ Request::is('colaboradores/avisos') ? 'on' : '' }}" > 
            <img src='{{ URL::to("img/icon-avisos.png") }}' /> Avisos
        </a>
    </li>
    <li>
        <a href='{{ URL::to("colaboradores/acompanhar") }}' class="{{ Request::is('colaboradores/acompanhar') ? 'on' : '' }}"> 
            <img src='{{ URL::to("img/icon-acomp.png") }}' /> Acompanhar solicitações
        </a>
    </li>


</ul>