<?php if(!empty($funcionario_matriculas)):?>
	<?php foreach ($funcionario_matriculas as $key1 => $funcionario_matricula): ?>

		<?php switch ($funcionario_matricula['ClienteFuncionario']['ativo']){
			case 1:
			$status = 'Ativo';
			break;
			case 2:
			$status = 'Férias';
			break;
			case 3:
			$status = 'Afastado';
			break;
			case 0:
			$status = 'Inativo';
			break;
			default:
			$status = 'Inativo';
			break;
		}
		?>
		<div class="well">      
			<div>
				<strong>Código:</strong> <?php echo $funcionario_matricula['ClienteFuncionario']['codigo'] ?>&nbsp;&nbsp;&nbsp;
				<strong>Data admissão:</strong> <?php echo $funcionario_matricula['ClienteFuncionario']['admissao'] ?>&nbsp;&nbsp;&nbsp;
				<strong>Matrícula:</strong> <?php echo $funcionario_matricula['ClienteFuncionario']['matricula'] ?>&nbsp;&nbsp;&nbsp;
				<strong>Status:</strong> <?php echo $status; ?>&nbsp;&nbsp;&nbsp;
				<strong>Data demissão:</strong> <?php echo $funcionario_matricula['ClienteFuncionario']['data_demissao'] ?: '-'; ?>&nbsp;&nbsp;&nbsp;
                <strong>Centro de Custo:</strong> <?php echo $funcionario_matricula['ClienteFuncionario']['centro_custo'] ?: '-'; ?>
                <br />
				<button type="button" class="btn btn-danger pull-right btn-small js-excluir-matricula" data-codigo="<?php echo $funcionario_matricula['ClienteFuncionario']['codigo'] ?>" data-toggle="tooltip" title="Excluir matrícula">Excluir matrícula</button>
				<button type="button" class="btn btn-warning pull-right btn-small js-editar-matricula margin-right-10" data-codigo="<?php echo $key1 ?>" data-toggle="tooltip" title="Editar matrícula">Editar matrícula</button>
			</div>
			<hr style="margin: 20px 0 10px 0;">
			<div class="text-right margin-bottom-10">
				<?php echo $this->Form->button('<i class="icon-plus icon-white"></i>', array('type' => 'button', 'class' => 'btn btn-success btn-small js-novo-setor-cargo', 'data-codigo' => $key1, 'data-toggle' => 'tooltip', 'data-html' => true, 'title' => 'Inserir novo setor/cargo', 'escape' => false)); ?>
			</div>
			<table class="table">
				<thead>
					<tr>
						<th class='input-small'>
							Inicio
						</th>
						<th class='input-small'>
							Fim
						</th>
						<th>
							Unidade
						</th>
						<th class='input-medium'>
							Setor
						</th>
						<th class='input-medium'>
							Cargo
						</th>
						<th class='input-mini'>
							Ações
						</th>
					</tr>
				</thead>

				<tbody>

					<?php
					if(isset($funcionario_matricula['FuncionarioSetorCargo'])) {						
					?>

						<?php foreach ($funcionario_matricula['FuncionarioSetorCargo'] as $key => $funcionario_setor_cargo) { ?>
						<tr <?php echo empty($funcionario_setor_cargo['data_fim'])? 'style="background: rgba(248,148,6,0.23)"' : '' ?>>
							<td>
								<?php echo AppModel::dbDateToDate($funcionario_setor_cargo['data_inicio']) ?>
							</td>
							<td>
								<?php echo AppModel::dbDateToDate($funcionario_setor_cargo['data_fim']) ?>
							</td>
							<td>
								<?php echo (!empty($funcionario_setor_cargo['Cliente']['nome_fantasia'])? $funcionario_setor_cargo['Cliente']['nome_fantasia'] : '') ?>
							</td>
							<td class='input-medium'>
								<?php echo (!empty($funcionario_setor_cargo['Setor']['descricao'])? $funcionario_setor_cargo['Setor']['descricao'] : ''); ?>
							</td>
							<td class='input-medium'>
								<?php echo (!empty($funcionario_setor_cargo['Cargo']['descricao'])? $funcionario_setor_cargo['Cargo']['descricao'] : ''); ?>
							</td>
							<td class='input-mini'>
								
								<?php echo $this->Html->link('', '#', array('class' => 'icon-edit js-editar-setor-cargo', 'data-codigo' => $key1.'-'.$key, 'data-codigo-fsc' => $funcionario_setor_cargo['codigo'], 'data-toggle' => 'tooltip', 'title' => 'Editar')); ?> &nbsp;
								

								<?php echo $this->Html->link('', '#', array('class' => 'icon-remove js-remover-setor-cargo', 'data-toggle' => 'tooltip', 'title' => 'Excluir', 'data-codigo' => $funcionario_setor_cargo['codigo'])); ?>

							</td>
						</tr>

						<!-- Modal editar setor/cargo -->
						<div id="editarSetorCargo-<?php echo $key1?>-<?php echo $key?>" class="modal modal-large hide fade editar-setor-cargo" tabindex="-1" role="dialog" aria-labelledby="editarSetorCargo-<?php echo $key1?>-<?php echo $key?>Label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h3 id="myModalLabel">Editar Setor / Cargo</h3>
							</div>
							<div class="modal-body">
								<div class="js-form">
									<div class="row-fluid">
										<div class="pull-left ">
											<?php echo $this->Form->hidden('codigo', array('id' => false, 'value' => $funcionario_setor_cargo['codigo'])); ?>
											<?php echo $this->Form->hidden('codigo_cliente_funcionario', array('id' => false, 'value' => $funcionario_matricula['ClienteFuncionario']['codigo'])); ?>

											<?php echo $this->BForm->input('data_inicio', array('id' => false, 'type' => 'text', 'label' => '* Data de início:', 'class' => 'data input-small', 'value' => AppModel::dbDateToDate($funcionario_setor_cargo['data_inicio']))); ?>
										</div>
										<div class="pull-left margin-left-10">
											<?php echo $this->BForm->input('data_fim', array('id' => false, 'type' => 'text', 'label' => 'Data fim:', 'class' => 'data input-small', 'value' => AppModel::dbDateToDate($funcionario_setor_cargo['data_fim']))); ?>
										</div>
									</div>
									<div class="row-fluid js-line js-editar">
										
										<?php if(!isset($funcionario_setor_cargo['PedidoExame'])): ?>
											<div class="pull-left">
												<?php echo $this->BForm->input('codigo_cliente_alocacao', array('id' => false, 'options' => $unidades, 'default' => !empty($funcionario_setor_cargo['codigo_cliente_alocacao'])? $funcionario_setor_cargo['codigo_cliente'] : null, 'label' => '* Unidade:', 'empty' => 'Selecione uma Unidade', 'class' => 'input-xlarge js-unidade js-unidade-tomador')); ?>
											</div>
											<div class="pull-left margin-left-10">
												<?php echo $this->BForm->input('codigo_setor', array('id' => 'edit_codigo_setor_'.$funcionario_setor_cargo['codigo_cliente_alocacao'], 'options' => $funcionario_setor_cargo['Setores'], 'empty' => 'Selecione um Setor', 'default' => !empty($funcionario_setor_cargo['Setor']['codigo'])? $funcionario_setor_cargo['Setor']['codigo'] : null, 'label' => '* Setor:', 'class' => 'input-xlarge js-setor')); ?>
											</div>
											<div class="pull-left margin-left-10">
												<?php echo $this->BForm->input('codigo_cargo', array('id' => 'edit_codigo_cargo_'.$funcionario_setor_cargo['codigo_cliente_alocacao'], 'options' => $funcionario_setor_cargo['Cargos'], 'empty' => 'Selecione um Cargo', 'default' => !empty($funcionario_setor_cargo['Cargo']['codigo'])? $funcionario_setor_cargo['Cargo']['codigo'] : null, 'label' => '* Cargo', 'class' => 'input-xlarge js-cargo'));?>

											</div>
										
										<?php else: ?>
											<?php echo $this->BForm->hidden('codigo_cliente_alocacao', array('id' => false, 'default' => !empty($funcionario_setor_cargo['codigo_cliente_alocacao'])? $funcionario_setor_cargo['codigo_cliente'] : null)); 
												echo $this->BForm->hidden('codigo_setor', array('id' => false, 'default' => !empty($funcionario_setor_cargo['Setor']['codigo'])? $funcionario_setor_cargo['Setor']['codigo'] : null));
												echo $this->BForm->hidden('codigo_cargo', array('id' => false, 'default' => !empty($funcionario_setor_cargo['Cargo']['codigo'])? $funcionario_setor_cargo['Cargo']['codigo'] : null));
											?>
										
											<div class="pull-left">
												<?php echo $this->BForm->input('codigo_cliente_alocacao_input', array('id' => false, 'default' => !empty($funcionario_setor_cargo['Cliente']['nome_fantasia'])? $funcionario_setor_cargo['Cliente']['nome_fantasia'] : null, 'readonly' => 'readonly', 'label' => '* Unidade:', 'class' => 'input-xlarge js-unidade')); ?>
											</div>
											<div class="pull-left margin-left-8">
												<?php echo $this->BForm->input('codigo_setor_input', array('id' => false, 'default' => !empty($funcionario_setor_cargo['Setor']['descricao'])? $funcionario_setor_cargo['Setor']['descricao'] : null, 'label' => '* Setor:', 'readonly' => 'readonly','class' => 'input-large js-setor')); ?>
											</div>
											<div class="pull-left margin-left-8">
												<?php echo $this->BForm->input('codigo_cargo_input', array('id' => false, 'default' => !empty($funcionario_setor_cargo['Cargo']['descricao'])? $funcionario_setor_cargo['Cargo']['descricao'] : null, 'label' => '* Cargo', 'readonly' => 'readonly', 'class' => 'input-large js-cargo'));?>

											</div>
										
										<?php endif; ?>

									</div>

									<div id="tomador_referencia_loading-<?php echo $key1?>-<?php echo $key?>" class="tomador_servico_load-<?php echo $key1?>-<?php echo $key?> hide"></div>

									<div id="tomador_referencia-<?php echo $key1?>-<?php echo $key?>" class="tomador_servico-<?php echo $key1?>-<?php echo $key?> hide">
										<div class="row-fluid js-line">
											<div class="pull-left">								
												<strong>*Vimos que selecionou uma unidade tomadora. Favor selecionar a Unidade do Funcionário.</strong>
												<?php echo $this->BForm->input('codigo_cliente_referencia', array('options' => $unidades_fiscais, 'empty' => 'Selecione uma Unidade', 'label' => '* Unidade do Funcionário:', 'class' => 'input-xlarge js-unidade-referencia')); ?>
											</div>
										</div>
									</div>
									


								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
								<button type="button" class="btn btn-primary salvar">Salvar</button>
							</div>
						</div>
						<?php } //fim foreach ?>
					<?php } //fim if isset  ?>
				</tbody>
			</table>
		</div>


		<!-- Modal editar matricula-->
		<div id="editarMatricula-<?php echo $key1?>" class="modal modal-large hide fade editar-matricula" tabindex="-1" role="dialog" aria-labelledby="editarMatricula-<?php echo $key1?>Label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Editar matricula</h3>
			</div>
			<div class="modal-body">
				<div class="js-form">
					<div class="row-fluid">
						<div class="pull-left">
							<?php echo $this->Form->hidden('codigo', array('id' => false, 'value' => $funcionario_matricula['ClienteFuncionario']['codigo'])); ?>
							<?php echo $this->Form->hidden('key1', array('id' => 'key1_'.$key1, 'value' => $key1)); ?>
							<?php echo $this->Form->hidden('codigo_cliente_matricula', array('id' => false, 'value' => $funcionario_matricula['ClienteFuncionario']['codigo_cliente_matricula'])); ?>
							<?php echo $this->Form->hidden('codigo_funcionario', array('id' => false, 'value' => $funcionario_matricula['ClienteFuncionario']['codigo_funcionario'])); ?>
							<?php echo $this->BForm->input('matricula', array('id' => 'matricula_'.$key1, 'data-codigo' => $key1, 'label' => 'Matrícula:', 'value' => $funcionario_matricula['ClienteFuncionario']['matricula'], 'class' => 'matricula')); ?>
						</div>
						<?php if($funcionario_matricula['ClienteFuncionario']['pre_admissional'] == 1): ?>
							<div class="pull-left pre_admissional_<?php echo $key1; ?>" style="margin-top: 28px;margin-left: 13px;">
								<?php echo $this->Form->input('pre_admissional', array('checked' => ($funcionario_matricula['ClienteFuncionario']['pre_admissional'] == 1) ? true : false, 'value' => $funcionario_matricula['ClienteFuncionario']['pre_admissional'], 'id' => 'pre_admissional_'.$key1, 'type' => 'checkbox', 'class' => 'input-xlarge pre_admissional', 'label' => 'Pré Admissional <abbr title="O campo matrícula deverá ser atualizado após a efetivação do colaborador para que o arquivo XML seja preenchido corretamente"><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>')) ?>						
							</div>
						<?php endif; ?>
						<div class="pull-left margin-left-10 categCol_<?php echo $key1; ?>">
							<?php echo $this->BForm->input('codigo_esocial_01', array('id' => 'categCol_'.$key1, 'value' => $funcionario_matricula['ClienteFuncionario']['codigo_esocial_01'], 'label' => 'Categoria do Colaborar (Tabela 01 - eSocial):', 'options' => $categoria_colaborador,'empty' => 'Selecione', 'class' => 'input-xlarge bselect2 pull-left categoria_colaborador')) ?>  
						</div>
					</div>
					<div class="row-fluid">
						<div class="pull-left margin-left-10">
							<?php echo $this->BForm->input('admissao', array('id' => false, 'type' => 'text', 'label' => 'Data de admissão:', 'class' => 'data input-small', 'value' => $funcionario_matricula['ClienteFuncionario']['admissao'])); ?>
						</div>
						<div class="pull-left margin-left-10">
							<?php echo $this->BForm->input('data_demissao', array('id' => false, 'type' => 'text', 'label' => 'Data de demissão:', 'class' => 'data input-small data-demissao', 'value' => $funcionario_matricula['ClienteFuncionario']['data_demissao'])); ?>
						</div>
                        <div class="pull-left margin-left-10">
                            <?php echo $this->BForm->input('centro_custo', array('id' => false, 'type' => 'text', 'label' => 'Centro de Custo:', 'class' => 'input-small', 'value' => $funcionario_matricula['ClienteFuncionario']['centro_custo'], 'maxlength' => '60')); ?>
                        </div>
						<div class="pull-left margin-left-10">
							<?php echo $this->BForm->input('ativo', array('id' => false, 'label' => 'Status:', 'options' => array(1 => 'Ativo', 2 => 'Férias', 3 => 'Afastado', 0 => 'Inativo'), 'class' => 'input-small status', 'value' => $funcionario_matricula['ClienteFuncionario']['ativo'])); ?>
						</div>
					</div>
					
					<?php if($funcionario_matricula['ClienteFuncionario']['sem_matricula'] == 1): ?>
						<div class="row-fluid">
							<div class="pull-left sem_matricula_<?php echo $key1; ?>">
								<?php echo $this->Form->input('sem_matricula', array('checked' => ($funcionario_matricula['ClienteFuncionario']['sem_matricula'] == 1) ? true : false, 'value' => $funcionario_matricula['ClienteFuncionario']['sem_matricula'], 'id' => 'sem_matricula_'.$key1, 'type' => 'checkbox', 'class' => 'input-xlarge sem_matricula', 'label' => 'Não possui matrícula <abbr title="Preencher com código da categoria do trabalhador. Informar somente no caso de TSVE sem informação de matrícula no evento S-2300"><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>')) ?>						
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
				<button type="button" class="btn btn-primary salvar">Salvar</button>
			</div>
		</div>

		<!-- Modal inserir setor / cargo-->
		<div id="inserirSetorCargo-<?php echo $key1?>" class="modal modal-large hide fade inserir-setor-cargo" tabindex="-1" role="dialog" aria-labelledby="inserirSetorCargo-<?php echo $key1?>Label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Inserir setor e cargo</h3>
			</div>
			<div class="modal-body">
				<div class="js-form">
					<div class="row-fluid">
						<div class="pull-left ">
							<?php echo $this->BForm->input('data_inicio', array('id' => false, 'type' => 'text', 'label' => '* Data de início:', 'class' => 'data input-small')); ?>							
						</div>
					</div>

					<div class="row-fluid js-line">
						<div class="pull-left">
							<?php echo $this->BForm->hidden('codigo_cliente_funcionario', array('value' => $funcionario_matricula['ClienteFuncionario']['codigo'])); ?>
							<?php echo $this->BForm->input('codigo_cliente_alocacao', array('options' => $unidades, 'empty' => 'Selecione uma Unidade', 'label' => '* Unidade:', 'class' => 'input-xlarge js-unidade', 'data-codigo' => $key1)); ?>
						</div>
						<div class="pull-left margin-left-10">
										<?php echo $this->BForm->input('codigo_setor', array('options' => array(), 'empty' => 'Selecione um Setor', 'label' => '* Setor:', 'class' => 'input-xlarge js-setor'/*, 'readonly' => true*/)); ?>
						</div>
						<div class="pull-left margin-left-10">
									<?php echo $this->BForm->input('codigo_cargo', array('options' => array(), 'empty' => 'Selecione um Cargo', 'label' => '* Cargo', 'class' => 'input-xlarge js-cargo'/*, 'readonly' => true*/));?>
						</div>

					</div>
					
					<div id="tomador_referencia_loading-<?php echo $key1?>" class="tomador_servico_load-<?php echo $key1?> hide"></div>

					<div id="tomador_referencia-<?php echo $key1?>" class="tomador_servico-<?php echo $key1?> hide">
						<div class="row-fluid js-line">
							<div class="pull-left">								
								<strong>*Vimos que selecionou uma unidade tomadora. Favor selecionar a Unidade do Funcionário.</strong>
								<?php echo $this->BForm->input('codigo_cliente_referencia', array('options' => $unidades_fiscais, 'empty' => 'Selecione uma Unidade', 'label' => '* Unidade do Funcionário:', 'class' => 'input-xlarge js-unidade-referencia')); ?>
							</div>
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn cancelar" data-dismiss="modal" aria-hidden="true">Cancelar</button>
				<button type="button" class="btn btn-primary salvar">Salvar</button>
			</div>
		</div>



	<?php endforeach; ?> 
