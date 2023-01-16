<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir_classificacao'), array('class' => 'btn btn-success', 'escape' => false ));?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>

			<th>Classificações</th>
			<th class='action-icon' colspan="1">Editar</th>
		</thead>
		<tbody>
			<?php foreach ($classificacoes as $classificacao):?>
				<tr>
					<td>
						<?php echo $classificacao['VeiculoClassificacao']['descricao'];?>
					</td>
					<td>
						<?php echo $html->link('', array('controller' => 'veiculos_classificacao', 'action' => 'editar_classificacao', $classificacao['VeiculoClassificacao']['codigo']), array('class' => 'icon-edit', 'title' => 'Alterar Classificação')); ?>
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
</div>