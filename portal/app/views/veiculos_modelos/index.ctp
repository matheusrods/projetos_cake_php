<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('class' => 'btn btn-success', 'escape' => false ));?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>

			<th>Modelo</th>
			<th>Fabricante</th>
			<th>Tipo</th>
			<th class='action-icon' colspan="1">Editar</th>
		</thead>
		<tbody>
			<?php foreach ($modelos as $modelo):?>
				<tr>
					<td>
						<?php echo $modelo['VeiculoModelo']['descricao'];?>
					</td>
					<td>
						<?php echo $modelo['VeiculoFabricante']['descricao'];?>
					</td>
					<td>
						<?php echo $modelo['VeiculoTipo']['descricao'];?>
					</td>
					<td>
						<?php echo $html->link('', array('controller' => 'veiculos_modelos', 'action' => 'editar', $modelo['VeiculoModelo']['codigo']), array('class' => 'icon-edit', 'title' => 'Alterar modelo')); ?>
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