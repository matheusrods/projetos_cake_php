<div class='well'>
    <?php $searcher = !empty($searcher)? $searcher : $this->data['Fornecedor']['searcher'];?>
    <?php $display = !empty($display)? $display : $this->data['Fornecedor']['display'];?>
  <?php echo $bajax->form('Fornecedor', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Fornecedor', 'element_name' => 'fornecedores_buscar_codigo', 'searcher' => $searcher, 'display' => $display), 'divupdate' => '.form-procurar-codigo-fornecedor')) ?>
    <?php echo $this->element('fornecedores/buscar_codigo_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro-fornecedores").click(function(){
            bloquearDiv(jQuery(".form-procurar-codigo-fornecedor"));
            jQuery(".form-procurar-codigo-fornecedor").load(baseUrl + "/filtros/limpar/model:Fornecedor/element_name:fornecedores_buscar_codigo/searcher:'.$searcher.'/display:'.$display.'/" + Math.random())
        });
        atualizaListaFornecedoresVisualizar("fornecedores_buscar_codigo", "'.$searcher.'","'.$display.'");
    });', false);
?>