</table>  
<?php else:?>
	<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<script type="text/javascript">
	function atualizaLista(codigo_cliente, codigo_funcionario){
		var div = $('#lista');
		bloquearDiv(div);
		div.load(baseUrl + 'clientes_funcionarios/listagem_matriculas/' + codigo_cliente + '/' + codigo_funcionario);
	}

	function alerta_hierarquia_pendente(dados,msg_retorno){

		var retorno = 0;
      $.ajax({
            type: 'POST',
            url: baseUrl + 'clientes_funcionarios/existe_alerta_hierarquia_pendente',
            type: 'POST',
			dataType: 'json',
			data: {dados: dados},
			})
            .done(function(response) {
                if(response == 1){
      				retorno = response;
            		$('#modal_hierarquia_pendente').modal('show');
      				return retorno;
      			} else {
					swal({
						type: 'success',
						title: 'Sucesso',
						text: msg_retorno
					});
					
      				return 0;
      			}
            })
			.fail(function() {
				swal({
					type: 'success',
					title: 'Sucesso',
					text: msg_retorno
				});
				return 0;
			});
     
	}

	function valida_se_existe_data(data, texto) {
		if(texto == undefined) {
			texto = 'O campo Data de início é obrigatório';
		}
		if(data == undefined || data == '') {
			swal({
				type: 'warning',
				title: 'Atenção',
				text: texto,
			});
			return false;
		} else {
			return true;
		}
	}

	function data_fim_menor_inicio_aquisitivo(data_fim, data_inicio) {	
		if(data_fim != ''){
			data_fim = data_fim.split('/');
			data_inicio = data_inicio.split('/');

			if(parseInt(data_fim[2] + data_fim[1] + data_fim[0]) <= parseInt(data_inicio[2] + data_inicio[1] + data_inicio[0])) {

				jQuery("input[name='data[data_fim_p_aquisitivo]']").css('box-shadow','0 0 5px 1px red');
				
				swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Data Fim Período Aquisitivo deve ser maior à Data Início Período Aquisitivo.',
				});
				return false;
			} else {
				return true;
			}
		}
	}

	function valida_se_data_fim_menor_que_data_inicio(data_inicio, data_fim) {
		var data_inicio = data_inicio.split('/');
		var data_fim = data_fim.split('/');
		if(parseInt(data_fim[2] + data_fim[1] + data_fim[0]) < parseInt(data_inicio[2] + data_inicio[1] + data_inicio[0])) {
			swal({
				type: 'warning',
				title: 'Atenção',
				text: 'A Data fim não pode ser menor do que a Data de início.',
			});
			return false;
		} else {
			return true;
		}
	}

	/**
	 * [valida_campos_obrigatorios Valida os campos obrigatórios]
	 * @param  {object}  param [objetos do formulario nova matrícula]
	 * @return {boolean}       
	 */
	function valida_campos_obrigatorios(param){

		var unidade = param.find('.js-unidade').val();
		var setor 	= param.find('.js-setor').val();
		var cargo 	= param.find('.js-cargo').val();
		var matricula = param.find('#ClienteFuncionarioMatricula').val();
		var type 	= 'warning';
		var title 	= 'Atenção';
		var text 	= '';
		var error 	= false;

		if(matricula == ''){
			text = 'O campo Matrícula é obrigatório'
			error = true;
		} else if(unidade == ''){
			text = 'O campo Unidade é obrigatório'
			error = true;
		} else if(setor == ''){
			text = 'O campo Setor é obrigatório'
			error = true;
		} else if(cargo == ''){
			text = 'O campo Cargo é obrigatório'
			error = true;
		}

		if(error){
			swal({
				type: type,
				title: title,
				text: text,
			});

			return false;
		}else{
			return true;
		}
	}//FINAL FUNCTION valida_campos_obrigatorios

	$(document).ready(function() {

		$('.js-unidade').change(function() {			
			este = $(this);

			if(!verifica_tomador('insert',this.value,$(this).attr('data-codigo'), $("#codigo_cliente_funcionario").val()) ){			
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
			}
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

		carregaUnidade = function(codigo_cliente_alocacao,codigo_setor)
		{
			$("#edit_codigo_setor_"+codigo_cliente_alocacao).html('<option value="">Carregando...</option>');
			$("#edit_codigo_cargo_"+codigo_cliente_alocacao).html('<option value="">Carregando...</option>');
			$.ajax({
				url: baseUrl + 'setores/obtem_setores_por_ajax',
				type: 'POST',
				dataType: 'html',
				data: { codigo_cliente: codigo_cliente_alocacao }
			})
			.done(function(response) {
				if(response) {
					$("#edit_codigo_setor_"+codigo_cliente_alocacao).html(response);

					$('#edit_codigo_setor_'+codigo_cliente_alocacao +' option[value='+codigo_setor+']').attr('selected','selected');

					return 'feito';
				}
			})
		}

		carregaSetor = function(codigo_cliente_alocacao,codigo_setor,codigo_cargo)
		{
			$("#edit_codigo_cargo_"+codigo_cliente_alocacao).html('<option value="">Carregando...</option>');
			$.ajax({
				url: baseUrl + 'cargos/obtem_cargos_por_ajax',
				type: 'POST',
				dataType: 'html',
				data: { codigo_cliente: codigo_cliente_alocacao, codigo_setor: codigo_setor }
			})
			.done(function(response) {
				if(response) {
					$("#edit_codigo_cargo_"+codigo_cliente_alocacao).html(response);

					$('#edit_codigo_cargo_'+codigo_cliente_alocacao +' option[value='+codigo_cargo+']').attr('selected','selected');
				}
			})
		}

		carregaSetorInsert = function(codigo_cliente_alocacao,codigo_setor,codigo_cargo)
		{

			$("#codigo_setor").html('<option value="">Carregando...</option>');
			$.ajax({
				url: baseUrl + 'setores/obtem_setores_por_ajax', 
				type: 'POST',
				dataType: 'html',
				data: { codigo_cliente: codigo_cliente_alocacao }
			})
			.done(function(response) { 
				if(response) {
					$("#codigo_setor").html(response);
					$('#codigo_setor option[value='+codigo_setor+']').attr('selected','selected');
				}
			});

			$("#codigo_cargo").html('<option value="">Carregando...</option>');
			$.ajax({
				url: baseUrl + 'cargos/obtem_cargos_por_ajax',
				type: 'POST',
				dataType: 'html',
				data: { codigo_cliente: codigo_cliente_alocacao, codigo_setor: codigo_setor }
			})
			.done(function(response) {
				if(response) {
					$("#codigo_cargo").html(response);

					$('#codigo_cargo option[value='+codigo_cargo+']').attr('selected','selected');
					$('#codigo_cargo').attr('disabled','disabled');					
				}
			})
		}

		$('[data-toggle=\"tooltip\"]').tooltip();
		setup_mascaras(); 
		setup_datepicker(); 
		setup_time(); 
		$('.js-editar-setor-cargo').click(function(event) {
			
			event.preventDefault();
			$('#editarSetorCargo-' + $(this).attr('data-codigo')).modal('show');

			este = $('#editarSetorCargo-' + $(this).attr('data-codigo'));
			var codigo_alocacao = '';
			$(este.find('input,select')).each(function(index, val) {				
				if(val.name.substr(0, val.name.length - 1).substr(5) == 'codigo_cliente_alocacao') {
					codigo_cliente_alocacao = val.value;
				}
			});

			if(codigo_cliente_alocacao != '') {
				var codigo_cargo = $('#edit_codigo_cargo_'+codigo_cliente_alocacao).val();
				var codigo_setor = $('#edit_codigo_setor_'+codigo_cliente_alocacao).val();

				carregaUnidade(codigo_cliente_alocacao,codigo_setor);
				carregaSetor(codigo_cliente_alocacao,codigo_setor,codigo_cargo);

				var codigo_fsc = $(this).attr('data-codigo-fsc');
				verifica_tomador('edit',codigo_cliente_alocacao,$(this).attr('data-codigo'), $("#codigo_cliente_funcionario").val(),codigo_fsc);
			}			
		});
		$('.js-editar-matricula').click(function(event) {
			$('#editarMatricula-' + $(this).attr('data-codigo')).modal('show');
			var data_codigo = $(this).attr('data-codigo');			

			$('#matricula_'+ $(this).attr('data-codigo')).each(function(indice){				
				var id = this.id;
				var value = this.value;
					
				$('#sem_matricula_'+ $(this).attr('data-codigo')).each(function(indice){					
					var id = $(this).prop('id');						
				
					if($('#'+id).prop('checked')) {
						$('#matricula_' + data_codigo).attr('readonly', true);							
						$('#sem_matricula_' + data_codigo).val(1);														
					} else {
						$('#matricula_' + data_codigo).attr('readonly', false);
						$('#sem_matricula_' + data_codigo).val(0);														
					}
				});

				$('#pre_admissional_'+ $(this).attr('data-codigo')).each(function(indice){					
					var id = $(this).prop('id');						
				
					if($('#'+id).prop('checked')) {
						$('#pre_admissional_' + data_codigo).val(1);														
						$('#matricula_' + data_codigo).attr('readonly', true);							
					} else {
						$('#matricula_' + data_codigo).attr('readonly', false);														
						$('#pre_admissional_' + data_codigo).val(0);
					}
				});
					
				$('#sem_matricula_'+ $(this).attr('data-codigo')).change(function(){
					var id = $(this).prop('id');
					if($('#'+id).prop('checked')) {
						$('#sem_matricula_' + data_codigo).val(1);
						$('#matricula_' + data_codigo).attr('readonly', true);													
					} else {
						$('#matricula_' + data_codigo).attr('readonly', false);
						$('#sem_matricula_' + data_codigo).val(0);												
					}
				});

				$('#pre_admissional_'+ $(this).attr('data-codigo')).change(function(){
					var id = $(this).prop('id');
					if($('#'+id).prop('checked')) {
						$('#pre_admissional_' + data_codigo).val(1);	
						$('#matricula_' + data_codigo).attr('readonly', true);													
					} else {
						$('#pre_admissional_' + data_codigo).val(0);
						$('#matricula_' + data_codigo).attr('readonly', false);													
					}
				});
			});
		});
		$('.js-novo-setor-cargo').click(function(event) {
			$('#inserirSetorCargo-' + $(this).attr('data-codigo')).modal('show');
		});
		$('.editar-setor-cargo .salvar').click(function(event) {
			var dados = new Object();
			este = $(this).parents('.editar-setor-cargo');
			if(!valida_se_existe_data(este.find('.data').first().val())) {
				return false;
			}
			if(!valida_se_data_fim_menor_que_data_inicio(este.find('.data').first().val(), este.find('.data').last().val())) {
				return false;
			}
			if(!valida_campos_obrigatorios(este)){
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
				atualizaLista('<?php echo $codigo_cliente ?>', '<?php echo $codigo_funcionario ?>');
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
					var msg_sucesso = 'O setor e cargo foram atualizados com sucesso.';
					alerta_hierarquia_pendente(response,msg_sucesso);

				}
			})
			.fail(function() {
				este.modal('hide');
				$(this).html(button);
				este.find('[data-dismiss="modal"]').show();
			})
		});
		$('.inserir-setor-cargo .salvar').click(function(event) {
			var dados = new Object();
			este = $(this).parents('.inserir-setor-cargo');
			if(!valida_se_existe_data(este.find('.data').val())) {
				return false;
			}else if(!valida_campos_obrigatorios(este)){
				return false;
			}
			var button = $(this).html();
			este.find('[data-dismiss="modal"]').hide();
			$(this).css({height: $(this).outerHeight(), width: $(this).outerWidth() }).html('<img src="' + baseUrl + 'img/default.gif">');
			$(este.find('input, select')).each(function(index, val) {
				dados[val.name.substr(0, val.name.length - 1).substr(5)] = val.value;
			});
			$.ajax({
				url: baseUrl + 'clientes_funcionarios/insere_setor_cargo',
				type: 'POST',
				dataType: 'json',
				data: {dados: dados},
			})
			.done(function(response) {
				atualizaLista('<?php echo $codigo_cliente ?>', '<?php echo $codigo_funcionario ?>');
				este.modal('hide');
				$(this).html(button);
				este.find('[data-dismiss="modal"]').show();
				var msg_sucesso = 'O setor e cargo foram criados com sucesso.';
			 	alerta_hierarquia_pendente(response,msg_sucesso);
			
				
			})
			.fail(function() {
				este.modal('hide');
				$(this).html(button);
				este.find('[data-dismiss="modal"]').show();
			})
		});
		$('.editar-matricula .salvar').click(function(event) {
			var dados = new Object();
			este = $(this).parents('.editar-matricula');			
			if(!valida_se_existe_data(este.find('.data').val(), 'O campo Data de admissão é obrigatório.')) {
				return false;
			}
			// if(!valida_se_existe_data(este.find('.matricula').val(), 'O campo Matrícula é obrigatório.')) 	{
			// 	return false;
			// } trecho comentado por causa da mudança com o campo categoria do colaborador
			var button = $(this).html();
			este.find('[data-dismiss="modal"]').hide();
			$(this).css({height: $(this).outerHeight(), width: $(this).outerWidth() }).html('<img src="' + baseUrl + 'img/default.gif">');
			$(este.find('input, select')).each(function(index, val) {
				dados[val.name.substr(0, val.name.length - 1).substr(5)] = val.value;
			});
			$.ajax({
				url: baseUrl + 'clientes_funcionarios/edita_matricula',
				type: 'POST',
				dataType: 'json',
				data: {dados: dados},
			})
			.done(function(response) {
				
				$(this).html(button);
				este.find('[data-dismiss="modal"]').show();

				if(response.type == 'warning'){
					este.find('.salvar').html('Salvar');
					swal({
						type: response.type,
						title: response.title,
						text: response.text,
					});
					return false;
				}else{
					atualizaLista('<?php echo $codigo_cliente ?>', '<?php echo $codigo_funcionario ?>');
					este.modal('hide');
					
					swal({
						type: 'success',
						title: 'Sucesso',
						text: 'A matrícula foi atualizada com sucesso.'
					});
				}
			})
			.fail(function() {
				este.modal('hide');
				$(this).html(button);
				este.find('[data-dismiss="modal"]').show();
			})
		});
		$('.js-remover-setor-cargo').click(function(event) {
			event.preventDefault();
			bloquearDiv($('div#lista'));
			$.ajax({
				url: baseUrl + 'funcionarios/excluir_setor_cargo',
				type: 'POST',
				dataType: 'JSON',
				data: {codigo: $(this).attr('data-codigo')},
			})
			.done(function(resp) {
				$('div#lista').unblock();
				if(resp.error) {
					swal({
						type: 'error',
						title: 'Erro',
						text: resp.message
					});
				} else {
					swal({
						type: 'success',
						title: 'Sucesso',
						text: 'Setor/cargo excluído com sucesso.'
					});
					atualizaLista('<?php echo $codigo_cliente ?>', '<?php echo $codigo_funcionario ?>');
				}
			})
			.fail(function() {
				$('div#lista').unblock();
				swal({
					type: 'error',
					title: 'Erro',
					text: 'Ouve uma falha no processo, por favor tente novamente.'
				});
			})
		});
		$('.js-excluir-matricula').click(function(event) {
			event.preventDefault();
			bloquearDiv($('div#lista'));
			$.ajax({
				url: baseUrl + 'clientes_funcionarios/excluir_matricula',
				type: 'POST',
				dataType: 'JSON',
				data: {codigo: $(this).attr('data-codigo')},
			})
			.done(function(resp) {
				$('div#lista').unblock();
				if(resp.error) {
					swal({
						type: 'error',
						title: 'Erro',
						text: resp.message
					});
				} else {
					swal({
						type: 'success',
						title: 'Sucesso',
						text: 'Matrícula excluída com sucesso.'
					});
					atualizaLista('<?php echo $codigo_cliente ?>', '<?php echo $codigo_funcionario ?>');
				}
			})
			.fail(function() {
				$('div#lista').unblock();
				swal({
					type: 'error',
					title: 'Erro',
					text: 'Ouve uma falha no processo, por favor tente novamente.'
				});
			})
		});

		//verfica se é uma unidade tomadora
		verifica_tomador = function(tipo,codigo_cliente, chave, codigo_cliente_funcionario,codigo_funcionario_setor_cargo){

			//apresenta o loding
			$(".tomador_servico-"+ chave).addClass('hide');
			$(".tomador_servico_load-"+ chave).removeClass('hide');
			$(".tomador_servico_load-"+ chave).html('<img src="' + baseUrl + 'img/default.gif">');

			$('.js-setor').removeAttr('disabled');
			$('.js-cargo').removeAttr('disabled');
			$('.js-unidade-tomador').removeAttr('disabled');
			
			$.ajax({
				url: baseUrl + 'clientes_funcionarios/get_tomador',
				type: 'POST',
				dataType: 'JSON',
				data: { codigo_cliente: codigo_cliente, codigo_cliente_funcionario: codigo_cliente_funcionario, codigo_fsc: codigo_funcionario_setor_cargo }
			})
			.done(function(resp) {

				if(resp.retorno) {					
					//apresenta as unidades de referencia					
					$(".tomador_servico_load-"+ chave).addClass('hide');
					$(".tomador_servico-"+ chave).removeClass('hide');
					
					if(resp.codigo_cliente_referencia) {
						//deixa selecionado o valor
						$(".tomador_servico-"+ chave +' option[value='+resp.codigo_cliente_referencia+']').attr('selected','selected');

						if(tipo == 'insert') {
							var codigo_cliente_alocacao = resp.codigo_cliente_referencia;
							var codigo_cargo = resp.codigo_cargo;
							var codigo_setor = resp.codigo_setor;
							//seleciona o codigo setor
							$("#codigo_setor option[value="+codigo_setor+"]").attr('selected','selected');
							$('#codigo_setor').attr('disabled','disabled');
							$('#codigo_setor').attr('readonly','readonly');

							carregaSetorInsert(codigo_cliente_alocacao,codigo_setor,codigo_cargo);							
						}
						else if(tipo == 'edit') {
							$('.js-unidade-tomador').attr('disabled','disabled');
							$('#edit_codigo_setor_'+codigo_cliente).attr('disabled','disabled');
							$('#edit_codigo_cargo_'+codigo_cliente).attr('disabled','disabled');
						}
					}

				}
				else { 
					$(".tomador_servico-"+ chave).addClass('hide');
					$(".tomador_servico_load-"+ chave).addClass('hide');
				}
			})
			.fail(function() {
				
				$(".tomador_servico-"+ chave).addClass('hide');
				$(".tomador_servico_load-"+ chave).addClass('hide');

				swal({
					type: 'error',
					title: 'Erro',
					text: 'Houve uma falha no processo, por favor tente novamente.'
				});
			});

		}//fim change codigo_cliente_alocacao

	});
</script>
