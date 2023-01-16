<table class='table table-striped'>
	<thead>
		<th>Endereço IP</th>
		<th class="input-mini"></th>
	</thead>

	<tbody>
		<?php foreach ($usuario_ips as $listaIp): ?>
		<tr>
			<td><?php echo $listaIp['UsuarioIp']['endereco_ip'] ?></td>
			<td class="pagination-centered">
				<?php echo $this->Html->link('', array('action' => 'excluir', $listaIp['UsuarioIp']['codigo'], rand()), array('title' => 'Remover IP', 'class' => 'icon-trash'));?>
			</td>
		</tr>
	<?php endforeach ?>
</tbody>
</table>
<?php echo $this->Javascript->codeBlock('
	$(function(){
		$(".listaIps a.icon-trash").click(function(){
			if(confirm("Deseja remover este registro?")){
				$.ajax({
					url:$(this).attr("href"),
					dataType: "html",
					success: function(data){
						atualizaListaIps('.$codigo_usuario.');
					}
				});
				
			}

			return false;
		});
	});
');
?>
