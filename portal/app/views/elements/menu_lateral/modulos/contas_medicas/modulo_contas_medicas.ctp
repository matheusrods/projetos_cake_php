
<!-- Modulo Contas medicas-->
<?php $this->Session->read('modulo_selecionado') ?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::CONTASMEDICAS?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Contas Médicas</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php echo $this->element('menu_lateral/modulos/contas_medicas/sub_menus/cadastros'); ?>

        <?php echo $this->element('menu_lateral/modulos/contas_medicas/sub_menus/operacoes'); ?>
    </ul>
</li>
