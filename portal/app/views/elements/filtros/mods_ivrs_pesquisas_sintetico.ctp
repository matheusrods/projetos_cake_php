<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('ModIvrPesquisa', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ModIvrPesquisa', 'element_name' => 'mods_ivrs_pesquisas_sintetico'), 'divupdate' => '.form-procurar')) ?>
         <div class="row-fluid inline">
           <?= $this->element('filtros/mods_ivrs_pesquisas_filtros') ?>
        </div>
        <div class="row-fluid inline">
            <span class="label label-info">Agrupar por:</span>
            <div id='agrupamento'>
                <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'legend' => false, 'label' => array('class' => 'radio inline input-medium'))) ?>
            </div>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn' )) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "mods_ivrs_pesquisas/sintetico_listagem/" + Math.random());':'').'
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.grafico");bloquearDiv(div);div.load(baseUrl + "mods_ivrs_pesquisas/sintetico_grafico/"+ Math.random());':'').'        
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ModIvrPesquisa/element_name:mods_ivrs_pesquisas_sintetico/" + Math.random())
            jQuery(".lista").empty();
            jQuery(".grafico").empty();
        });
        jQuery("#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        
    });', false);
?>
<?php
if (!empty($filtrado)):
    echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');
endif; 
?>

