<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('class' => 'btn btn-success', 'escape' => false ));?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>

			<th>Fabricantes</th>
			<th>Classificações</th>
			<th class='action-icon' colspan="1">Editar</th>
		</thead>
		<tbody>
			<?php foreach ($tipos as $tipo):?>
				<tr>
					<td>
						<?php echo $tipo['VeiculoTipo']['descricao'];?>
					</td>
					<td>
						<?php echo $tipo['VeiculoClassificacao']['descricao'];?>
					</td>
					<td>
						<?php echo $html->link('', array('controller' => 'veiculos_tipos', 'action' => 'editar', $tipo['VeiculoTipo']['codigo']), array('class' => 'icon-edit', 'title' => 'Alterar tipo')); ?>
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