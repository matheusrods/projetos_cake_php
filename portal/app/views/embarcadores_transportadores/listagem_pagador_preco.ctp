<?php if(isset($dados_preco) && !empty($dados_preco)) :?>
<div class="well">
	<div class='row-fluid inline'>
		<?php if (isset($embarcador['Cliente']['razao_social'])): ?>
			<strong>Embarcador: </strong><?= $embarcador['Cliente']['razao_social'] ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php endif;?>
		<?php if (isset($transportador['Cliente']['razao_social'])): ?>
			<strong>Transportador: </strong><?= $transportador['Cliente']['razao_social'] ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php endif;?>
		<?php if (isset($produto['Produto']['descricao'])): ?>
			<strong>Produto: </strong><?= $produto['Produto']['descricao'] ?>
		<?php endif;?>
	</div>
</div>

<table class="table">

    <thead>
        <tr>
            <th class='input-mini'>Código</th>
            <th class='input-large'>Pagador</th>
            <th class='input-mini'>Serviço</th>
            <th class='input-mini numeric'>Valor (R$)</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($dados_preco as $dado): ?>
        <tr>
            <td><?= $dado['ClienteProduto']['codigo_cliente'] ?></td>
			<td><?= $dado[0]['cliente_pagador'] ?></td>
			<td><?= $dado['Servico']['descricao'] ?></td>
            <td class='input-mini numeric'><?= $this->Buonny->moeda($dado['ClienteProdutoServico2']['valor']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
		<?php if (isset($transportador['Cliente']['razao_social']) && isset($produto['Produto']['descricao'])): ?>
		<div class="alert">
	
			O transportador <strong><?= $transportador['Cliente']['razao_social'] ?></strong> não possui nenhum contrato para o produto <strong><?= $produto['Produto']['descricao'] ?></strong>.
			</div>
		<?php endif;?>
	

<?php endif; ?>