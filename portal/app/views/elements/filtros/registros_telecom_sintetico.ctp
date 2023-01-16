<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('RegistroTelecom', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RegistroTelecom', 'element_name' => 'registros_telecom_sintetico'), 'divupdate' => '.form-procurar')) ?>
        <?= $this->element('filtros/registros_telecom') ?>
        <span class="label label-info">Agrupar por:</span>
        <div id='agrupamento'>
            <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-medium'))) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn' )) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-registro-telecom', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>     
</div>
<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){


    '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "registros_telecom/sintetico_listagem/" + Math.random());':'').'

    jQuery("#limpar-filtro-registro-telecom").click(function(){
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RegistroTelecom/element_name:registros_telecom_sintetico/" + Math.random())
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
    echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');
endif; 
?>