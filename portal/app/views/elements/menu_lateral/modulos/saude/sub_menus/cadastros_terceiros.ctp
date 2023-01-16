<!-- Sub menus Saude cadastros terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Cadastros terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'cliente_aparelho_audiometrico','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/cliente_aparelho_audiometrico">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Aparelhos audiométricos</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'hospitais_emergencia','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/hospitais_emergencia">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Hospitais de emergência</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'pmps','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/pmps">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Materiais de pronto socorro</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
