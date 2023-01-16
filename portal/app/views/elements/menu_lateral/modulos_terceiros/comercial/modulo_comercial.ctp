<!-- Modulo Comercial-->
<?php
$menusCadastros = array(
    array(
        "controller" => "assinatura_eletronica",
        "action" => "index",
    ),
    array(
        "controller" => "cargos",
        "action" => "cargo_terceiros",
    ),
    array(
        "controller" => "clientes",
        "action" => "funcionarios",
    ),
    array(
        "controller" => "clientes",
        "action" => "funcionarios_percapita",
    ),
    array(
        "controller" => "medicos",
        "action" => "index",
    ),
    array(
        "controller" => "setores",
        "action" => "setor_terceiros",
    ),
    array(
        "controller" => "clientes",
        "action" => "cliente_tomador",
    ),
    array(
        "controller" => "clientes",
        "action" => "cliente_terceiros",
    ),
    array(
        "controller" => "tipos_acoes",
        "action" => "index",
    ),
);

$menusOperacoes = array(
    array(
        "controller" => "tipo_digitalizacao",
        "action" => "operacao_digitalizacao_terceiros",
    ),
);

$menusConsultas = array(
    array(
        "controller" => "tipo_digitalizacao",
        "action" => "consulta_digitalizacao_terceiros",
    ),
    array(
        "controller" => "consultas",
        "action" => "ppra_pcmso_pendente_terceiros",
    ),
    array(
        "controller" => "riscos_exames",
        "action" => "aplicados",
    ),
    array(
        "controller" => "clientes_funcionarios",
        "action" => "consulta_vidas",
    ),
    array(
        "controller" => "aplicacao_exames",
        "action" => "vigencia_ppra_pcmso",
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

$permiteMenusConsultas = 0;
foreach ($menusConsultas as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenusConsultas++;
    }
}
?>

<?php $this->Session->read('modulo_selecionado') ?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::COMERCIAL?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Comercial</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php 
            if ($menusCadastros) {
                echo $this->element('menu_lateral/modulos/comercial/sub_menus/cadastros_terceiros');
            }

            if ($menusOperacoes) {
                echo $this->element('menu_lateral/modulos/comercial/sub_menus/operacoes_terceiros');
            }

            if ($menusConsultas) {
                echo $this->element('menu_lateral/modulos/comercial/sub_menus/consultas_terceiros');
            }
        ?>
    </ul>
</li>
