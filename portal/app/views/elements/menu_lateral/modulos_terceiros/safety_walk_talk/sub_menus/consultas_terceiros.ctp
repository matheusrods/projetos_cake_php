<!-- Sub menus Segurança Consultas terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-bulb"></i></span>
        <span class="scoop-mtext">Consultas terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'swt','action'=>'relatorio_swt'))) :?>
            <li class="">
                <a href="/portal/swt/relatorio_swt">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Relatório de Walk & Talk</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'swt','action'=>'relatorio_analise_swt'))) :?>
            <li class="">
                <a href="/portal/swt/relatorio_analise_swt">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Relatório de Análises de Walk &nbsp;&nbsp;&nbsp;&nbsp;& Talk</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
    </ul>
</li>
