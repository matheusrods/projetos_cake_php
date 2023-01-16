<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>

<?php if(count($clientes) > 0): ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="input-mini">Código Cliente</th>
				<th class="input-mini">Mês/Ano</th>
				<th>Data Inclusão</th>
				<th>Cliente</th>
				<th class="input-mini numeric">Qtd Enviado E-mail</th>
				<th class="input-mini numeric">Qtd Processado</th>
				<th class="input-mini numeric">Qtd À Faturar</th>
				<th>Data Processamento</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($clientes as $cliente) : 

					$codigo_cliente = $cliente['CtrPreFatPerCapita']['codigo_cliente_matricula'];
			?>
				<tr>
					<td class="input-mini"><?= $codigo_cliente ?></td>
					<td><?php echo $cliente['CtrPreFatPerCapita']['mes_ano'] ?></td>
					<td><?php echo $cliente['CtrPreFatPerCapita']['data_inclusao'] ?></td>
					<td><?php echo $cliente['Cliente']['razao_social'] ?></td>

					<td class="input-mini numeric">
						<?= is_null($cliente['CtrPreFatPerCapita']['qtd_total_email']) ? ' - ' :  $this->Buonny->moeda($cliente['CtrPreFatPerCapita']['qtd_total_email'], array('nozero' => true, 'places' => 0));
						?>
					</td>
					<td class="input-mini numeric">
						<?php 
							$qtd_processado = $cliente['CtrPreFatPerCapita']['qtd_processado'];

							if(is_null($qtd_processado)):
								echo (' - '); 
							else:
								$qtd_processado = $this->Buonny->moeda($qtd_processado, array('nozero' => true, 'places' => 0));

								$codigo_importacao_estrutura = $cliente['CtrPreFatPerCapita']['codigo_importacao_estrutura'];

								$url = "/importar/gerenciar_importacao_estrutura/{$codigo_cliente}/{$codigo_importacao_estrutura}";
								echO( $this->Html->link($qtd_processado, $url) );
							endif;
						?>
					</td>
					<td class="input-mini numeric">
						<?php 

							$qtd_a_faturar = $cliente['CtrPreFatPerCapita']['qtd_a_faturar'];

							if(is_null($cliente['CtrPreFatPerCapita']['qtd_a_faturar'])):
								echo(' - ');
							else:

							 	$url = "listagem_pagadores/{$codigo_cliente}";
								echo( $this->Html->link($qtd_a_faturar, $url) );

							endif;
						?>
					</td>
					<td>
						<?= is_null($cliente['CtrPreFatPerCapita']['data_alteracao']) ? ' - ' : $cliente['CtrPreFatPerCapita']['data_alteracao'] ?>
					</td>
				</tr>
			<?php endforeach; ?>        
		</tbody>
	</table>
	<div class='row-fluid'>
		<div class='numbers span6'>
			<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
			<?php echo $this->Paginator->numbers(); ?>
			<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
		</div>
		<div class='counter span6'>
			<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		</div>
	</div>
<?php else:?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif;?>
<?php echo $this->Js->writeBuffer(); ?>