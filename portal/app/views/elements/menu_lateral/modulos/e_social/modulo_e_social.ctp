
<!-- Modulo e-social-->
<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::ESOCIAL ?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">E-Social</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php echo $this->element('menu_lateral/modulos/e_social/sub_menus/operacoes_terceiros'); ?>

    </ul>
</li>
