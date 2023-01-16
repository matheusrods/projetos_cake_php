<!-- Sub menus Financeiro Consultas terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-bulb"></i></span>
        <span class="scoop-mtext">Consultas</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'gerar_segunda_via_faturamento'))) :?>
            <li class="">
                <a href="/portal/clientes/gerar_segunda_via_faturamento">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Segunda via faturamento</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'utilizacao_de_servicos'))) :?>
            <li class="">
                <a href="/portal/clientes/utilizacao_de_servicos">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Utilização de serviços</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'utilizacao_de_servicos_historico'))) :?>
            <li class="">
                <a href="/portal/clientes/utilizacao_de_servicos_historico">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Utilização de serviços <br>&nbsp;&nbsp;&nbsp;&nbsp;Historico (demostrativo)</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

    </ul>
</li>
