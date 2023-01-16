<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>

			<th class='input-small'>Código</th>
			<th class='input-large'>Descrição</th>
			<th class='input-large'>Observação</th>
			<th class='input-mini'></th>
		</thead>
		<tbody class="line-selector">
			<?php foreach ($listagem as $rota): ?>
			<tr class="rotas-tr" codigo="<?php echo $rota['TRotaRota']['rota_codigo'] ?>" descricao="<?php echo $rota['TRotaRota']['rota_descricao'] ?>" >
				<td><?php echo $rota['TRotaRota']['rota_codigo'] ?></td>
				<td><?php echo $rota['TRotaRota']['rota_descricao'] ?></td>
				<td><?php echo $rota['TRotaRota']['rota_observacao'] ?></td>
				<td id="ver_rota"><?php echo $this->Html->link('','javascript:ver_rota_link('.$rota['TRotaRota']['rota_codigo'].');', array('title' => 'Ver Rota', 'class' => 'icon-eye-open'));?></td>
			</tr>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan = "4"><strong>Total</strong> <?php echo count($listagem); ?></td>
		</tr>
	</tfoot>
</table>
</div>
<?php echo $this->Javascript->codeBlock("
jQuery(document).ready(function() {
	var double = true;

	$('.icon-eye-open').click(function(e){
		e.preventDefault();
		var codigo = $(this).closest('tr').attr('codigo');
		if(codigo != ''){
			var url = '/portal/rotas/mapa?rota_codigo='+codigo+'&edit=false';
			$(this).attr('href',url);
			return open_popup(this,520,520);
		}
	});

	$('tr.rotas-tr td').not('#ver_rota').click(function() {
		if(double){
			double = false;
			var codigo = $(this).parent().attr('codigo');
			var descricao = $(this).parent().attr('descricao');

			var input = $('#{$this->passedArgs['searcher']}');
			input.val(codigo).change();

			var display = $('#{$this->passedArgs['display']}');
			display.val(descricao);

			close_dialog();
		}
	});
});
"); ?>