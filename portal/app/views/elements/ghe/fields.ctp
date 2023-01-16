<?php //debug($this->validationErrors);?>

<div class='well'>
	<?php if ($edit_mode): ?>
		<?php echo $this->BForm->hidden('codigo'); ?>
	<?php endif;?>

	<div class="row-fluid inline">
		<?php
			if ($is_admin) {
				if ($this->Buonny->seUsuarioForMulticliente()) {
					echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, "Matriz");
				} else {
					echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_matriz', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'Ghe');
				}
			} else {
				if ($this->Buonny->seUsuarioForMulticliente()) {
					echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, "Matriz");
				} else {
					echo $this->BForm->input('codigo_matriz', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
					echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia['Cliente']['nome_fantasia']}"));
				}
			}
		?>

		<?php echo $this->BForm->input('codigo_cliente', array('label' => "Unidade (*)", 'class' => 'input-xlarge', 'options' => array(), 'empty' => 'Selecione uma unidade')); ?>

		<?php echo $this->BForm->input('chave_ghe', array('class' => 'input-xlarge', 'placeholder' => 'Digite a chave ghe', 'label' => 'Chave GHE (*)')) ?>

		<?php
			echo $this->BForm->input('aprho_parecer_tecnico',
				array(
					'label' => 'APRHO parecer técnico (*)',
					'class' => 'input-xlarge',
					'options' => array(
						'Agente abaixo da tolerância' => 'Agente abaixo da tolerância',
						'Agente acima da tolerância' => 'Agente acima da tolerância',
						'Agente acima do nível de ação' => 'Agente acima do nível de ação',
					), 
					'empty' => 'Todos', 
					'default' => ' '
				)
			); 
		?>

		<?php if ($edit_mode): ?>
			<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Status (*)', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos', 'default' => ' ')); ?>
		<?php endif;?>
	</div>

	<div class="row-fluid inline">
		<!-- Setores e Cargos -->
		<div class="span6 div-setor-cargo">
			<div class="row-fluid inline">
				<div class="span12">
					<div class="pull-left">
						<div class="control-group input select">
							<label for="codigo_setor">Setor (*)</label>
							<select name="data[Ghe][setores][1][codigo_setor]" onchange="onChangeSetor(1, this.value)"
								id="codigo_setor_1" style="width: 200px" data-id="1" class="input-medium setor">
								<option value="">Selecione um setor</option>
							</select>
						</div>
					</div>
					<div class="pull-left margin-left-5">
						<div class="control-group input select">
							<label for="codigo_cargo">Cargo (*)</label>
							<select name="data[Ghe][setores][1][codigo_cargo][]" id="codigo_cargo_1"
								style="width: 250px" data-id="1" class="input-xlarge cargo">
								<option value="">Selecione um cargo</option>
							</select>
						</div>
					</div>
					<div class="pull-left margin-left-5">
						<div class="control-group input select">
							<label for="">&nbsp;</label>
							<a href="javascript:void(0)" class="btn btn-success add-setor-cargo">
								<i class="icon-plus icon-white"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Agentes de Riscos e Riscos Impactos -->
		<div class="span6">
			<div class="row-fluid inline">
				<div class="span12">
					<div class="pull-left">
						<?php 
							echo $this->BForm->input('codigo_arrtpa_ri', array(
									'id' => false,
									'options' => array(),
									'empty' => 'Selecione um risco/impacto',
									'label' => 'Riscos Impactos (*)',
									'style' => 'width: 250px',
									'multiple' => 'select',
									'class' => 'input-xlarge risco-impacto'
								)
							); 
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		setup_mascaras();
	});
'); ?>

