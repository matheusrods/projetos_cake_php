<?php if (!isset($dados)): ?>
	<div class='well'>
		<?php echo $this->BForm->create('Notaite', array('url' => array('controller' => 'itens_notas_fiscais', 'action' => 'comparativo_anual'))); ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('ano', array('label' => false, 'placeholder' => 'Ano','class' => 'input-small', 'type' => 'select', 'options' => $anos)) ?>
			<?php echo $this->BForm->input('grupo_empresa', array('legend' => false, 'class' => 'input-small', 'options' => $grupos_empresas, 'type' => 'radio', 'label' => array('class' => 'radio inline'))) ?>
			<?php echo $this->BForm->input('empresa', array('label' => false, 'placeholder' => 'Empresa', 'class' => 'input-large', 'options' => $empresas, 'empty' => 'Todas empresas')) ?>
			<?php echo $this->BForm->input('codigo_produto', array('label' => false, 'placeholder' => 'Código Produto', 'class' => 'input-small', 'type' => 'text')) ?>
			<?php echo $this->BForm->input('codigo_cliente', array('label' => false, 'placeholder' => 'Código Cliente', 'class' => 'input-small', 'type' => 'text')) ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $this->BForm->end();?>
		<?php $this->addScript($this->Buonny->link_js('itens_notas_fiscais')) ?>
	</div>
