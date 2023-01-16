<!-- Modulo Financeiro-->
<?php

$menusOperacoes = array(
    array(
        "controller" => "clientes",
        "action" => "pre_faturamento",
    )
);

$menusConsultas = array(
    array(
        "controller" => "clientes",
        "action" => "gerar_segunda_via_faturamento",
    ),
    array(
        "controller" => "clientes",
        "action" => "utilizacao_de_servicos",
    ),
    array(
        "controller" => "clientes",
        "action" => "utilizacao_de_servicos_historico",
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

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::FINANCEIRO?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Financeiro</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php
        if (!empty($permiteMenuOperacoes)) {
            echo $this->element('menu_lateral/modulos_terceiros/financeiro/sub_menus/operacoes_terceiros');
        }

        if (!empty($permiteMenusConsultas)) {
            echo $this->element('menu_lateral/modulos_terceiros/financeiro/sub_menus/consultas_terceiros');
        }
        ?>
    </ul>
</li>
