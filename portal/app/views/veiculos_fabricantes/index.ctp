<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('class' => 'btn btn-success', 'escape' => false ));?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>

			<th>Fabricantes</th>
			<th class='action-icon' colspan="1">Editar</th>
		</thead>
		<tbody>
			<?php foreach ($fabricantes as $fabricante):?>
				<tr>
					<td>
						<?php echo $fabricante['VeiculoFabricante']['descricao'];?>
					</td>
					<td>
						<?php echo $html->link('', array('controller' => 'veiculos_fabricantes', 'action' => 'editar', $fabricante['VeiculoFabricante']['codigo']), array('class' => 'icon-edit', 'title' => 'Alterar Fabricante')); ?>
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