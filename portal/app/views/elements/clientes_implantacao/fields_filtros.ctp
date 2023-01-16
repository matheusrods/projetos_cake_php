<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'ClienteImplantacao'); ?>
	<?php echo $this->BForm->input('status', array('label' => false, 'class' => 'input-large', 'options' => array(
		'1' => 'Estrutura Pendente',
		'2' => 'PGR Pendente', 
		'3' => 'PCMSO Pendente', 
		'4' => 'Liberado Pendente'
	), 
	'empty' => 'Todos os Status')); ?>
</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes', 'class' => 'btn')) ;?>