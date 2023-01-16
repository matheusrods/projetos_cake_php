<!-- Modulo Safety walk talk-->

<?php
$menusCadastros = array(
    array(
        "controller" => "acoes_melhorias_tipo",
        "action" => "index",
    ),
    array(
        "controller" => "area_atuacao",
        "action" => "index",
    ),
    array(
        "controller" => "clientes",
        "action" => "regras_acao",
    ),
    array(
        "controller" => "clientes",
        "action" => "config_criticidade",
    ),
    array(
        "controller" => "clientes",
        "action" => "matriz_responsabilidade",
    ),
    array(
        "controller" => "origem_ferramenta",
        "action" => "index",
    ),
    array(
        "controller" => "pda_config_regra",
        "action" => "index_pda_regra",
    ),
    array(
        "controller" => "subperfil",
        "action" => "index",
    ),
    array(
        "controller" => "clientes",
        "action" => "usuarios",
    ),
);

$menusConsultas = array(
    array(
        "controller" => "clientes",
        "action" => "acoes_cadastradas",
    ),
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
?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::WALK_TALK?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Safety walk & talk</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php
            if ($permiteMenuCadastros > 0) {
                echo $this->element('menu_lateral/modulos/safety_walk_talk/sub_menus/cadastros_terceiros');
            }

            if ($permiteMenuConsultas > 0) {
                echo $this->element('menu_lateral/modulos/safety_walk_talk/sub_menus/consultas_terceiros');
            }
        ?>
    </ul>
</li>
