<!-- Sub menus Segurança Consultas terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-bulb"></i></span>
        <span class="scoop-mtext">Consultas terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'pos_obs_relatorio_realizadas','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/pos_obs_relatorio_realizadas">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Relatório de Observações &nbsp;&nbsp;&nbsp;&nbsp;Realizadas</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'swt','action'=>'relatorio_analise_swt'))) :?>
            <li class="">
                <a href="/portal/pos_obs_relatorio_analise_qualidade">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Relatório de Análises de &nbsp;&nbsp;&nbsp;&nbsp;Qualidade</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
    </ul>
</li>
