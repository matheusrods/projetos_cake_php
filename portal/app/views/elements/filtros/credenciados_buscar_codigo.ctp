<div class='well'>
    <?php $input_id = !empty($input_id)? $input_id : $this->data['PropostaCredenciamento']['input_id'];?>
    <?php $input_display = !empty($input_display)? $input_display : $this->data['PropostaCredenciamento']['input_display'];?>
  <?php echo $bajax->form('PropostaCredenciamento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PropostaCredenciamento', 'element_name' => 'credenciados_buscar_codigo', 'searcher' => $input_id, 'display' => $input_display), 'divupdate' => '.form-procurar-codigo-proposta-credenciamento')) ?>
    <?php echo $this->element('propostas_credenciamento/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro-credenciados").click(function(){
            bloquearDiv(jQuery(".form-procurar-codigo-proposta-credenciamento"));
            jQuery(".form-procurar-codigo-proposta-credenciamento").load(baseUrl + "/filtros/limpar/model:PropostaCredenciamento/element_name:credenciados_buscar_codigo/searcher:'.$input_id.'/display:'.$input_display.'/" + Math.random())
        });
        atualizaListaCredenciadosVisualizar("credenciados_buscar_codigo", "'.$input_id.'","'.$input_display.'");
    });', false);
?>