<table class='table table-striped'>
	<thead>
		<th>Gerenciadora</th>
		<th></th>
	</thead>

	<tbody>
		<?php foreach ($gerenciadoras as $gerenciadora): ?>
		<tr>
			<td><?php echo $gerenciadora['TPjurGerenciadoraRisco']['pjur_razao_social'] ?></td>
			<td class="pagination-centered">
				<?php echo $this->Html->link('', array('action' => 'excluir_gerenciadora', $gerenciadora['TGpjuGerenciadoraPessoaJur']['gpju_codigo'], rand()), array('title' => 'Remover Gerenciadora', 'class' => 'icon-trash excluir-gerenciadora'));?>
			</td>
		</tr>
	<?php endforeach ?>
</tbody>
</table>
<?php echo $this->Javascript->codeBlock('
	$(function(){
		$("a.excluir-gerenciadora").click(function(){
			if(confirm("Deseja remover este registro?")){
				$.ajax({
					url:$(this).attr("href"),
					dataType: "html",
					success: function(data){
						atualizaClienteGerenciadoras('.$codigo_cliente.');
					}
				});
				
			}
			return false;
		});
	});
');
?>