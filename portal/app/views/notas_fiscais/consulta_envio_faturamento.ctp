<div class='well'>
	<?php echo $this->BForm->create('RetornoNf', array('url' => array('controller' => 'notas_fiscais', 'action' => 'consulta_envio_faturamento'))); ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('nota_fiscal', array('label' => false, 'placeholder' => 'Nota Fiscal','class' => 'input-small')) ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end() ?>
</div>
<?php if (isset($dados)): ?>
    <strong>Email: </strong><?php echo $dados['Outbox']['to'] ?><br>
    <strong>Data envio: </strong><?php echo $dados['Outbox']['sent'] ?><br>
    <strong>Banco: </strong><?php echo $dados['Banco']['banco'] . ' - ' . $dados['Banco']['descricao'] ?><br>
<?php endif ?>