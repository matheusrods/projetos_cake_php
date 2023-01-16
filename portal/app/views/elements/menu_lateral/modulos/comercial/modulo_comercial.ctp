
<!-- Modulo Comercial-->
<?php $this->Session->read('modulo_selecionado') ?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::COMERCIAL?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Comercial</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php echo $this->element('menu_lateral/modulos/comercial/sub_menus/cadastros'); ?>

        <?php echo $this->element('menu_lateral/modulos/comercial/sub_menus/operacoes'); ?>

        <?php echo $this->element('menu_lateral/modulos/comercial/sub_menus/consultas'); ?>

        <?php echo $this->element('menu_lateral/modulos/comercial/sub_menus/cadastros_terceiros'); ?>

        <?php echo $this->element('menu_lateral/modulos/comercial/sub_menus/operacoes_terceiros'); ?>

        <?php echo $this->element('menu_lateral/modulos/comercial/sub_menus/consultas_terceiros'); ?>
    </ul>
</li>
