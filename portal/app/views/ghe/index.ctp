<div class='form-procurar'>
	<?php echo $this->element('/filtros/ghe') ?>
</div>
<div class='actionbar-right' style="margin-bottom: 10px;">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'ghe', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Cadastrar novo GHE')); ?>
</div>

<div class='lista'></div>

<script type="text/javascript">
    const carregarUnidades = function(codigo_matriz) {
		const input = $('#GheCodigoUnidade');

		input.html('');
		input.append($('<option />').val('').text('Carregando...'));

		$.ajax({
			'url': baseUrl + 'ghe/combo_clientes_ajax/' + codigo_matriz + '/' + Math.random(),
			'dataType': 'json',
			'success': function(result) {
				const multi_cliente = codigo_matriz.indexOf(",") !== -1;

				input.html('');

				if (result !== null && Array.isArray(result) && result.length > 0) {
					const valor = result.map(item => item['Cliente']['codigo']).join(",");

					input.append($('<option />').val(valor).text(`Todos (${result.length})`));

					$.each(result, function(i, r) {
						input.append($('<option />').val(r['Cliente']['codigo']).text(r['Cliente']['nome_fantasia']));
					});
				} else {
					input.append($('<option />').val('').text('Selecione uma unidade'));
				}
			},
			'error': function() {
				input.html('');
				input.append($('<option />').val('').text('Selecione uma unidade'));
			}
		});
	};

	document.addEventListener('change', (e) => {
		const input = e.target;

		if (input.id === "GheCodigoCliente" || input.id === "ClienteCodigoCliente") {
			carregarUnidades(input.value);
		}
	});
</script>