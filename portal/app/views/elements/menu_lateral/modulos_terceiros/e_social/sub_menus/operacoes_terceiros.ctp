<!-- Sub menus E-social operacoes terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-bulb"></i></span>
        <span class="scoop-mtext">Operações</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'esocial','action'=>'s2210'))) :?>
            <li class="">
                <a href="/portal/esocial/s2210">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Tabela S-2210</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'esocial','action'=>'s2220'))) :?>
            <li class="">
                <a href="/portal/esocial/s2220">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Tabela S-2220</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'esocial','action'=>'s2221'))) :?>
            <li class="">
                <a href="/portal/esocial/s2221">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Tabela S-2221</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'esocial','action'=>'s2240'))) :?>
            <li class="">
                <a href="/portal/esocial/s2240">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Tabela S-2240</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
    </ul>
</li>
