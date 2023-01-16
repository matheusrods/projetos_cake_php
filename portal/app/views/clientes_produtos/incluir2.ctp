<?php $produto_anterior = ''; ?>
<div class='well'>
	<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
    <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
	<!-- <strong>Lista de Preço: </strong><?= $lista_de_preco['ListaDePreco']['descricao'] ?>  -->
</div>
<?php echo $this->BForm->create('ClienteProdutoServico2', array('type' => 'POST','autocomplete' => 'off', 'url' => array('controller' => 'clientes_produtos', 'action' => 'incluir2', $this->passedArgs[0], $this->passedArgs[1]))) ;
 //echo 'www'.$this->BForm->error("ClienteProdutoServico2.quantidade.36");?>
<table class='table'>
	<thead>
		<tr>
			<th class='action-icon'></th>
			<th class='input-large'>Produto / Serviço</th>
			<th class='input-medium numeric'>Cód. Cliente Pagador</th>
			<th class='input-medium numeric'>Taxa Bancária</th>
			<th class='input-medium numeric'>Taxa Corretora</th>
			<th class='input-small'>Tipo Prêmio Mínimo</th>
			<th class='input-small numeric' title='Prêmio Mínimo'>PM (R$)</th>
			<th class='input-small numeric'>Valor</th>		
		</tr>
	</thead>
	<tbody>
		<?php if (count($produtos)): ?>
			<?php foreach($produtos as $produto): ?>
				<?php foreach($produto['ListaDePrecoProdutoServico'] as $servico): ?>
					<?php $tipo_premio_minimo = $servico['valor_premio_minimo'] > 0 ? 2 : 1 ?>
					<?php $valor_premio_minimo = $tipo_premio_minimo == 1 ? $produto['ListaDePrecoProduto']['valor_premio_minimo'] : $servico['valor_premio_minimo'] ?>
					<?php $qtd_premio_minimo = $tipo_premio_minimo == 1 ? $produto['ListaDePrecoProduto']['qtd_premio_minimo'] : $servico['qtd_premio_minimo'] ?>

					<?php if($produto['Produto']['descricao'] != $produto_anterior): ?>
						<?php $produto_anterior = $produto['Produto']['descricao']; ?>
						<tr><td colspan='11'><i class="icon-chevron-right"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= $produto['Produto']['descricao'] ?></strong></td></tr>
					<?php endif; ?>

					<tr>
						<td class='action-icon'>
						<?= $this->BForm->input("ClienteProdutoServico2.codigo_lista_de_preco_produto_servico.{$servico['codigo']}", array('label' => false, 'type' => 'checkbox', 'name' => "data[ClienteProdutoServico2][codigo_lista_de_preco_produto_servico][{$servico['codigo']}]"))  ?></td>
						<td class='first'><?= $servico['Servico']['descricao'] ?></td>
						<td class='numeric'>
							<?= $this->BForm->input("codigo_cliente_pagador.{$servico['codigo']}", array('label' => false, 'class' => 'input-small ', 'name' => "data[ClienteProdutoServico2][codigo_cliente_pagador][{$servico['codigo']}]", 'maxlength' => 11, 'default' => "{$cliente['Cliente']['codigo']}" )) ?>
						</td>
						<td class='numeric'>
						<?=  $this->BForm->input("valor_taxa_bancaria.{$servico['codigo']}", array('type' => $produto['Produto']['quantitativo'] ? 'text' : 'hidden', 'label' => false, 'maxlength' => false, 'name' => "data[ClienteProdutoServico2][valor_taxa_bancaria][{$servico['codigo']}]", 'class' => 'input-mini numeric moeda', 'default' => 0))  ?></td>
						<td class='numeric'><?= $this->BForm->input("valor_taxa_corretora.{$servico['codigo']}", array('type' => $produto['Produto']['quantitativo'] ? 'text' : 'hidden', 'label' => false, 'maxlength' => false, 'name' => "data[ClienteProdutoServico2][valor_taxa_corretora][{$servico['codigo']}]", 'class' => 'input-mini numeric moeda', 'default' => 0)) ?></td>
						<td><?= $valor_premio_minimo == 0 ? '' : ($tipo_premio_minimo == 1 ? 'por Produto' : 'por Serviço') ?></td>
						<td class='numeric'><?= $this->BForm->input("valor_premio_minimo.{$servico['codigo']}", array('type' => $produto['Produto']['quantitativo'] ? 'text' : 'hidden','label' => false, 'class' => 'input-small numeric moeda', 'name' => "data[ClienteProdutoServico2][valor_premio_minimo][{$servico['codigo']}]"))?></td>
						<td class='numeric'><?= $this->BForm->input("valor.{$servico['codigo']}", array('label' => false, 'class' => 'input-small numeric moeda', 'name' => "data[ClienteProdutoServico2][valor][{$servico['codigo']}]", 'maxlength' => 14)) ?></td>
						<?php echo $this->BForm->hidden("tipo_premio_minimo][{$servico['codigo']}]",array('value' => ($tipo_premio_minimo == 1 ? 'PRODUTO' : 'SERVICO'))); ?>
					</tr>
				<?php endforeach ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'assinatura'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {setup_mascaras()});") ?>