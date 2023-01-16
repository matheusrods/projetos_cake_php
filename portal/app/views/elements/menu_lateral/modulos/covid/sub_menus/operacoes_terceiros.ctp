<!-- Sub menus Covid Operações terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Operações terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'usuario_grupo_covid','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/usuario_grupo_covid">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Gestão Covid</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'funcionarios','action'=>'index_funcionario_liberacao'))) :?>
            <li class="">
                <a href="/portal/funcionarios/index_funcionario_liberacao">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Gestão funcionários</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
