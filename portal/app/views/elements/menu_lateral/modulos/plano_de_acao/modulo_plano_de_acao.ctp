<!-- Modulo Plano de ação-->

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

$menusOperacoes = array(
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

$permiteMenuOperacoes = 0;
foreach ($menusOperacoes as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenuOperacoes++;
    }
}
?>
<?php $this->Session->read('modulo_selecionado') ?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::PLANO_DE_ACAO?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Plano de ação</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php
            if ($permiteMenuCadastros > 0) {
                echo $this->element('menu_lateral/modulos/plano_de_acao/sub_menus/cadastros_terceiros');
            }

            if ($permiteMenuOperacoes > 0) {
                echo $this->element('menu_lateral/modulos/plano_de_acao/sub_menus/operacoes_terceiros');
            }
        ?>
    </ul>
</li>
