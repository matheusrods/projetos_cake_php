<div class="row-fluid inline">
	<?php echo $this->BForm->input('data_inclusao_inicio', array('placeholder'=>'Data Inicial', 'label' => 'Inicio', 'class' => 'input-small data', 'title' => 'Inicio')); ?>
	
	<?php echo $this->BForm->input('data_inclusao_fim', array('placeholder'=>'Data Final', 'label' => 'Fim', 'class' => 'input-small data', 'title' => 'Fim')); ?>
	
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Pagador', 'Pagador','logFaturamento_pagador') ?>
	
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Ultilizador', 'Utilizador','logFaturamento_utilizador') ?> 
	
	<?php if(empty($authUsuario['Usuario']['codigo_seguradora'])): ?>
		<?php echo $this->BForm->input('codigo_seguradora', array('label' => 'Seguradora', 'class' => 'input-medium', 'options' => $seguradoras, 'empty' => 'Todas')); ?>
	<?php endif; ?>
	
	<?php echo $this->Buonny->input_codigo_corretora($this, 'codigo_corretora', 'Corretora', 'Corretora','logFaturamento_corretora') ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_gestor', array('label' => 'Gestor', 'class' => 'input-medium', 'options' => $gestores, 'empty' => 'Todos')); ?>
	<?php echo $this->Buonny->input_codigo_endereco_regiao($this, $filiais, 'Todas','codigo_endereco_regiao', 'Filiais', 'Cliente') ?>
	<?php echo $this->BForm->input('codigo_produto', array('label' => 'Produtos', 'class' => 'input-medium', 'options' => $produtos, 'empty' => 'Todos')); ?>
	<?php echo $this->BForm->input('codigo_servico', array('label' => 'Serviços', 'class' => 'input-medium', 'options' => $servicos, 'empty' => 'Todos')); ?>


	<?php echo $this->BForm->input('tipo_arquivo', array('label' => 'Cobrado', 'class' => 'input-medium', 'options' => array(
		'1' => 'Sim', 
		'2'  => 'Não',
		 ), 'empty' => 'Todos')); ?>
	<?php echo $this->BForm->input('tipo_arquivo', array('label' => 'SM Online', 'class' => 'input-medium', 'options' => array(
		'1' => 'Sim', 
		'2'  => 'Não',
	), 'empty' => 'Todos')); ?>

</div>
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-ultilizacao_servicos', 'class' => 'btn')) ;?>