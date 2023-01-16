
<!-- Modulo Mapeamento de risco-->
<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::MAPEAMENTORISCO ?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Mapeamento risco</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php echo $this->element('menu_lateral/modulos/mapeamento_risco/sub_menus/cadastros'); ?>

        <?php echo $this->element('menu_lateral/modulos/mapeamento_risco/sub_menus/consultas_terceiros'); ?>
    </ul>
</li>
