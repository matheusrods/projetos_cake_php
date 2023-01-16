<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('PrestadoresPostgres', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PrestadoresPostgres', 'element_name' => 'prestadores_sintetico'), 'divupdate' => '.form-procurar2')) ?>
        <?= $this->element('filtros/prestadores_combos_acionamento') ?>
        <span class="label label-info">Agrupar por:</span>
        <div id='agrupamento'>
            <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-medium'))) ?>
        </div>
        <?php echo $this->BForm->submit('Filtrar', array('div' => false, 'class' => 'btn' )) ?>
        <?php echo $html->link('Limpar filtro', 'javascript:void(0)', array('id' => 'limpar-filtro-prestadores', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>     
</div>
<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
    setup_mascaras();    
    '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "prestadores/sintetico_listagem/" + Math.random());':'').'
    jQuery("#limpar-filtro-prestadores").click(function(){
        bloquearDiv(jQuery(".form-procurar2"));
        jQuery(".form-procurar2").load(baseUrl + "/filtros/limpar/model:Prestadores/element_name:prestadores_sintetico/" + Math.random())
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