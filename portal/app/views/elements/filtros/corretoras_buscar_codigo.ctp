<div class='well'>
  <?php echo $bajax->form('Corretora', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Corretora', 'element_name' => 'corretoras_buscar_codigo', 'searcher' => $input_id, 'display' => $input_display), 'divupdate' => '.form-procurar-codigo-corretora')) ?>
    <?php echo $this->element('corretoras/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro-corretoras").click(function(){
            bloquearDiv(jQuery(".form-procurar-codigo-corretora"));
            jQuery(".form-procurar-codigo-corretora").load(baseUrl + "/filtros/limpar/model:Corretora/element_name:corretoras_buscar_codigo/searcher:'.$input_id.'/display:'.$input_display.'/" + Math.random())
        });
        atualizaListaCorretorasVisualizar("corretoras_buscar_codigo", "'.$input_id.'","'.$input_display.'");
    });', false);
?>