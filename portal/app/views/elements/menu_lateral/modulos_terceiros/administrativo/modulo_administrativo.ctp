
<!-- Modulo administrativo-->
<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::ADMINISTRATIVO?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Administrativo</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php echo $this->element('menu_lateral/modulos/administrativo/sub_menus/cadastros'); ?>

        <?php echo $this->element('menu_lateral/modulos/administrativo/sub_menus/operacoes'); ?>

        <?php echo $this->element('menu_lateral/modulos/administrativo/sub_menus/consultas'); ?>

    </ul>
</li>
