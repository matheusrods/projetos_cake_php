<!-- Modulo e-social-->
<?php
$menusOperacoes = array(
    array(
        "controller" => "esocial",
        "action" => "s2210",
    ),
    array(
        "controller" => "esocial",
        "action" => "s2220",
    ),
    array(
        "controller" => "esocial",
        "action" => "s2221",
    ),
    array(
        "controller" => "esocial",
        "action" => "s2240",
    )
);

$permiteMenuOperacoes = 0;
foreach ($menusOperacoes as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenuOperacoes++;
    }
}
?>

<?php $this->Session->read('modulo_selecionado') ?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::ESOCIAL ?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">E-Social</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php
        if (!empty($permiteMenuOperacoes)) {
            echo $this->element('menu_lateral/modulos_terceiros/e_social/sub_menus/operacoes_terceiros');
        }
        ?>
    </ul>
</li>
