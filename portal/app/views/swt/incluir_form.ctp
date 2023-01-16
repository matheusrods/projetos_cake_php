<?php echo $this->BForm->create('PosSwtForm', array('url' => array('controller' => 'swt','action' => 'incluir_form', $codigo_cliente))); ?>
    
    <div class='well'>	
		<?php
    	echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));
		echo $this->BForm->hidden('codigo', array('value' => !empty($this->data['PosSwtForm']['codigo'])? $this->data['PosSwtForm']['codigo'] : '') );
		?>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('form_tipo', array('label' => 'Tipo (*)', 'class' => 'input', 'default' => '1', 'options' => $form_tipo)); ?>
		</div>

	</div>

    <div class='form-actions'>
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        <?= $html->link('Voltar', array('controller' => 'swt', 'action' => 'index_form'), array('class' => 'btn')); ?>
    </div>

<?php echo $this->BForm->end(); ?>
