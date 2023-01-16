<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'AplicacaoExame'); ?>
	<?php echo $this->BForm->input('codigo_exame', array('label' => false, 'class' => 'input-large', 'options' => $exames, 'empty' => 'Todos os Exames')); ?>
</div>
        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes', 'class' => 'btn')); ?>