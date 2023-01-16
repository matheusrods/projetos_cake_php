<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>

			<th class='input-small'>Id</th>
			<th class='input-large'>Razão Social</th>
			<th class='input-large'>CNPJ</th>

		</thead>
		<tbody>
			<?php foreach ($listagem as $escolta):?>
				<tr class="escolta-tr" codigo="<?php echo $escolta['TPjurPessoaJuridica']['pjur_pess_oras_codigo'] ?>" descricao="<?php echo $escolta['TPjurPessoaJuridica']['pjur_razao_social'] ?>" >
					<td><?php echo $escolta['TPjurPessoaJuridica']['pjur_pess_oras_codigo'] ?></td>
					<td><?php echo $escolta['TPjurPessoaJuridica']['pjur_razao_social'] ?></td>
					<td><?php echo Comum::formatarDocumento($escolta['TPjurPessoaJuridica']['pjur_cnpj']) ?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan = "3"><strong>Total</strong> <?php echo count($listagem); ?></td>
			</tr>
		</tfoot>
	</table>
</div>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
	$('tbody').attr('class', 'line-selector');
	var double = true;
	$('tr.escolta-tr').click(function() {
		if(double){
			double = false;
			var codigo = $(this).attr('codigo');
			var descricao = $(this).attr('descricao');

			var input = $('#{$this->passedArgs['searcher']}');
			input.val(codigo).change();

			var display = $('#{$this->passedArgs['display']}');
			display.val(descricao);

			close_dialog();
		}
	})
})"); ?>