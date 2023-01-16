<div class='actionbar-right'>
	<?php if(empty($questoes)) { ?>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'questoes', 'action' => 'incluir', 'questao', $codigo_questionario), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar novo questionário'));?>
	<?php } else { ?>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'questoes', 'action' => 'incluir', 'resposta', $codigo_questionario), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar nova resposta'));?>
	<?php } ?>
</div>

<div class='lista'>	
	<?php if(!empty($questoes)):?>
		<?php echo $paginator->options(array('update' => 'div.lista')); ?>
		<table class="table table-striped table-adjusts">
			<thead>
				<tr>
					<th class="">Descrição</th>
					<th class="acoes" style="width:75px">Ações</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($questoes as $key => $dados): ?>
					<tr data-codigo="<?php echo $dados['Questao']['codigo'] ?>" class="questions data-src" data-nivel="1" data-src="<?php echo ($key+1) ?>">
						<td class=""><i class="icon-chevron-down"></i><strong>Pergunta:</strong> <?php echo $dados['LabelQuestao']['label'] ?></td>
						<td class="js-actions">
							<?php echo $this->Html->link('', array('action' => 'alterar', $codigo_questionario, $dados['Questao']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?> &nbsp;

							<?php echo $this->Html->link('', array('action' => 'incluir', 'resposta', $codigo_questionario, $dados['Questao']['codigo']), array('class' => 'icon-plus ', 'title' => 'Adicionar resposta')); ?> &nbsp;

							<?php echo $this->Html->link('', array('action' => 'excluir', $codigo_questionario, $dados['Questao']['codigo']), array('class' => 'icon-trash delete-confirm', 'title' => 'Excluir', 'data-title' => 'Tem certeza?', 'data-text' => 'Esta operação também exclui as questões vinculadas a este questionário')); ?> &nbsp;

						</td>
					</tr>
					<?php if(!empty($dados['Respostas'])) { ?>
					<?php foreach($dados['Respostas'] as $key2 => $resposta) { ?>
					<tr class="answers open-questions data-src" data-codigo="<?php echo $resposta['codigo_proxima_questao'] ?>"  data-nivel="1" data-src="<?php echo ($key+1) ?>" data-contaC="<?php echo ($key+1).($key2+1) ?>">
						<td>
							<div class="padding-adjust" style="padding-left: 15px;">
								<?php if(!empty($resposta['codigo_proxima_questao'])) { ?>
								<i class="icon-chevron-right"></i> 
								<?php } else { ?>
								<div style="width: 18px;" class="pull-left">&nbsp;</div>
								<?php } ?>
								<strong>Resposta:</strong> <?php echo $resposta['Respostas'][0]['label'] ?>
								<?php if($resposta['pontos'] > 1) {
									echo '('.$resposta['pontos'].' pontos)';
								} elseif($resposta['pontos'] == 1) {
									echo '('.$resposta['pontos'].' ponto)';
								} ?>
							</div>
						</td>
						<td class="js-actions">
							<?php echo $this->Html->link('', array('action' => 'alterar', $codigo_questionario, $resposta['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?> &nbsp;
							
							<?php if(empty($resposta['codigo_proxima_questao'])) { ?>
							<?php echo $this->Html->link('', array('action' => 'incluir', 'questao', $codigo_questionario, $resposta['codigo']), array('class' => 'icon-plus ', 'title' => 'Adicionar resposta')); ?> &nbsp;
							<?php } ?>

							<?php echo $this->Html->link('', array('action' => 'excluir', $codigo_questionario, $resposta['codigo']), array('class' => 'icon-trash delete-confirm', 'title' => 'Excluir', 'data-title' => 'Tem certeza?', 'data-text' => 'Esta operação também exclui as questões vinculadas a este questionário')); ?> &nbsp;
						</td>
					</tr>
					<?php } ?>
					<?php } ?>
				<?php endforeach ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Questao']['count']; ?></td>
				</tr>
			</tfoot>    
		</table>
		<div class='row-fluid'>
			<div class='numbers span6'>
				<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
				<?php echo $this->Paginator->numbers(); ?>
				<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
			</div>
			<div class='counter span7'>
				<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>

			</div>
		</div>
	<?php else:?>
		<div class="alert">Nenhum dado foi encontrado.</div>
	<?php endif;?>    
</div>
<?php echo $this->Javascript->codeBlock("
$(document).ready(function() {
		deleteAlert();
		gerencia_arvore();
	});
", false) ?>