	<br />
	<?php if($cliente): ?>
	<div class='actionbar-right'>
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir',$cliente['Cliente']['codigo']), array('class' => 'btn btn-success', 'escape' => false )); ?>
	</div>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
	</div>
	

	<div class='row-fluid inline'>
		<table class='table table-striped'>
			<thead>
				<th>Descrição</th>
				
				<th style="width:13px"></th>
				<th style="width:13px"></th>
			</thead>
			<tbody>
				
				<?php foreach ($listagem as $codigo => $descricao):?>
					<tr>
						<td>
							<?php echo $descricao ?>
						</td>
						<td>
							<?php echo $html->link('', array('controller' => 'Regioes', 'action' => 'alterar',$cliente['Cliente']['codigo'], $codigo), array('class' => 'icon-edit', 'title' => 'Alterar Região')); ?>
						</td>
        				<td>
        					<?php echo $html->link('', array('controller' => 'Regioes', 'action' => 'remover', $codigo, rand()), array('onclick' => 'return open_dialog(this, "Remover Região", 560)', 'title' => 'Remover Região', 'class' => 'icon-trash')) ?>
        				</td>
					</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan = "4">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<?php endif; ?>
	</div>