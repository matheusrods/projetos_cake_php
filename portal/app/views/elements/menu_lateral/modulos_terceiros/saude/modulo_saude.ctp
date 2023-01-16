<!-- Modulo terceiros Saude-->
<?php
$menusCadastro = array(
    array(
        "controller" => "cliente_aparelho_audiometrico",
        "action" => "index",
    ), 
    array(
        "controller" => "hospitais_emergencia",
        "action" => "index",
    ), 
    array(
        "controller" => "pmps",
        "action" => "index",
    ),
);

$menusOperacoes = array(
    array(
        "controller" => "audiometrias",
        "action" => "index",
    ),
    array(
        "controller" => "atestados",
        "action" => "index",
    ), 
    array(
        "controller" => "itens_pedidos_exames_baixa",
        "action" => "index",
    ), 
    array(
        "controller" => "consultas_agendas",
        "action" => "index2",
    ), 
    array(
        "controller" => "clientes_funcionarios",
        "action" => "selecao_funcionarios",
    ), 
    array(
        "controller" => "fichas_assistenciais",
        "action" => "index",
    ), 
    array(
        "controller" => "ficha_psicossocial",
        "action" => "index",
    ),
    array(
        "controller" => "importar",
        "action" => "manutencao_pedido_exame",
    ),
    array(
        "controller" => "clientes_implantacao",
        "action" => "index_pcmso_ext",
    ),
    array(
        "controller" => "clientes_implantacao",
        "action" => "gestao_cronograma_pcmso",
    ),
);

$menusConsultas = array(
    array(
        "controller" => "atestados",
        "action" => "sintetico",
    ),
    array(
        "controller" => "medicos",
        "action" => "corpo_clinico",
    ),
    array(
        "controller" => "consultas_agendas",
        "action" => "index",
    ),
    array(
        "controller" => "consulta_pedidos_exames",
        "action" => "baixa_exames_sintetico",
    ),
    array(
        "controller" => "ficha_psicossocial",
        "action" => "ficha_psicossocial_terceiros",
    ),    
    array(
        "controller" => "fichas_pcd",
        "action" => "index",
    ),    
    array(
        "controller" => "clientes",
        "action" => "funcionarios_ppp",
    ),    
    array(
        "controller" => "exames",
        "action" => "posicao_exames_sintetico",
    ),
    array(
        "controller" => "exames",
        "action" => "posicao_exames_analitico2",
    ),
    array(
        "controller" => "exames",
        "action" => "relatorio_anual",
    ), 
    array(
        "controller" => "fichas_clinicas",
        "action" => "fichas_clinicas_terceiros",
    ),    
    array(
        "controller" => "pcmso_versoes",
        "action" => "versoes_pcmso",
    ),
);

$permiteMenuCadastro = 0;
foreach ($menusCadastro as $menu) {

    if ($this->BMenu->permiteMenu(array('controller'=> "{$menu['controller']}",'action'=> "{$menu['action']}"))) {
        $permiteMenuCadastro++;
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

<li class="scoop-hasmenu <?= $modulo_selecionado==Modulo::SAUDE?'active':''; ?>">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-user"></i></span>
        <span class="scoop-mtext">Saúde</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php 
            if ($permiteMenuCadastro > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/saude/sub_menus/cadastros_terceiros');
            }

            if ($permiteMenuOperacoes > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/saude/sub_menus/operacoes_terceiros');
            }

            if ($permiteMenusConsultas > 0) {
                echo $this->element('menu_lateral/modulos_terceiros/saude/sub_menus/consultas_terceiros');
            }
        ?>
    </ul>
</li>
