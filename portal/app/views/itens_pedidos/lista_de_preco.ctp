<option value=''>Selecione um serviço</option>
<?php foreach ($servicos as $key => $servico): ?>		
	<option value='<?= $servico['ListaDePrecoProdutoServico']['codigo'] ?>'><?= $servico['Servico']['descricao']; ?></option>
<?php endforeach ?>