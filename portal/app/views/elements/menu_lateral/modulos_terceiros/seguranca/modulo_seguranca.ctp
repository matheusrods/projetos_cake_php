<!-- Modulo Segurança-->
<?php

$menusOperacoes = array(
    array(
        "controller" => "cat",
        "action" => "index",
    ),
    array(
        "controller" => "clientes_implantacao",
        "action" => "index_ppra_ext",
    ),
    array(
        "controller" => "clientes_implantacao",
        "action" => "gestao_cronograma_ppra",
    ),
    array(
        "controller" => "chamados",
        "action" => "index",
    ),
    array(
        "controller" => "ghe",
        "action" => "index",
    ),
    array(
        "controller" => "processos",
        "action" => "index",
    ),
    array(
        "controller" => "riscos_tipos",
        "action" => "index",
    ),
    array(
        "controller" => "perigos_aspectos",
        "action" => "index",
    ),
    array(
        "controller" => "riscos_impactos",
        "action" => "index",
    ),
    array(
        "controller" => "agentes_riscos",
        "action" => "index",
    ),
    array(
        "controller" => "unidades_medicao",
        "action" => "index",
    ),
    array(
        "controller" => "clientes",
        "action" => "visualizar_clientes_gestao_de_risco",
    )
);

$menusConsultas = array(
    array(
        "controller" => "ppra_versoes",
        "action" => "versoes_ppra",
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

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::SEGURANCA?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Segurança</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php
            if ($permiteMenuOperacoes > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/seguranca/sub_menus/operacoes_terceiros');
            }

            if ($permiteMenusConsultas > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/seguranca/sub_menus/consultas_terceiros');
            }
        ?>
    </ul>
</li>
