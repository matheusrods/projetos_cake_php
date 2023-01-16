<div class='row-fluid inline'>
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_pagador', 'Cliente', true) ?>
    <?php echo $this->BForm->input('codigo_produto', array('label' => 'Produto', 'empty' => 'Selecione um produto', 'options' => $produtos)) ?>
    <?php echo $this->BForm->input('valor_acima_irrf', array('label' => 'Valor Acima IRRF', 'class' => 'input-small numeric moeda')) ?>
    <?php echo $this->BForm->input('percentual_irrf', array('label' => '% IRRF', 'class' => 'input-small numeric moeda')) ?>
    <?php echo $this->BForm->input('valor_acima_formula', array('label' => 'Valor Acima', 'class' => 'input-small numeric moeda')) ?>
    <?php echo $this->BForm->input('codigo_formula_naveg', array('label' => 'Código Fórmula', 'class' => 'input-mini')) ?>
</div>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() { setup_mascaras() })") ?>