<?php else: ?>
	<div class="row-fluid">
		<div class="lista">
			<?php if(isset($dados) && isset($dados2)): ?>
				<div class="well">
					<?php if (!(isset($gestor) || isset($corretora) || isset($seguradora)) && isset($nome_grupo) && !empty($nome_grupo)): ?>
						<strong>Grupo: </strong><?php echo $nome_grupo; ?>
					<?php endif ?>
					<?php if (!(isset($gestor) || isset($corretora) || isset($seguradora)) && isset($empresa) && !empty($empresa)): ?>
						<strong>Empresa: </strong><?php echo $empresa['LojaNaveg']['razaosocia']; ?>
					<?php endif ?>
					<?php if (isset($cliente) && !empty($cliente)): ?>
						<strong>Código: </strong><?php echo $cliente['Cliente']['codigo']; ?>
						<strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social']; ?>
					<?php elseif (isset($gestor) && !empty($gestor)): ?>						
						<strong>Gestor: </strong><?php echo $gestor['Gestor']['nome']; ?>
					<?php elseif (isset($corretora) && !empty($corretora)): ?>						
						<strong>Corretora: </strong><?php echo $corretora['Corretora']['nome']; ?>
					<?php elseif (isset($seguradora) && !empty($seguradora)): ?>						
						<strong>Seguradora: </strong><?php echo $seguradora['Seguradora']['nome']; ?>
					<?php endif ?>
					<?php if (isset($produto) && !empty($produto)): ?>
                       <strong>Produto: </strong><?php echo $produto['NProduto']['descricao']; ?>
					<?php endif ?>
				</div>
				<?php $ano1 = substr($dados[0][0]['ano_mes'], -4) ?>
				<?php $ano2 = substr($dados2[0][0]['ano_mes'], -4) ?>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th></th>
							<th class="numeric"><?= $ano1."(R$)"; ?></th>
							<th class="numeric"><?= $ano2."(R$)"; ?></th>
							<th class="numeric">Diferença(%)</th>
						</tr>
					</thead>
					<tbody>
						<?php $total_nota_ano1 = 0 ?>
						<?php $total_nota_ano2 = 0 ?>
						<?php $acumulado_ano1 = 0 ?>
						<?php $acumulado_ano2 = 0 ?>
						<?php $mes_acumulado = 0 ?>
						<?php foreach($dados as $key => $dado): ?>
							<?php $mes = str_replace("/","",substr($dado[0]['ano_mes'], 0, 2)); ?>
							<?php $diferenca = 0; ?>
							<?php if($dado[0]['preco'] != 0 && $dados2[$key][0]['preco'] != 0): ?>
								<?php if($dado[0]['preco'] > $dados2[$key][0]['preco']): ?>
									<?php $diferenca = (100 * $dados2[$key][0]['preco']) / $dado[0]['preco']; ?>
									<?php $diferenca = 100-$diferenca; ?>
								<?php elseif($dado[0]['preco'] < $dados2[$key][0]['preco']): ?>
									<?php $diferenca = ($dados2[$key][0]['preco'] / $dado[0]['preco']) * 100; ?>
									<?php if ($diferenca > 0): ?>
										<?php $diferenca = $diferenca-100; ?>
									<?php endif ?>
								<?php endif; ?>
							<?php endif; ?>
							<?php if ($ano2 < date('Y') || $mes <= date('m')): ?>
								<?php $acumulado_ano1 += $dado[0]['preco']; ?>
								<?php $acumulado_ano2 += $dados2[$key][0]['preco']; ?>
								<?php $mes_acumulado ++ ?>
							<?php endif ?>
							<?php $negative = ($dado[0]['preco'] > $dados2[$key][0]['preco'] && $diferenca != 0) ?>
							<tr>
								<td><?php echo $this->Buonny->mes_extenso($mes); ?></td>
								<td class="numeric"><?php echo $this->Buonny->moeda($dado[0]['preco']); ?></td>
								<td class="numeric"><?php echo $this->Buonny->moeda($dados2[$key][0]['preco']); ?></td>
								<td class="numeric <?= ($negative ? 'negative_value' : '') ?>"><?= ($negative ? '-' : '').$this->Buonny->moeda(round($diferenca, 2)) ?></td>
							</tr>
							<?php $total_nota_ano1 = $total_nota_ano1 + $dado[0]['preco']; ?>
							<?php $total_nota_ano2 = $total_nota_ano2 + $dados2[$key][0]['preco']; ?>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<?php if ($ano2 == date('Y')): ?>
							<?php $diferencaAcumulado = 0; ?>
							<?php if($acumulado_ano1 != 0 && $acumulado_ano2 != 0): ?>
								<?php $diferencaAcumulado = (($acumulado_ano2 / $acumulado_ano1)-1) * 100; ?>
							<?php endif; ?>
							<?php $negative = ($acumulado_ano1 > $acumulado_ano2 && $diferencaAcumulado != 0) ?>
							<tr>
								<td>Total até <?= $this->Buonny->mes_extenso($mes_acumulado) ?></td>
								<td class="numeric"><?php echo $this->Buonny->moeda(round($acumulado_ano1, 2)) ?></td>
								<td class="numeric"><?php echo $this->Buonny->moeda(round($acumulado_ano2, 2)) ?></td>
								<td class="numeric <?= ($negative ? 'negative_value' : '') ?>"><?= ($negative ? '-' : '').$this->Buonny->moeda(round($diferencaAcumulado, 2)) ?></td>
							</tr>
						<?php endif ?>
						<?php $diferencaTotais = 0; ?>
						<?php if($total_nota_ano1 != 0 && $total_nota_ano2 != 0): ?>
							<?php $diferencaTotais = (($total_nota_ano2 / $total_nota_ano1)-1) * 100 ?>
						<?php endif; ?>
						<?php $negative = ($total_nota_ano1 > $total_nota_ano2 && $diferencaTotais != 0) ?>
						<tr>
							<td>Total</td>
							<td class="numeric"><?php echo $this->Buonny->moeda(round($total_nota_ano1, 2)) ?></td>
							<td class="numeric"><?php echo $this->Buonny->moeda(round($total_nota_ano2, 2)) ?></td>
							<td class="numeric <?= ($negative ? 'negative_value' : '') ?>"><?= ($negative ? '-' : '').$this->Buonny->moeda(round($diferencaTotais, 2)) ?></td>
						</tr>
					</tfoot>
				</table>
			<?php endif; ?>
		</div>
	</div>
<?php endif ?>