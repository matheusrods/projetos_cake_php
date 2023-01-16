<?php $empty = 'Selecione um usuário' ?>
<option value=''><?php echo $empty ?></option>
<?php if (count($results)): ?>
	<?php foreach ($results as $result): ?>
		<option value="<?php echo $result['Usuario']['codigo'] ?>" <?php echo count($results) == 1 ? 'selected="true"' : '' ?>><?php echo $result['Usuario']['apelido'] ?></option>
	<?php endforeach ?>
<?php endif ?>