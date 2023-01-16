<?php echo $this->BForm->create('PosQtdParticipantes', array('url' => array('controller' => 'swt','action' => 'incluir_qtd_participantes', $codigo_cliente))); ?>
    
    <div class='well'>	
		<?php
    	echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));
		echo $this->BForm->hidden('codigo', array('value' => !empty($this->data['PosQtdParticipantes']['codigo'])? $this->data['PosQtdParticipantes']['codigo'] : '') );
		?>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('quantidade', array('label' => 'Quantidade (*)', 'class' => 'input-small')); ?>	
		</div>
	</div>

    <div class='form-actions'>
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        <?= $html->link('Voltar', array('controller' => 'swt', 'action' => 'index_qtd_participantes'), array('class' => 'btn')); ?>
    </div>

<?php echo $this->BForm->end(); ?>
