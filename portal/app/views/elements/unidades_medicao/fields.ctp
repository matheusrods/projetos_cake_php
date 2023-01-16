<?php //debug($this->validationErrors); ?>

<div class='well'>
	<?php if($edit_mode): ?>
		<?php echo $this->BForm->hidden('codigo'); ?>
	<?php endif; ?>

<!--	<div class="row-fluid inline">-->
<!--        --><?php
//        if ($this->Buonny->seUsuarioForMulticliente()) {
//            echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'RiscosTipo');
//        } else {
//            echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'RiscosTipo');
//        }
//        ?>
<!--    </div>-->

    <div class="row-fluid inline">
		<?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descrição', 'label' => 'Descrição (*)')) ?>

        <?php echo $this->BForm->input('inteiro', array('class' => 'input-small', 'placeholder' => 'Inteiro', 'label' => 'Inteiro (*)')) ?>

	</div>

	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>
