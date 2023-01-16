<!-- Sub menus Plano de ação Operações terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Operações terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'configuracao_obs'))) :?>
            <li class="">
                <a href="/portal/clientes/configuracao_obs">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Configurações Observador &nbsp;&nbsp;&nbsp;&nbsp;EHS</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
    </ul>
</li>
