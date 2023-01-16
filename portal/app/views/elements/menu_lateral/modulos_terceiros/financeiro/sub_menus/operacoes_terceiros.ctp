<!-- Sub menus Financeiro Operações terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Operações</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'pre_faturamento'))) :?>
            <li class="">
                <a href="/portal/clientes/pre_faturamento">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Pré faturamento</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

    </ul>
</li>
