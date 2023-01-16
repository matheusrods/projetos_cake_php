<div class='well'>
  <?php echo $bajax->form('ClienteImplantacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteImplantacao', 'element_name' => "clientes_implantacao"), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('clientes_implantacao/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div> 

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaClientesImplantacao();
        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteImplantacao/element_name:clientes_implantacao/" + Math.random())
        });
    });', false);
?>