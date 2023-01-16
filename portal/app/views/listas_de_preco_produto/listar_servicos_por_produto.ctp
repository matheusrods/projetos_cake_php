<?php $empty = 'Serviço' ?>
<option value=''><?php echo $empty ?></option>
<?php if (count($servicos)): ?>
	<?php foreach ($servicos as $result): ?>
		<option value="<?php echo $result['Servico']['codigo'] ?>" <?php echo count($servicos) == 1 ? 'selected="true"' : '' ?>><?php echo $result['Servico']['descricao'] ?></option>
	<?php endforeach ?>
<?php endif ?>