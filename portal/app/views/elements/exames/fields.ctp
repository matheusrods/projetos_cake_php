<div class='row-fluid'>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['Exame']['codigo']) ? $this->data['Exame']['codigo'] : '')); ?>
		<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
		<?php echo $this->BForm->input('codigo_rh', array('label' => 'Código RH', 'class' => 'input-mini')); ?>
		<div class='row-fluid inline'>
			<?php if (empty($this->passedArgs)) : ?>
				<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
			<?php else : ?>
				<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input-small', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
			<?php endif;  ?>
		</div>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('descricao_ingles', array('label' => 'Descrição em Inglês', 'class' => 'input-xxlarge')); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('codigo_servico', array('label' => 'Serviço (*)', 'class' => 'input-xxlarge', 'options' => $servico, 'readonly' => true)); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('periodo_meses', array('label' => 'Periodicidade Padrão(Meses)', 'class' => 'input-mini just-number')); ?>
		<?php echo $this->BForm->input('periodo_apos_demissao', array('label' => 'Periodicidade após admissão(Meses)', 'class' => 'input-mini just-number')); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('codigo_tabela_amb', array('label' => 'Código referência AMB', 'class' => 'input-medium', 'maxlength' => 9)); ?>
		<?php echo $this->BForm->input('codigo_tuss', array('label' => 'Código referência TUSS', 'class' => 'input-medium', 'maxlength' => 9)); ?>
		<?php echo $this->BForm->input('codigo_ch', array('label' => 'Código CH', 'class' => 'input-medium', 'maxlength' => 9)); ?>
	</div>
	<div class='row-fluid'>
		<div class='control-group input checkbox'>
			<?php echo $this->BForm->input('empresa_cliente', array('label' => 'Copia para empresa cliente 	', 'type' => 'checkbox', 'div' => false)) ?>
		</div>
		<div class='control-group input checkbox'>
			<?php echo $this->BForm->input('exame_auto', array('label' => 'Criar exame no momento da criação da ficha clínica ', 'type' => 'checkbox', 'div' => false)) ?>
		</div>
		<div class='control-group input checkbox'>
			<?php echo $this->BForm->input('laboral', array('label' => 'Exame Laboratorial', 'type' => 'checkbox', 'div' => false)) ?>
		</div>
		<div class='control-group input checkbox'>
			<?php echo $this->BForm->input('exame_audiometria', array('label' => 'É um exame de Audiometria', 'type' => 'checkbox', 'div' => false)) ?>
		</div>
		<div class='control-group input checkbox'>
			<?php echo $this->BForm->input('exame_assinar_eletronicamente', array('label' => 'Assinar exame eletronicamente', 'type' => 'checkbox', 'div' => false)) ?>
		</div>
		<div class='control-group input checkbox'>
			<?php echo $this->BForm->input('exame_atraves_lyn', array('label' => 'Exame através do Lyn', 'type' => 'checkbox', 'div' => false)) ?>
		</div>
		<div class='control-group input checkbox'>
			<?php echo $this->BForm->input('anexo_nao_comparecimento', array('label' => 'Permitir upload de anexo em caso de não comparecimento', 'type' => 'checkbox', 'div' => false)) ?>
		</div>		
	</div>
	<div class='row-fluid inline row-servico'>
		<?php echo $this->BForm->input('codigo_servico_lyn', array('label' => 'Serviço Lyn(*)', 'class' => 'input-xxlarge', 'options' => $servico_lyn, 'default' => '', 'empty' => 'Selecione')); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('tela_resultado', array('label' => 'Tela de Resultado', 'class' => 'input-medium', 'default' => '', 'empty' => 'Selecione', 'options' => $tela_resultado)); ?>

		<?php echo $this->BForm->input('TiposResultadosExames.codigo', array('label' => 'Tipos de Resultado', 'class' => 'input-medium', 'default' => '', 'empty' => 'Selecione', 'options' => $resultados)); ?>

	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('referencia', array('label' => 'Referência', 'type' => 'textarea', 'class' => 'input-xxlarge')) ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('recomendacoes', array('label' => 'Recomendação de Exames', 'type' => 'textarea', 'class' => 'input-xxlarge')) ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('conduta_exame', array('label' => 'Conduta do Exame', 'type' => 'textarea', 'class' => 'input-xxlarge')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('unidade_medida', array('label' => 'Unidade de medida', 'class' => 'input-medium')); ?>
		<?php echo $this->BForm->input('sexo', array('label' => 'Sexo', 'class' => 'input', 'default' => '', 'empty' => 'Selecione', 'options' => array('F' => 'Feminino', 'M' => 'Masculino'))); ?>
	</div>
	<div class='row-fluid inline'>
		<div class='control-group input checkbox'>
			<?php echo $this->BForm->input('controla_validacoes', array('label' => 'Controla Validações', 'type' => 'checkbox', 'div' => false)) ?>
		</div>
	</div>
</div>
<div class='row-fluid'>
	<h4>Monitoração Biológica (eSocial - Tabela 7)</h4>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo_esocial', array('label' => 'Análise', 'class' => 'input-xxlarge', 'default' => '', 'empty' => 'Selecione', 'options' => $esocial)); ?>
	</div>
	<div class="row-fluid inline">

		<?php echo $this->BForm->input('material_biologico', array('label' => 'Material Biológico', 'class' => 'input', 'empty' => 'Selecione', 'options' => $material_biologico)); ?>
		<?php echo $this->BForm->input('interpretacao_exame', array('label' => 'Interpretação do Exames', 'class' => 'input', 'default' => '', 'empty' => 'Selecione', 'options' => array('EE' => 'EE', 'SC' => 'SC', 'SC+' => 'SC+'))); ?>
		<?php echo $this->BForm->input('data_incio_monitoracao', array('label' => 'Data Início da Monitoração Biológica', 'class' => 'input-medium data', 'type' => 'text')); ?>
	</div>
</div>

<div class='row-fluid'>
	<h4>(eSocial - Tabela 27) - Procedimentos</h4>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo_esocial_27', array('label' => '', 'class' => 'input-xxlarge bselect2', 'default' => '', 'empty' => 'Selecione', 'options' => $tabela27)); ?>
	</div>

	<div class='row-fluid'>
		<h4>Relatório Operacional</h4>
		<div class='row-fluid inline'>
			<div class='row-fluid'>
				<div class='control-group input checkbox'>
					<?php echo $this->BForm->input('exame_excluido_convocacao', array('label' => 'Convocação de Exames (**)', 'type' => 'checkbox', 'div' => false)) ?>
				</div>
				<div class='control-group input checkbox'>
					<?php echo $this->BForm->input('exame_excluido_ppp', array('label' => 'PPP (**)', 'type' => 'checkbox', 'div' => false)) ?>
				</div>
				<div class='control-group input checkbox'>
					<?php echo $this->BForm->input('exame_excluido_aso', array('label' => 'ASO (**)', 'type' => 'checkbox', 'div' => false)) ?>
				</div>
				<div class='control-group input checkbox'>
					<?php echo $this->BForm->input('exame_excluido_pcmso', array('label' => 'PCMSO (**)', 'type' => 'checkbox', 'div' => false)) ?>
				</div>
				<div class='control-group input checkbox'>
					<?php echo $this->BForm->input('exame_excluido_anual', array('label' => 'Relatório Anual (**)', 'type' => 'checkbox', 'div' => false)) ?>
				</div>
				<div class='control-group input checkbox'>
					<?php echo $this->BForm->input('exame_excluido_rac', array('label' => 'RAC (**)', 'type' => 'checkbox', 'div' => false)) ?>
				</div>
			</div>
			<h6>(**) A alteração deste parametro não refletirá nos exames já incluidos.</h6>
		</div>

		<div class='row-fluid inline'>
			<div class='control-group input checkbox'>
				<?php echo $this->BForm->input('exame_admissional', array('label' => 'Admissional', 'type' => 'checkbox', 'div' => false)) ?>
			</div>
			<div class='control-group input checkbox'>
				<?php echo $this->BForm->input('exame_periodico', array('label' => 'Periódico', 'type' => 'checkbox', 'div' => false)) ?>
			</div>
			<div class='control-group input checkbox'>
				<?php echo $this->BForm->input('exame_demissional', array('label' => 'Demissional', 'type' => 'checkbox', 'div' => false)) ?>
			</div>
			<div class='control-group input checkbox'>
				<?php echo $this->BForm->input('exame_retorno', array('label' => 'Retorno de Trabalho', 'type' => 'checkbox', 'div' => false)) ?>
			</div>
			<div class='control-group input checkbox'>
				<?php echo $this->BForm->input('exame_mudanca', array('label' => 'Mudança de Riscos Ocupacionais', 'type' => 'checkbox', 'div' => false)) ?>
			</div>
			<div class='control-group input checkbox'>
				<?php echo $this->BForm->input('exame_monitoracao', array('label' => 'Monitoração Pontual', 'type' => 'checkbox', 'div' => false)) ?>
			</div>
			<div class='control-group input checkbox'>
				<?php echo $this->BForm->input('qualidade_vida', array('label' => 'Qualidade de Vida', 'type' => 'checkbox', 'div' => false)) ?>
			</div>
		</div>
	</div>

	<div class='row-fluid'>
		<h4>Periodicidade por Idade</h4>
		<h6>As opções abaixo serão aplicadas apenas conforme a idade.</h6>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('periodo_idade', array('label' => false, 'before' => 'A partir de ', 'type' => 'text',  'class' => 'input-mini just-number')) ?>
			<?php echo $this->BForm->input('qtd_periodo_idade', array('label' => false, 'before' => 'anos de idade solicitar este exame a cada ', 'after' => ' meses.', 'type' => 'text',  'class' => 'input-mini just-number')) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('periodo_idade_2', array('label' => false, 'before' => 'A partir de ', 'type' => 'text',  'class' => 'input-mini just-number')) ?>
			<?php echo $this->BForm->input('qtd_periodo_idade_2', array('label' => false, 'before' => 'anos de idade solicitar este exame a cada ', 'after' => ' meses.', 'type' => 'text',  'class' => 'input-mini just-number')) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('periodo_idade_3', array('label' => false, 'before' => 'A partir de ', 'type' => 'text',  'class' => 'input-mini just-number')) ?>
			<?php echo $this->BForm->input('qtd_periodo_idade_3', array('label' => false, 'before' => 'anos de idade solicitar este exame a cada ', 'after' => ' meses.', 'type' => 'text',  'class' => 'input-mini just-number')) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('periodo_idade_4', array('label' => false, 'before' => 'A partir de ', 'type' => 'text',  'class' => 'input-mini just-number')) ?>
			<?php echo $this->BForm->input('qtd_periodo_idade_4', array('label' => false, 'before' => 'anos de idade solicitar este exame a cada ', 'after' => ' meses.', 'type' => 'text',  'class' => 'input-mini just-number')) ?>
		</div>
	</div>
	<div class='form-actions'>
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary'));
		// echo $html->link('Salvar', '#',
		// array('data-href' => Router::url(
		//           	array(
		//               	'controller' => 'exames', 
		//               	'action' => 'editar'
		//               )
		//          ), 
		//           	'class' => 'btn btn-primary pull-left submit-load', 
		//           	'escape' => false, 
		//           	//'data-toggle' => 'tooltip'
		//          )
		//      ); 
		?>
		<?= $html->link('Voltar', array('controller' => 'exames', 'action' => 'index'), array('class' => 'btn')); ?>
	</div>

	<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras(); 
		setup_datepicker();	

		$("#ExameCodigoEsocial").change(function() {

		  	var codigo_esocial = $("#ExameCodigoEsocial").val();
			var descricao = "";

			$.ajax({
	            type: "POST",
	            dataType: "json",
	            url: baseUrl + "exames/busca_esocial/" + codigo_esocial + "/" + Math.random(),
	            success: function(data){
					
					$("#ExameMaterialBiologico option").remove();

					$.each(data.EsocialPai, function(campo, value) {
						
						if(campo == "coluna_adicional"){

							if(value == 1){
								descricao = "Urina";
							}
							else if(value == 2){
								descricao = "Sangue";
							}
							else{
								descricao = "Sangue e Urina";	
							}


							$("#ExameMaterialBiologico").append($("<option>").text(descricao).attr("value", value));
						}
					});
	        	}
	        });
		});
	});
