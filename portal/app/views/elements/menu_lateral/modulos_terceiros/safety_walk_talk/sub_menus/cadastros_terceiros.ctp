<!-- Sub menus Plano de ação cadastros terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Cadastros terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'swt','action'=>'index_qtd_participantes'))) :?>
            <li class="">
                <a href="/portal/swt/index_qtd_participantes">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Conf. Quantidade de &nbsp;&nbsp;&nbsp;&nbsp;Participantes Walk & Talk</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'configuracao_swt'))) :?>
            <li class="">
                <a href="/portal/clientes/configuracao_swt">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Configuração Walk & Talk</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'swt','action'=>'index_form'))) :?>
            <li class="">
                <a href="/portal/swt/index_form">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Formulários Dinâmicos Walk & &nbsp;&nbsp;&nbsp;&nbsp;Talk</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'swt','action'=>'index_metas'))) :?>
            <li class="">
                <a href="/portal/swt/index_metas">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Metas da Área</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
