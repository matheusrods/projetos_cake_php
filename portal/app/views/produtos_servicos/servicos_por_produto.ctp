<option value=''>Selecione um serviço</option>
<?php foreach ($servicos as $key => $servico): ?>
	<option value='<?= $key ?>'><?= $servico ?></option>
<?php endforeach ?>