'); ?>


	<script type="text/javascript">
		jQuery(document).ready(function() {

			//funcao para validar a descricao do servico
			$('#ExameDescricao').change(function() {
				get_servico_existente();
			}); //fim funcao validar servico

			get_servico_existente = function(descricaoExame) {

				//trata os valores preenchidos pelo usuario
				var descricaoExame = ($('#ExameDescricao').val()).replace('/', '|');
				var codigo_servico = $('#ExameCodigoServico').val();
				var codigo_exame = $('#ExameCodigo').val();

				$.ajax({
					url: baseUrl + "exames/buscar_servico_existente/" + codigo_servico + "/" + codigo_exame + "/" + descricaoExame,
					dataType: "json",
					beforeSend: function() {
						bloquearDiv($(".form-procurar"));
					},
					success: function(data) {

						if (data.return == 1) {
							swal({
								type: 'warning',
								title: 'Atenção',
								text: 'Já existe um exame para este serviço! Ao terminar a edição, este serviço terá a mesma descricao do exame!'
							});
						}
					},
					complete: function(data) {
						($(".form-procurar")).unblock();
					}
				});
			}

			if ($('#ExameExameAtravesLyn').is(':checked')) {
				$('.row-servico').show();
			} else {
				$('.row-servico').hide();
				$('#ExameCodigoServicoLyn').val('');
			}

			$('#ExameExameAtravesLyn').change(function(event) {
				if ($('#ExameExameAtravesLyn').is(':checked')) {
					$('.row-servico').show();
				} else {
					$('.row-servico').hide();
					$('#ExameCodigoServicoLyn').val('');
				}
			});

		});
	</script>