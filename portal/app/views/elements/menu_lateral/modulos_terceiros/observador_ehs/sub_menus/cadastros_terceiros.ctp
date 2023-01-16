<!-- Sub menus Observador EHS cadastros terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Cadastros terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'pos_categorias','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/pos_categorias">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Tipos de Observação</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
