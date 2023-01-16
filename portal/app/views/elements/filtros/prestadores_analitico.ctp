<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('PrestadoresPostgres', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PrestadoresPostgres', 'element_name' => 'prestadores_analitico'), 'divupdate' => '.form-procurar2')) ?>
        <?= $this->element('filtros/prestadores_combos_acionamento') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn' )) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-prestadores', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>    
</div>
<?php echo $this->Javascript->codeBlock('
function atualiza_informacoes_prestadores(pagina){
    var div = jQuery("div.lista");bloquearDiv(div);
    if(pagina) {
        div.load(baseUrl + "prestadores/analitico_listagem/0/page:"+pagina+"/" + Math.random());
    }else {
        div.load(baseUrl + "prestadores/analitico_listagem/" + Math.random());
    }
}
jQuery(document).ready(function(){
    setup_mascaras();   
    '.(isset($filtrado) && ($filtrado) ? 'atualiza_informacoes_prestadores();':'').'
    jQuery("#limpar-filtro-prestadores").click(function(){
        bloquearDiv(jQuery(".form-procurar2"));
        jQuery(".form-procurar2").load(baseUrl + "/filtros/limpar/model:Prestadores/element_name:prestadores_analitico/" + Math.random())
    });
    jQuery("a#filtros").click(function(){
        jQuery("div#filtros").slideToggle("slow");
    });
    jQuery("#FiltroSalvarFiltro").click(function(){
        jQuery("#FiltroNomeFiltro").parent().toggle()
    });
});', false);?>
<?php
if (!empty($filtrado)):
    echo $this->Javascript->codeBlock('
        jQuery(document).ready(function(){
            jQuery("div#filtros").hide();
        })');
endif; 
?>