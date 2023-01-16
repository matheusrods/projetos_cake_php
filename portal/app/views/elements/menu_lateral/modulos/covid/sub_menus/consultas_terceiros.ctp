<!-- Sub menus Covid Consultas terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-bulb"></i></span>
        <span class="scoop-mtext">Consultas terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'covid','action'=>'brasil_io'))) :?>
            <li class="">
                <a href="/portal/covid/brasil_io">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Mapa Covid-19 Brazil</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'covid','action'=>'lyn'))) :?>
            <li class="">
                <a href="/portal/covid/lyn">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Dash Lyn</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'covid','action'=>'lyn_rh'))) :?>
            <li class="">
                <a href="/portal/covid/lyn_rh">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Resultado de exame</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
