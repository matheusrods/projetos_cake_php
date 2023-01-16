<?php echo $this->BForm->create('TErasEstacaoRastreamento',array('url' => array('controller' => 'eras_estacoes_rastreamentos','action' => 'editar'), 'type' => 'POST')) ?>

	<div class="row-fluid inline">
		<?php echo $this->BForm->hidden('eras_codigo')?>
		<?php echo $this->BForm->input('eras_descricao',array('class' => 'input-large', 'label' => 'Descrição','readonly' => TRUE)) ?>
		<?php echo $this->BForm->input('eras_ramal',array('class' => 'input-mini', 'label' => 'Ramal')) ?>
		<?php echo $this->BForm->input('eras_logistico', array('label' => 'Estação Logistico', 'class' => 'input-large', 'options' => $estacoes, 'empty' => 'Selecione uma estação')); ?>

	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		<?php echo $html->link('Voltar',array('controller'=>'eras_estacoes_rastreamentos','action'=>'listar_estacoes_rastreamento') , array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>