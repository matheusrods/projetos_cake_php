
<!-- Modulo Financeiro-->
<?php $this->Session->read('modulo_selecionado') ?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::FINANCEIRO?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Financeiro</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php echo $this->element('menu_lateral/modulos/financeiro/sub_menus/cadastros'); ?>

        <?php echo $this->element('menu_lateral/modulos/financeiro/sub_menus/operacoes'); ?>

        <?php echo $this->element('menu_lateral/modulos/financeiro/sub_menus/consultas'); ?>

        <?php echo $this->element('menu_lateral/modulos/financeiro/sub_menus/operacoes_terceiros'); ?>

        <?php echo $this->element('menu_lateral/modulos/financeiro/sub_menus/consultas_terceiros'); ?>
    </ul>
</li>
