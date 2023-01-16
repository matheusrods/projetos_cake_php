<!-- Sub menus Segurança Consultas terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-bulb"></i></span>
        <span class="scoop-mtext">Consultas</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'ppra_versoes','action'=>'versoes_ppra'))) :?>
            <li class="">
                <a href="/portal/ppra_versoes/versoes_ppra">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Versões PGR</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
    </ul>
</li>
