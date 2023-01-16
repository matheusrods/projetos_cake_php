<?php //debug($nome_fantasia); ?>

<div class='well'>
    <?php if($edit_mode): ?>
        <?php echo $this->BForm->hidden('codigo'); ?>
    <?php endif; ?>

    <div class="row-fluid inline">
        <?php
            echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
        ?>
    </div>

    <div class="row-fluid inline">
        <?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descrição', 'label' => 'Descrição (*)')) ?>
    </div>

    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>
