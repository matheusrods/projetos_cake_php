<table class='table table-striped'>
	<thead>
		<th>Placa</th>
		<th class="input-mini"></th>
	</thead>

	<tbody>
		<?php foreach ($veiculos as $veiculo): ?>
		<tr>
			<td><?php echo Comum::formatarPlaca($veiculo['Veiculo']['placa']) ?></td>
			<td class="pagination-centered">
				<?php echo $this->Html->link('', array('controller' => 'usuarios','action' => 'excluir_veiculo_alerta', $veiculo['Veiculo']['codigo'], $codigo_usuario, rand()), array('title' => 'Remover Veículo', 'class' => 'icon-trash'));?>
			</td>
		</tr>
	<?php endforeach ?>
</tbody>
</table>
<?php echo $this->Javascript->codeBlock('
	$(function(){
		$(".veiculos a.icon-trash").click(function(){
			if(confirm("Deseja remover este registro?")){
				$.ajax({
					url:$(this).attr("href"),
					dataType: "html",
					success: function(data){
						atualizaListaUsuarioVeiculoAlerta('.$codigo_usuario.');
					}
				});
				
			}

			return false;
		});
	});
');
?>