<script type="text/javascript">
	const carregarClientes = function(codigo_matriz) {
		const input = $('#GheCodigoCliente');

		input.html('');
		input.append($('<option />').val('').text('Carregando...'));

		$.ajax({
			'url': baseUrl + 'ghe/combo_clientes_ajax/' + codigo_matriz + '/' + Math.random(),
			'dataType': 'json',
			'success': function(result) {
				input.html('');
				input.append($('<option />').val('').text('Selecione uma unidade'));

				if (result !== null && Array.isArray(result) && result.length > 0) {
					$.each(result, function(i, r) {
						input.append($('<option />').val(r['Cliente']['codigo']).text(r['Cliente']['nome_fantasia']));
					});
				}
			},
			'error': function() {
				input.html('');
				input.append($('<option />').val('').text('Selecione uma unidade'));
			}
		});
	};

	jQuery(document).ready(function() {
		count = jQuery('.setor').size();
		setor_options = null;

		const codigo_matriz = jQuery('#GheCodigoMatriz').val();
		const multi_cliente = jQuery('#MatrizCodigoCliente').val();

		if (codigo_matriz || multi_cliente) {
			carregarClientes(codigo_matriz || multi_cliente);
		}

		jQuery('[data-toggle=\"tooltip\"]').tooltip();
	});

	jQuery('#GheCodigoMatriz, #MatrizCodigoCliente').change(function() {
		const codigo_matriz = this.value;

		jQuery('.setor').html('<option value="">Selecione um setor</option>');
		jQuery('.cargo').html('<option value="">Selecione um cargo</option>');
		jQuery('.risco-impacto').html('<option value="">Selecione um risco/impacto</option>');

		carregarClientes(codigo_matriz);
	});

	jQuery('#GheCodigoCliente').change(function() {
		const codigo_cliente = this.value;

		comboSetor(codigo_cliente);
		comboRiscosImpactos(codigo_cliente);
	});

	const comboSetor = function(codigo_cliente) {
		jQuery('.setor').html('<option value="">Carregando...</option>');
		jQuery('.cargo').html('<option value="">Selecione um cargo</option>');

		jQuery.ajax({
				url: baseUrl + 'setores/obtem_setores_por_ajax',
				type: 'POST',
				dataType: 'html',
				data: {
					'codigo_cliente': codigo_cliente
				}
			})
			.done(function(response) {
				if (response) {
					setor_options = response;
					jQuery('.setor').html(response);
				} else {
					setor_options = null;
				}
			});
	}

	jQuery(".add-setor-cargo").click(function() {
		count++;
		adiciona_setor_cargo();
	});

	const comboRiscosImpactos = function(codigo_cliente) {
		jQuery('.risco-impacto').html('<option value="">Carregando...</option>');

		if (codigo_cliente) {
			jQuery.ajax({
				url: baseUrl + 'ghe/combo_riscos_impactos',
				type: 'POST',
				dataType: 'html',
				data: {
					'codigo_cliente': codigo_cliente
				}
			})
			.done(function(response) {
				if (response) {
					jQuery('.risco-impacto').html(response).attr('multiple', true);
				} else {
					jQuery('.risco-impacto').html('<option value="">Selecione um risco/impacto</option>');
				}
			});
		} else {
			jQuery('.risco-impacto').html('<option value="">Selecione um risco/impacto</option>');
		}
	}

	const onChangeSetor = function(id, value) {
		jQuery('#codigo_cargo_' + id).html('<option value="">Carregando...</option>');

		jQuery.ajax({
			url: baseUrl + 'cargos/obtem_cargos_por_ajax',
			type: 'POST',
			dataType: 'html',
			data: {
				codigo_cliente: jQuery('#GheCodigoCliente').val(),
				codigo_setor: value
			}
		})
		.done(function(response) {
			if (response) {
				jQuery('#codigo_cargo_' + id).html(response).attr('multiple', true);
			}
		});
	};

	const adiciona_setor_cargo = function() {
		var setor_cargo = '<div class="row-fluid inline row_setor_cargo_' + count + '">' +
			'<div class="span12">' +
			'<div class="pull-left">' +
			'<div class="control-group input select">' +
			'<label for="codigo_setor">Setor (*)</label>' +
			'<select name="data[Ghe][setores][' + count + '][codigo_setor]" onchange="onChangeSetor(' + count +
			', this.value)" id="codigo_setor_' + count + '" style="width: 200px" data-id="' + count +
			'" class="input-medium setor">' +
			'<option value="">Selecione um setor</option>' +
			'</select>' +
			'</div>' +
			'</div>' +
			'<div class="pull-left margin-left-5">' +
			'<div class="control-group input select">' +
			'<label for="codigo_cargo">Cargo (*)</label>' +
			'<select name="data[Ghe][setores][' + count + '][codigo_cargo][]" id="codigo_cargo_' + count +
			'" style="width: 250px" data-id="' + count + '" class="input-xlarge cargo">' +
			'<option value="">Selecione um cargo</option>' +
			'</select>' +
			'</div>' +
			'</div>' +
			'<div class="pull-left margin-left-5">' +
			'<div class="control-group input select">' +
			'<label for="">&nbsp;</label>' +
			'<a href="javascript:void(0)" onclick="removerSetorCargo(' + count +
			')" class="btn btn-danger" data-id="' + count + '">' +
			'<i class="icon-remove icon-white"></i>' +
			'</a>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>';

		jQuery(".div-setor-cargo").append(setor_cargo);

		if (setor_options != null) {
			jQuery('#codigo_setor_' + count).html(setor_options);
		}
	}

	const removerSetorCargo = function(id) {
		jQuery('.row_setor_cargo_' + id).remove();
	}
</script>
