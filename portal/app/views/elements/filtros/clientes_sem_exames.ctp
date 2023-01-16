<div class='well'>
  <?php echo $bajax->form('AplicacaoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AplicacaoExame', 'element_name' => "clientes_sem_exames"), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('clientes_sem_exames/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div> 

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaClientesSemExames();
        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AplicacaoExame/element_name:clientes_sem_exames/" + Math.random())
        });
    });', false);
?>