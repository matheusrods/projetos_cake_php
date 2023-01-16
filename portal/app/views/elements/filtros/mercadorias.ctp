<div class='well'>
  <?php echo $bajax->form('TProdProduto', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TProdProduto', 'element_name' => 'mercadorias'), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('mercadorias/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaMercadorias("mercadorias");
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TProdProduto/element_name:mercadorias/" + Math.random())
        });
        
        
    });', false);
?>