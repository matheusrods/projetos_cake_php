<?php $codigo_fornecedor_matriz = empty($this->passedArgs[0])?$this->passedArgs['codigo_fornecedor_matriz']:$this->passedArgs[0];?>
<div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor_unidade', 'Unidade',false,'FornecedorUnidade') ?>            
            <?php echo $this->BForm->hidden('codigo', array('value' => $codigo_fornecedor_matriz,'hiddenField' => false)); ?>
</div>       