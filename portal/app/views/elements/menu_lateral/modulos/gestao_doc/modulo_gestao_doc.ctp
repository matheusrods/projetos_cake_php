
<!-- Modulo Gestão doc-->
<?php $this->Session->read('modulo_selecionado') ?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::GESTAODOCUMENTOS?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Gestão doc.</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php echo $this->element('menu_lateral/modulos/gestao_doc/sub_menus/cadastros'); ?>
    </ul>
</li>
