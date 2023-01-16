<?php //debug($nome_fantasia); ?>

<div class='well'>
    <?php if($edit_mode): ?>
        <?php echo $this->BForm->hidden('codigo'); ?>
    <?php endif; ?>

    <div class="row-fluid inline">
        <?php
        if($edit_mode){
            echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
        } else {
            echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$this->params['pass'][0]}"));
        }

        echo $this->BForm->input('ativo', array('type' => 'hidden', 'value' => "1"));
        echo $this->BForm->input('codigo_pos_ferramenta', array('type' => 'hidden', 'value' => "3"));
        ?>
    </div>

    <div class="row-fluid inline">
        <?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descrição', 'label' => 'Descrição (*)')) ?>
    </div>

    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php

    if($edit_mode){
        echo $html->link('Voltar', array('action' => 'gerenciar', $codigo_cliente), array('class' => 'btn'));
    } else {
        echo $html->link('Voltar', array('action' => 'gerenciar', $this->params['pass'][0]), array('class' => 'btn'));
    }

     ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>
