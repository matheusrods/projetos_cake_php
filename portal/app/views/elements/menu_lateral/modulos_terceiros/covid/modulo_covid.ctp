<!-- Modulo Covid-->
<?php
$menusOperacoes = array(
    array(
        "controller" => "usuario_grupo_covid",
        "action" => "index",
    ),
    array(
        "controller" => "funcionarios",
        "action" => "index_funcionario_liberacao",
    )
);

$menusConsultas = array(
    array(
        "controller" => "covid",
        "action" => "brasil_io",
    ),
    array(
        "controller" => "covid",
        "action" => "lyn",
    ),
    array(
        "controller" => "covid",
        "action" => "lyn_rh",
    ),
    array(
        "controller" => "covid",
        "action" => "resultado_exame_sintetico",
    )
);

$permiteMenuOperacoes = 0;
foreach ($menusOperacoes as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenuOperacoes++;
    }
}

$permiteMenusConsultas = 0;
foreach ($menusConsultas as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenusConsultas++;
    }
}
?>

<?php $this->Session->read('modulo_selecionado') ?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::COVID?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Covid</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php

            if ($permiteMenuOperacoes > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/covid/sub_menus/operacoes_terceiros');
            }

            if ($permiteMenusConsultas > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/covid/sub_menus/consultas_terceiros');
            } 

        ?>
    </ul>
</li>
