<div class='well'>
  <?php echo $bajax->form('Produto', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Produto', 'element_name' => 'produtos'), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('produtos/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaProdutos("produtos");
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Produto/element_name:produtos/" + Math.random())
        });
        
        
    });', false);
?>