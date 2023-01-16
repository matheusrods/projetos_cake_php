<div class='well'>
  <?php echo $bajax->form('TTtraTipoTransporte', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TTtraTipoTransporte', 'element_name' => 'tipo_transportes'), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('tipo_transportes/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaTipoTransporte("tipo_transportes");
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TTtraTipoTransporte/element_name:tipo_transportes/" + Math.random())
        });
        
        
    });', false);
?>