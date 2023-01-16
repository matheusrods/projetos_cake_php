<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h3 id="myModalLabel">Editar Setor / Cargo</h3>
</div>
<div class="modal-body">
	<div class="js-form">
		<div class="row-fluid">
			<div class="pull-left ">
				<?php echo $this->Form->hidden('codigo', array('id' => false, 'value' => $fsc['FuncionarioSetorCargo']['codigo'])); ?>
				<?php echo $this->Form->hidden('codigo_cliente_funcionario', array('id' => false, 'value' => $fsc['ClienteFuncionario']['codigo'])); ?>
				<?php echo $this->BForm->input('data_inicio', array('id' => false, 'type' => 'text', 'label' => '* Data de início:', 'class' => 'data input-small', 'value' => AppModel::dbDateToDate($fsc['FuncionarioSetorCargo']['data_inicio']))); ?>
			</div>
			<div class="pull-left margin-left-10">
				<?php echo $this->BForm->input('data_fim', array('id' => false, 'type' => 'text', 'label' => 'Data fim:', 'class' => 'data input-small', 'value' => AppModel::dbDateToDate($fsc['FuncionarioSetorCargo']['data_fim']))); ?>
			</div>
		</div>

		<div class="row-fluid js-line js-editar">
			<div class="pull-left">
				<?php echo $this->BForm->input('codigo_cliente_alocacao', array('id' => false, 'options' => $unidades, 'default' => !empty($fsc['FuncionarioSetorCargo']['codigo_cliente_alocacao'])? $fsc['FuncionarioSetorCargo']['codigo_cliente_alocacao'] : null, 'label' => 'Unidade:', 'empty' => 'Selecione uma Unidade', 'class' => 'input-xlarge js-unidade')); ?>
			</div>
			<div class="pull-left margin-left-10">
				<?php echo $this->BForm->input('codigo_setor', array('id' => false, 'options' => $fsc['FuncionarioSetorCargo']['Setores'], 'empty' => 'Selecione um Setor', 'default' => !empty($fsc['FuncionarioSetorCargo']['codigo_setor'])? $fsc['FuncionarioSetorCargo']['codigo_setor'] : null, 'label' => 'Setor:', 'class' => 'input-xlarge js-setor')); ?>
			</div>
			<div class="pull-left margin-left-10">
				<?php echo $this->BForm->input('codigo_cargo', array('id' => false, 'options' => $fsc['FuncionarioSetorCargo']['Cargos'], 'empty' => 'Selecione um Cargo', 'default' => !empty($fsc['FuncionarioSetorCargo']['codigo_cargo'])? $fsc['FuncionarioSetorCargo']['codigo_cargo'] : null, 'label' => 'Cargo', 'class' => 'input-xlarge js-cargo'));?>

			</div>

		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
	<button type="button" class="btn btn-primary salvar">Salvar</button>
</div>
<script type="text/javascript">

	$(document).ready(function() {
		$('.js-unidade').change(function() {
			este = $(this);
			este.parents('.js-line').find('.js-setor').html('<option value="">Carregando...</option>');
			este.parents('.js-line').find('.js-cargo').html('<option value="">Selecione um Cargo</option>');
			$.ajax({
				url: baseUrl + 'setores/obtem_setores_por_ajax',
				type: 'POST',
				dataType: 'html',
				data: { codigo_cliente: this.value }
			})
			.done(function(response) {
				if(response) {
					este.parents('.js-line').find('.js-setor').html(response);
				}
			})
		});
		$('.js-setor').change(function() {
			este = $(this);
			este.parents('.js-line').find('.js-cargo').html('<option value="">Carregando...</option>');
			$.ajax({
				url: baseUrl + 'cargos/obtem_cargos_por_ajax',
				type: 'POST',
				dataType: 'html',
				data: { codigo_cliente: este.parents('.js-line').find('.js-unidade').val(), codigo_setor: this.value }
			})
			.done(function(response) {
				if(response) {
					este.parents('.js-line').find('.js-cargo').html(response);
				}
			})
		});
		$('[data-toggle=\"tooltip\"]').tooltip();
		setup_mascaras(); 
		setup_datepicker();
		setup_time();
	});

	$('.salvar').click(function(event) {
		var dados = new Object();
		este = $(this).parents('.editar-setor-cargo');
		if(!valida_se_existe_data(este.find('.data').first().val())) {
			return false;
		}
		if(!valida_se_data_fim_menor_que_data_inicio(este.find('.data').first().val(), este.find('.data').last().val())) {
			return false;
		}
		var button = $(this).html();
		este.find('[data-dismiss="modal"]').hide();
		$(this).css({height: $(this).outerHeight(), width: $(this).outerWidth() }).html('<img src="' + baseUrl + 'img/default.gif">');
		$(este.find('input, select')).each(function(index, val) {
			dados[val.name.substr(0, val.name.length - 1).substr(5)] = val.value;
		});
		$.ajax({
			url: baseUrl + 'clientes_funcionarios/edita_setor_cargo',
			type: 'POST',
			dataType: 'json',
			data: {dados: dados},
		})
		.done(function(response) {
			atualizaLista('<?php echo $codigo_cliente ?>', '<?php echo $fsc["ClienteFuncionario"]["codigo_funcionario"] ?>');
			este.modal('hide');
			$(this).html(button);
			este.find('[data-dismiss="modal"]').show();
			if(response.error) {
				swal({
					type: 'warning',
					title: 'Atenção',
					text: response.message
				});
			} else {
				swal({
					type: 'success',
					title: 'Sucesso',
					text: 'O setor e cargo foram atualizados com sucesso.'
				});
			}
		})
		.fail(function() {
			este.modal('hide');
			$(this).html(button);
			este.find('[data-dismiss="modal"]').show();
		})
	});

</script>