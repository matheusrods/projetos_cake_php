
<!-- Modulo Comercial-->
<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::CREDENCIAMENTO?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Credenciamento</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php echo $this->element('menu_lateral/modulos/credenciamento/sub_menus/cadastros'); ?>

        <?php echo $this->element('menu_lateral/modulos/credenciamento/sub_menus/operacoes'); ?>

        <?php echo $this->element('menu_lateral/modulos/credenciamento/sub_menus/consultas'); ?>

        <?php echo $this->element('menu_lateral/modulos/credenciamento/sub_menus/cadastros_terceiros'); ?>

        <?php echo $this->element('menu_lateral/modulos/credenciamento/sub_menus/consultas_terceiros'); ?>
    </ul>
</li>
