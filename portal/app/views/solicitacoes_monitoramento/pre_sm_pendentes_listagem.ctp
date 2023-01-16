<table class="table table-striped">
    <thead>
        <tr>
			<th class="input-mini">Código</th>
			<th class="input-medium">Dt. Inclusão</th>
			<th class="input-medium">Origem</th>
			<th class="input-large">Destino(s)</th>
			<th class="input-large">Pedido Cliente</th>
			<th class="input-mini">&nbsp;</th>
        </tr>
    </thead>
    <tbody class="line-selector">
		<?php foreach ($pre_sm_pendentes as $pre_sm): ?>
	        <tr>
				<td class="input-mini" ><?= $pre_sm['TPviaPreViagem']['pvia_codigo'] ?></td>
				<td class="date"><?=$pre_sm['TPviaPreViagem']['pvia_data_cadastro'] ?></td>
				<td><?=$pre_sm['pre_sm']['Recebsm']['refe_codigo_origem_visual'] ?></td>
				<td>
					<?php foreach ($pre_sm['pre_sm']['RecebsmAlvoDestino'] as $alvo):?>
						<?=$alvo['refe_codigo_visual']?><br/>
					<?php endforeach; ?>
				</td>
				<td><?=(isset($pre_sm['TPviaPreViagem']['pvia_pedido_cliente']) ? $pre_sm['TPviaPreViagem']['pvia_pedido_cliente'] : '') ?></td>
				<td >
					<input type="button" class="btn btn-success" title="Selecionar Pré-SM" codigo="<?=$pre_sm['TPviaPreViagem']['pvia_codigo'] ?>" value="Selecionar">
				</td>
			</tr>
		<?php endforeach; ?>        
    </tbody>
</table>
<script>
jQuery(document).ready(function() {
		$('tbody').attr('class', 'line-selector');
		var double = true;
		$('.btn-success').click(function() {
			if(double){
				double = false;
				var codigo = $(this).attr('codigo');
				var input = $('#RecebsmCodigoPreSm');
				input.val(codigo);
				close_dialog();

				$('#RecebsmConsultarParaIncluirForm').submit();
			}
		})
	})
</script>
