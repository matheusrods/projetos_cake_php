<table class='table table-striped'>
	<thead>
		<th>Descrição</th>
		<th>Carreteiro</th>
		<th>Outros</th>
		<th class="input-mini"></th>
	</thead>

	<tbody>
		<?php foreach ($produtos as $produto):
			$carreteiro =  $produto['TPjprPjurProd']['pjpr_permite_carreteiro'];
			$outros     =  $produto['TPjprPjurProd']['pjpr_permite_outros'];
		?>
		<tr>
			<td><?php echo $produto['TProdProduto']['prod_descricao'] ?></td>
			<td>
				<?php echo ($carreteiro === NULL || $carreteiro == TRUE ? $this->Html->image('icon-check.png') : $this->Html->image('icon-error.png'));?>
			</td>
			<td>
				<?php echo ($outros === NULL || $outros == TRUE ? $this->Html->image('icon-check.png') : $this->Html->image('icon-error.png'));?>
			</td>			
			<td class="pagination-centered">
				<?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "return excluir_produto('{$produto['TPjprPjurProd']['pjpr_codigo']}')", 'title' => 'Remover Tecnologia', 'class' => 'icon-trash')) ?>
			</td>
		</tr>
	<?php endforeach ?>
</tbody>
</table>
<?php echo $this->Javascript->codeBlock("function excluir_produto(pjpr_codigo){
	if(confirm('Deseja remover este registro?')){
		$.ajax({
			url: baseUrl + 'clientes_produtos_sm/excluir/'+ pjpr_codigo + '/' + Math.random(),
			dataType: 'html',
			beforeSend: function() {
				bloquearDiv($('.produtos-cliente'));
			},
			success: function(data){
				atualizaListaConfiguracaoTipoProduto('{$this->passedArgs[0]}');
			}
		});				
	}
	return false;
}");
?>
