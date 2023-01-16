<table class='table table-striped'>
	<thead>
		<th>Descrição</th>
		<th class="input-mini"></th>
	</thead>

	<tbody>
		<?php foreach ($tecnologias as $tecnologia): ?>
		<tr>
			<td><?php echo $tecnologia['TTecnTecnologia']['tecn_descricao'] ?></td>
			<td class="pagination-centered">
				<?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "return excluir_tecnologia('{$tecnologia['TPjtePjurTecn']['pjte_codigo']}')", 'title' => 'Remover Tecnologia', 'class' => 'icon-trash')) ?>
			</td>
		</tr>
	<?php endforeach ?>
</tbody>
</table>
<?php echo $this->Javascript->codeBlock("function excluir_tecnologia(pjte_codigo) {
	if(confirm('Deseja remover este registro?')){
		$.ajax({
			url: baseUrl + 'clientes_tecnologias_sm/excluir/'+ pjte_codigo + '/' + Math.random(),
			dataType: 'html',
			beforeSend: function() {
				bloquearDiv($('.tecnologias-cliente'));
			},
			success: function(data){
				atualizaListaConfiguracaoTecnologia('{$this->passedArgs[0]}');
			}
		});				
	}
	return false;
}");
?>
