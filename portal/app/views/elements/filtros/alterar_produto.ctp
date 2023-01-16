<div class="well">
    <?php echo $this->Bajax->form('ClienteProduto', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteProduto', 'element_name' => 'alterar_produto'), 'divupdate' => '.form-procurar')); ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
        </div>
        <div class="control-group">
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $this->BForm->end(); ?>
        </div>
    
</div>

<?php
echo $this->Javascript->codeBlock("$(document).ready(function() { atualizaListaFichasAlterarProduto(); });", false);
?>
