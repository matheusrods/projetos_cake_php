<!-- Modulo Mapeamento de risco-->
<?php
$menusConsultas = array(
    array(
        "controller" => "dados_saude_consultas",
        "action" => "dashboard/colaboradores_atestados",
    ),
    array(
        "controller" => "dados_saude_consultas",
        "action" => "dashboard/dados_gerais",
    ),
    array(
        "controller" => "dados_saude_consultas",
        "action" => "relatorio_faixa_etaria",
    ),
    array(
        "controller" => "dados_saude_consultas",
        "action" => "relatorio_fatores_risco",
    ),
    array(
        "controller" => "dados_saude_consultas",
        "action" => "relatorio_imc",
    ),
    array(
        "controller" => "dados_saude_consultas",
        "action" => "relatorio_genero",
    ),
    array(
        "controller" => "dados_saude_consultas",
        "action" => "relatorio_posicao_questionarios",
    ),
);

$permiteMenusConsultas = 0;
foreach ($menusConsultas as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenusConsultas++;
    }
}
?>

<?php $this->Session->read('modulo_selecionado') ?>

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::MAPEAMENTORISCO ?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Mapeamento risco</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">

        <?php
            if ($permiteMenusConsultas > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/mapeamento_risco/sub_menus/consultas_terceiros');
            }
        ?>
    </ul>
</li>
