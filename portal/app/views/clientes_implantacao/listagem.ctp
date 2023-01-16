<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<?php if(!empty($clientes)): ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="input-mini">Código</th>
				<th>Data Inclusão</th>
				<th>Nome</th>
				<th class="input-mini">Estrutura</th>
				<th class="input-mini">PGR</th>
				<th class="input-mini">PCMSO</th>
				<th class="input-mini">Liberado</th>
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

					switch($cliente['ClienteImplantacao']['ppra']) :
						case 'A' :
						$botao_ppra = 'badge badge-empty badge-info';
						$texto_ppra = 'Andamento';
						break;
						case 'C' :
						$botao_ppra = 'badge badge-empty badge-success';
						$texto_ppra = 'Concluído';
						break;
						default :
						if(!is_null($cliente['ClienteImplantacao']['estrutura']) && $cliente['ClienteImplantacao']['estrutura'] != 'A') {
							$botao_ppra = 'badge badge-empty badge-important';
							$texto_ppra = 'Pendente';								
						}
					endswitch;

					switch($cliente['ClienteImplantacao']['pcmso']) :
					case 'A' :
					$botao_pcmso = 'badge badge-empty badge-info';
					$texto_pcmso = 'Andamento';
					break;
					case 'C' :
					$botao_pcmso = 'badge badge-empty badge-success';
					$texto_pcmso = 'Concluído';
					break;
					default :
					if(!is_null($cliente['ClienteImplantacao']['ppra']) && $cliente['ClienteImplantacao']['ppra'] != 'A') {
						$botao_pcmso = 'badge badge-empty badge-important';
						$texto_pcmso = 'Pendente';								
					}
					endswitch;

					switch($cliente['ClienteImplantacao']['liberado']) :
					case 'A' :
					$botao_liberado = 'badge badge-empty badge-info';
					$texto_liberado = 'Andamento';
					break;
					case 'C' :
					$botao_liberado = 'badge badge-empty badge-success';
					$texto_liberado = 'Concluído';
					break;
					default :
					if(!is_null($cliente['ClienteImplantacao']['pcmso']) && $cliente['ClienteImplantacao']['pcmso'] != 'A') {
						$botao_liberado = 'badge badge-empty badge-important';
						$texto_liberado = 'Pendente';
					}
					endswitch;	 
					?>

					<td class="input-mini">
						<?php if(isset($botao_estrutura)) : ?>
							<?php if($cliente['ClienteImplantacao']['auth_est']) { ?>
							<?php echo $html->link($texto_estrutura, '/clientes_implantacao/estrutura/' . $cliente['ClienteImplantacao']['codigo_cliente'].'/implantacao', array('class' => $botao_estrutura)); ?>
							<?php } else { ?>
							<span class="access-denied" data-toggle="tooltip" title="Não disponível"><?php echo $texto_estrutura; ?></span>
							<?php } ?>
						<?php endif; ?>
					</td>
					<td class="input-mini">
						<?php if(isset($botao_ppra)) : ?>
							<?php if($cliente['ClienteImplantacao']['auth_ppra']) { ?>
							<?php echo $html->link($texto_ppra, '/clientes_implantacao/gerenciar_ppra/' . $cliente['ClienteImplantacao']['codigo_cliente'], array('class' => $botao_ppra)); ?>
							<?php } else { ?>
							<span class="access-denied" data-toggle="tooltip" title="Não disponível"><?php echo $texto_ppra; ?></span>
							<?php } ?>
						<?php endif; ?>
					</td>
					<td class="input-mini">
						<?php if(isset($botao_pcmso)) : ?>
							<?php if($cliente['ClienteImplantacao']['auth_pcmso']) { ?>
							<?php echo $html->link($texto_pcmso, '/clientes_implantacao/gerenciar_pcmso/' . $cliente['ClienteImplantacao']['codigo_cliente'], array('class' => $botao_pcmso)); ?>
							<?php } else { ?>
							<span class="access-denied" data-toggle="tooltip" title="Não disponível"><?php echo $texto_pcmso; ?></span>
							<?php } ?>
						<?php endif; ?>
					</td>
					<td class="input-mini">
						<?php if(isset($botao_liberado)) : ?>
							<?php if($cliente['ClienteImplantacao']['auth_est']) { ?>
							<?php echo $html->link($texto_liberado, 'javascript:void(0);', array('class' => $botao_liberado, 'onclick' => 'atualizaLiberado(this, '.$cliente['ClienteImplantacao']['codigo_cliente'].')')); ?>
							<?php } else { ?>
							<span class="access-denied" data-toggle="tooltip" title="Não disponível"><?php echo $texto_liberado; ?></span>
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
<?php else: ?>
	<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock("
	$(document).ready(function() {
		$('[data-toggle=\"tooltip\"]').tooltip();
	});
	function atualizaLiberado(element, cliente) {
		swal({
			type: 'success',
			title: 'Confirmação',
			text: 'Confirma liberação do cliente?',
			showCancelButton: true,
			cancelButtonText: 'Cancelar'
		}, function(isConfirm) {
			if(isConfirm) {
				bloquearDiv($('.lista'));
				$.post(baseUrl + 'clientes_implantacao/atualiza_status/' + cliente + '/liberado/C', function(response) {
						if(response > 0) {
							$(element).removeClass('badge-important').addClass('badge-success').text('Concluido');
							$('.blockUI').remove();
						} else {
							swal({
								type: 'warning',
								title: 'Atenção',
								text: 'Para liberar a implantação, verifique se os passos anteriores foram concluídos com sucesso.'
							});
							$('.blockUI').remove();
						}
				});
			}
		});
		
	}
	"); ?>	