<?php if(!empty($clientes)): ?>
	
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>
	
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="input-mini">Código</th>
				<th>Data Inclusão</th>
				<th>Nome</th>
				<th class="input-mini">Estrutura</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($clientes as $cliente) :?>

				<tr>
					<td class="input-mini"><?php echo $cliente['ClienteImplantacao']['codigo_cliente'] ?></td>
					<td><?php echo $cliente['Cliente']['data_inclusao'] ?></td>
					<td><?php echo $cliente['Cliente']['razao_social'] ?></td>

					<?php
					switch($cliente['ClienteImplantacao']['estrutura']) :
						case 'A' :
						$botao_estrutura = 'badge badge-empty badge-info';
						$texto_estrutura = 'Andamento';
						break;
						case 'C' :
						$botao_estrutura = 'badge badge-empty badge-success';
						$texto_estrutura = 'Concluído';
						break;
						default :
						$botao_estrutura = 'badge badge-empty badge-important';
						$texto_estrutura = 'Pendente';
					endswitch;
					?>

					<td class="input-mini">
						<?php if(isset($botao_estrutura)) : ?>
							<?php if($cliente['ClienteImplantacao']['auth_est']) { ?>
							<?php echo $html->link($texto_estrutura, '/clientes_implantacao/estrutura/' . $cliente['ClienteImplantacao']['codigo_cliente'].'/implantacao/terceiros_implantacao', array('class' => $botao_estrutura)); ?>
							<?php } else { ?>
							<span class="access-denied" data-toggle="tooltip" title="Não disponível"><?php echo $texto_estrutura; ?></span>
							<?php } ?>
						<?php endif; ?>
					</td>
				</tr>
				
				<?php unset($botao_estrutura, $texto_estrutura, $botao_ppra, $texto_ppra, $botao_pcmso, $texto_pcmso, $botao_liberado, $texto_liberado); ?>			
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
<?php echo $this->Js->writeBuffer(); ?>
<?php else: ?>
	<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock("
	$(document).ready(function() {
		$('[data-toggle=\"tooltip\"]').tooltip();
	});
	"); ?>	