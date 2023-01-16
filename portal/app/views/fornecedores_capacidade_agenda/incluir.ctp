<style>
	.control-group { float: left; margin-right: 15px; margin-bottom: 5px; }
	select { width: auto; }
	input[type="radio"], input[type="checkbox"] { margin: 0; }
	hr { margin: 0 0 20px 0; }
</style>

<div class="inline well">
	<?php echo $this->BForm->input('Fornecedor.razao_social', array('value' => $dados_fornecedor['Fornecedor']['razao_social'], 'class' => 'input-xxlarge', 'label' => 'Razão Social' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Fornecedor.codigo_documento', array('value' => $dados_fornecedor['Fornecedor']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ', 'readonly' => true, 'type' => 'text')); ?>
	<div style="clear: both;"></div>
</div>
	
<?php if(count($horarios_atendimento)) : ?>
	<?php echo $this->BForm->create('FornecedorCapacidadeAgenda', array('type' => 'post', 'onsubmit' => 'return false;'));?>
		<span class="row">
			<?php echo $this->BForm->input('ListaDePrecoProdutoServico.codigo', array('value' => null, 'class' => 'form-control', 'style' => 'width: 530px;', 'label' => 'Agenda do Exame:', 'div' => false, 'options' => array_map('utf8_encode', $lista_servicos))); ?>
			<?php echo $this->BForm->input('Fornecedor.codigo', array('type' => 'hidden', 'value' => $codigo_fornecedor)); ?>
		</span>
		<div class="well">
			<div id="periodos" class="form-group clear">
			
				<div id="cabecalho">
					<span class="span2">
						<label><b>Hora Início:</b></label>
					</span>
					<span class="span2">
						<label><b>Hora Fim:</b></label>
					</span>
					<span class="span1">
						<label><b>Tempo:</b></label>
					</span>
					<span class="span1">
						<label><b>Médicos:</b></label>
					</span>
					<span class="span4">
						<label><b>Dias da Semana:</b></label>
					</span>
					<span class="span1">
	    				<label><b>Ação:</b></label>
	    			</span>
	    			
	    			<div style="clear: both;"></div>
	    		</div>
	    		<hr />
				<?php if(count($horarios_atendimento)) : ?>
					<?php foreach($horarios_atendimento as $k => $horario) : ?>
						
						<div id="pediodo_<?php echo $k; ?>" class="periodo">
							<span class="span2">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.hora_inicio', array('value' => $horario['FornecedorHorario']['hora_inicio'][0], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['horas'])); ?>
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.minuto_inicio', array('value' => $horario['FornecedorHorario']['hora_inicio'][1], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['minutos'])); ?>	
							</span>
							<span class="span2">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.hora_fim', array('value' => $horario['FornecedorHorario']['hora_fim'][0], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['horas'])); ?>
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.minuto_fim', array('value' => $horario['FornecedorHorario']['hora_fim'][1], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['minutos'])); ?>
							</span>	
							<span class="span1">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.tempo_atendimento', array('value' => '', 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['tempo_consulta'])); ?>
							</span>
							<span class="span1">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.quantidade_medico', array('value' => '1', 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false)); ?>
							</span>	
							<span class="span4" style="padding-top: 5px;">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.seg', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorHorario']['dias_semana']['seg']) && ($horario['FornecedorHorario']['dias_semana']['seg'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> seg.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.ter', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorHorario']['dias_semana']['ter']) && ($horario['FornecedorHorario']['dias_semana']['ter'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> ter.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.qua', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorHorario']['dias_semana']['qua']) && ($horario['FornecedorHorario']['dias_semana']['qua'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> qua.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.qui', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorHorario']['dias_semana']['qui']) && ($horario['FornecedorHorario']['dias_semana']['qui'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> qui.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.sex', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorHorario']['dias_semana']['sex']) && ($horario['FornecedorHorario']['dias_semana']['sex'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> sex.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.sab', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorHorario']['dias_semana']['sab']) && ($horario['FornecedorHorario']['dias_semana']['sab'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> sab.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.dom', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorHorario']['dias_semana']['dom']) && ($horario['FornecedorHorario']['dias_semana']['dom'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> dom.
							</span>
							<span class="span1">
				    			<?php if($k > 0) : ?>
							        <a href="javascript:void(0);" onclick="$(this).parents('.periodo').remove();" class="label label-alert right" title="Remover Periodo">x</a>
				    			<?php endif; ?>
							</span>
							<div style="clear: both;"></div>
							<hr />
						</div>
					<?php endforeach; ?>						
					
				<?php else : ?>
				
					<div id="pediodo_0" class="periodo">
						<span class="span2">
							<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.hora_inicio', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['horas'])); ?>
							<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.minuto_inicio', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['minutos'])); ?>	
						</span>
						<span class="span2">
							<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.hora_fim', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['horas'])); ?>
							<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.minuto_fim', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['minutos'])); ?>
						</span>	
						<span class="span2">
							<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.tempo_atendimento', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['tempo_consulta'])); ?>
						</span>
						<span class="span2">
							<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.quantidade_medico', array('value' => '1', 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false)); ?>
						</span>	
						<span class="span2">
							<div class="dias">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.dias_semana.seg', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> seg.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.dias_semana.ter', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> ter.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.dias_semana.qua', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> qua.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.dias_semana.qui', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> qui.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.dias_semana.sex', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> sex.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.dias_semana.sab', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> sab.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.0.dias_semana.dom', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> dom.
							</div>
						</span>
						<span class="span1">
						
						</span>
						<div style="clear: both;"></div>
					</div>
				<?php endif; ?>
			</div>
			<span class="span2" style="float: right;">
				<a href="javascript:void(0);" onclick="FornecedoresCapacidadeAgenda.addPeriodo();" class="label label-info right" title="Incluir Período">Incluir Período</a>
			</span>
			
			<div style="clear: both;"></div>
		</div>

		<span class="span12" id="carregando" style="display: none;">
			<img src="/portal/img/default.gif"> Carregando...
		</span>		
		<span class="span12" id="botao">
			<?php echo $this->BForm->submit('Montar Grade de Horários', array('div' => false, 'class' => 'btn btn-primary')); ?>
		</span>		
	<?php echo $this->BForm->end(); ?>	

<?php else : ?>
	<div class="alert">Este fornecedor não tem horario de atendimento cadastrado</div>
<?php endif; ?>

<br /><br />
<div class="formulario" id="agenda">

</div>

<div class="form-actions center" id="rodape_botoes">
	<a href="/portal/fornecedores_capacidade_agenda/agenda_por_exame/<?php echo $codigo_fornecedor; ?>" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>
</div>

<div id="modelo_periodo" style="display:none;">
	<div class="form-group clear periodos">
		<div id="periodo_X" class="periodo">
			<span class="span2">
				<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.hora_inicio', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['horas'])); ?>
				<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.minuto_inicio', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['minutos'])); ?>	
			</span>
			<span class="span2">
				<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.hora_fim', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['horas'])); ?>
				<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.minuto_fim', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['minutos'])); ?>
			</span>	
			<span class="span1">
				<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.tempo_atendimento', array('class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['tempo_consulta'])); ?>
			</span>
			<span class="span1">
				<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.quantidade_medico', array('value' => '1', 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false)); ?>
			</span>
			<span class="span4">
				<div class="dias">
					<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.dias_semana.seg', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> seg.
					<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.dias_semana.ter', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> ter.
					<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.dias_semana.qua', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> qua.
					<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.dias_semana.qui', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> qui.
					<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.dias_semana.sex', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> sex.
					<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.dias_semana.sab', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> sab.
					<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.X.dias_semana.dom', array('type'=>'checkbox', 'checked' => '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> dom.
				</div>
			</span>
			<span class="span1">
				<a href="javascript:void(0);" onclick="$(this).parents('.periodo').remove();" class="label label-alert right" title="Remover Periodo">x</a>
			</span>
			<div style="clear: both;"></div>
			<hr />
		</div>
	</div>
</div>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		$("#FornecedorCapacidadeAgendaIncluirForm").submit(function(event) {
			FornecedoresCapacidadeAgenda.enviaFormAjax(this);
		    event.preventDefault();
		});
		
		setup_mascaras();
	});
'); ?>   

<?php echo $this->Buonny->link_js('fornecedores_capacidade_agenda'); ?>