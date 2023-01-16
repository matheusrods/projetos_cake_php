<!-- Modulo Observador EHS-->

<?php
$menusCadastros = array(
    array(
        "controller" => "pos_categorias",
        "action" => "index",
    )
);

$menusConsultas = array(
    array(
        "controller" => "pos_obs_relatorio_realizadas",
        "action" => "index",
    ),
    array(
        "controller" => "pos_obs_relatorio_analise_qualidade",
        "action" => "index",
    )
);

$menusOperacoes = array(
    array(
        "controller" => "pos_configuracoes",
        "action" => "index",
    )
);

$permiteMenuCadastros = 0;
foreach ($menusCadastros as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenuCadastros++;
    }
}

$permiteMenuConsultas = 0;
foreach ($menusConsultas as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenuConsultas++;
    }
}

$permiteMenuOperacoes = 0;
foreach ($menusOperacoes as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenuOperacoes++;
    }
}
?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::OBSERVADOR_EHS?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Observador EHS</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php
            if ($permiteMenuCadastros > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/observador_ehs/sub_menus/cadastros_terceiros');
            }

            if ($permiteMenuConsultas > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/observador_ehs/sub_menus/operacoes_terceiros');
            }

            if ($permiteMenuOperacoes > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/observador_ehs/sub_menus/consultas_terceiros');
            }
        ?>
    </ul>
</li>
