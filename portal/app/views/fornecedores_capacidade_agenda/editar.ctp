<div class='inline well'>
	<?php echo $this->BForm->input('Fornecedor.razao_social', array('value' => $dados_fornecedor['Fornecedor']['razao_social'], 'class' => 'input-xxlarge', 'label' => 'Razão Social' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Fornecedor.codigo_documento', array('value' => $dados_fornecedor['Fornecedor']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ', 'readonly' => true, 'type' => 'text')); ?>
	<div style="clear: both;"></div>
</div>
	
<div class="well">
	<?php echo $this->BForm->create('FornecedorCapacidadeAgenda', array('type' => 'post', 'onsubmit' => 'return false;'));?>
		<span class="span12" style="width: 96%">
			<?php echo $this->BForm->input('ListaDePrecoProdutoServico.codigo', array('value' => null, 'class' => 'form-control', 'style' => 'width: 530px;', 'label' => 'Agenda do Exame:', 'div' => false, 'options' => $lista_servicos)); ?>
			<?php echo $this->BForm->input('Fornecedor.codigo', array('type' => 'hidden', 'value' => $codigo_fornecedor)); ?>
			<hr />
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
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.hora_inicio', array('value' => $horario['FornecedorCapacidadeAgenda']['hora_inicio'][0], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['horas'])); ?>
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.minuto_inicio', array('value' => $horario['FornecedorCapacidadeAgenda']['hora_inicio'][1], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['minutos'])); ?>	
							</span>
							<span class="span2">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.hora_fim', array('value' => $horario['FornecedorCapacidadeAgenda']['hora_fim'][0], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['horas'])); ?>
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.minuto_fim', array('value' => $horario['FornecedorCapacidadeAgenda']['hora_fim'][1], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['minutos'])); ?>
							</span>	
							<span class="span1">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.tempo_atendimento', array('value' => $horario['FornecedorCapacidadeAgenda']['tempo_consulta'], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false, 'options' => $lista_horario['tempo_consulta'])); ?>
							</span>
							<span class="span1">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.' . $k .'.quantidade_medico', array('value' => $horario['FornecedorCapacidadeAgenda']['qtd_medico'], 'class' => 'form-control', 'style' => 'width: 60px;', 'label' => false, 'div' => false)); ?>
							</span>	
							<span class="span4" style="padding-top: 5px;">
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.seg', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorCapacidadeAgenda']['dias_semana']['seg']) && ($horario['FornecedorCapacidadeAgenda']['dias_semana']['seg'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> seg.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.ter', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorCapacidadeAgenda']['dias_semana']['ter']) && ($horario['FornecedorCapacidadeAgenda']['dias_semana']['ter'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> ter.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.qua', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorCapacidadeAgenda']['dias_semana']['qua']) && ($horario['FornecedorCapacidadeAgenda']['dias_semana']['qua'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> qua.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.qui', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorCapacidadeAgenda']['dias_semana']['qui']) && ($horario['FornecedorCapacidadeAgenda']['dias_semana']['qui'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> qui.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.sex', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorCapacidadeAgenda']['dias_semana']['sex']) && ($horario['FornecedorCapacidadeAgenda']['dias_semana']['sex'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> sex.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.sab', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorCapacidadeAgenda']['dias_semana']['sab']) && ($horario['FornecedorCapacidadeAgenda']['dias_semana']['sab'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> sab.
								<?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$k.'.dias_semana.dom', array('type'=>'checkbox', 'checked' => (isset($horario['FornecedorCapacidadeAgenda']['dias_semana']['dom']) && ($horario['FornecedorCapacidadeAgenda']['dias_semana']['dom'] == '1')) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> dom.								
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
				<a href="javascript:void(0);" onclick="FornecedoresCapacidadeAgenda.addPeriodo();" class="label label-info right" title="Incluir Periodo">Incluir Período</a>
			</span>
			<div style="clear: both;"></div>
		</div>		
		<br />
		<span class="span12" id="carregando" style="display: none;">
			<img src="/portal/img/default.gif"> Carregando...
		</span>
		<div class="row-fluid">
			
		<div class="span6" id="botao">
			<?php echo $this->BForm->submit('Montar Grade de Horários', array('div' => false, 'class' => 'btn btn-primary')); ?>
		</div>	
		<div class="span6 text-right">
			<a href="#datasBloqueadas" role="button" class="btn btn-warning" data-toggle="modal">Datas bloqueadas</a>
			<a href="#bloquearDatas" role="button" class="btn btn-danger" data-toggle="modal" onclick="FornecedoresCapacidadeAgenda.getData('<?php echo date('d/m/Y') ?>', <?php echo $codigo_fornecedor?>, <?php echo $codigo_lista_de_preco_produto_servico?>);">Bloquear datas</a>
		</div>
		</div>
	<?php echo $this->BForm->end(); ?>
</div>

<br /><br />
<div class="formulario" id="agenda">
	<style type="text/css">
	    td{
	        padding: 0px !important;    
	        vertical-align: middle !important; 
	        text-align: center !important;   
	    }
	    th{
	        text-align: center !important;   
	        padding: 0px !important;    
	    }
	    .control-group{
	        padding: 0px !important;
	        margin: 0px !important;
	    }
	    .input-hora{
	        width: 40px !important;
	        text-align: center !important;
	        border: 0px !important;
	    }
	</style>

	<h4>Grade de Capacidade de Atendimento</h4>
	<?php echo $this->BForm->create('FornecedorGradeAgenda', array('type' => 'post', 'onsubmit' => 'return false;'));?>
	
		<?php echo $this->BForm->input('ListaDePrecoProdutoServico.codigo', array('type' => 'hidden', 'value' => $codigo_lista_de_preco_produto_servico)); ?>
		<?php echo $this->BForm->input('Fornecedor.codigo', array('type' => 'hidden', 'value' => $codigo_fornecedor)); ?>
	
        <div class="row-fluid inline">
            <table class="table table-bordered" style="width: 460px;">
                <thead>
                    <tr>
                    <th></th>
                        <?php for($hora = 0; $hora<=23; $hora++): ?>
                            <th style="width: 30px !important;">
                            <?php echo str_pad($hora, 2, 0, STR_PAD_LEFT) ?>:00
                            </th>
                        <?php endfor;?>
                    </tr>
                <tbody>
                    <?php for($dia_semana = 0; $dia_semana <= 6; $dia_semana++): ?>
                    <tr>
                        <td><b><?php echo $dias_semana[$dia_semana] ?></b></td>
                        <?php for($hora=0; $hora <= 23; $hora++): ?>
	                        <?php if(isset($horas_disponiveis[$dia_semana]) && (array_key_exists($hora, $horas_disponiveis[$dia_semana]))) : ?>
		                        <td><?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$dia_semana.'.'.$hora.'.capacidade', array('value' => $horas_disponiveis[$dia_semana][$hora], 'label' => false, 'type' => 'text', 'class' => 'input-mini just-number input-hora', 'maxlength'=> "5", 'style' => 'background: #38D159;')); ?></td>
							<?php else : ?>
								<td><?php echo $this->BForm->input( 'FornecedorCapacidadeAgenda.'.$dia_semana.'.'.$hora.'.capacidade', array('label' => false, 'type' => 'text', 'class' => 'input-mini just-number input-hora', 'style' => 'background: #C49191;', 'disabled' => 'disabled', 'maxlength'=> "5")); ?></td>
	                        <?php endif; ?>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor?>
                </tbody>
            </table>
        </div>
    <?php echo $this->BForm->end() ?>
	
	<div id="quadro_agenda">
	
	</div>
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

<!-- Modal bloquear datas-->
<div id="bloquearDatas" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 900px; margin-left: -450px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Bloquear datas</h3>
	</div>
	<div class="modal-body">
	<div class="row-fluid">
		<div class="span6">
		<?php echo $this->BForm->input('data', array('placeholder'=>'Data', 'id' => 'data-bloqueio', 'value' => date('d/m/Y'),  'label' => 'Data de bloqueio:', 'class' => 'input-small datepicker', 'title' => 'Data')); ?>   
		</div>
		<div class="span6">
			<label>Legendas:</label>
			<div class="pull-left margin-right-20 margin-top-8">
			<span class="legenda-verde"></span> Horário disponível 	
			</div> 
			<div class="pull-left margin-top-8">
			<span class="legenda-vermelho"></span> Horário bloqueado 
			</div>
		</div>
	</div>
		<hr>
		<h4>Selecione os horários que serão bloqueados:</h4>
		<h5 class="dia-da-semana"></h5>
		<div class="margin-top-20 relative js-obtem-horarios">
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button type="button" class="btn btn-primary salvar-horarios" data-dismiss="modal" aria-hidden="true">Salvar</button>
	</div>
</div>

<!-- Modal datas bloqueadas -->
<div id="datasBloqueadas" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 900px; margin-left: -450px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Datas bloqueadas</h3>
	</div>
	<div class="modal-body">
	<table class="table blocked">
		<thead>
		<tr>
			<th>Data</th>
			<th>Horarios bloqueados</th>
			<th>Ação</th>
		</th>
		</thead>
		<tbody>
		<?php foreach ($datas_bloqueadas as $key => $data_bloqueada) {
			echo '<tr>';
			echo '<td>';
			echo $data_bloqueada['Fadb']['data'];
			echo '</td>';
			echo '<td>';
			$horarios = (json_decode($data_bloqueada['Fadb']['horarios']));
			if($data_bloqueada['Fadb']['bloqueado_dia_inteiro'] > 0) {
				echo '<span class="mini-block">';
				echo 'Bloqueado o dia inteiro';
				echo '</span>';
			} else {	
				if(!empty($horarios)) {
					foreach ($horarios as $key => $horario) {
						echo '<span class="mini-block">';
						if(strlen($horario) < 4) {
							echo "0" . substr($horario, 0, 1) . ":" . substr($horario, 1, 2);
						} else {
							echo substr($horario, 0, 2) . ":" . substr($horario, 2, 2);
						}
						echo '</span>';		
					}
				}
			}
			echo '</td>';
			echo '<td>';
			echo '<span class="icon-remove pointer remover-horario" data-codigo="'.$data_bloqueada['Fadb']['codigo'].'" data-toggle="tooltip" data-title="Remover horário">';
			echo '</td>';
			echo '</tr>';
		} ?>
		</tbody>
	</table>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
	</div>
</div>

  <?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		$("#FornecedorCapacidadeAgendaEditarForm").submit(function(event) {
			FornecedoresCapacidadeAgenda.enviaFormAjaxEditar(this);
			event.preventDefault();
		});
		setup_mascaras();
		$(".datepicker").datepicker({
			minDate: new Date('.date('Y').', '.((int)date('m') - 1).', '.date('d').'),
			dateFormat: "dd/mm/yy",
			dayNames : ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sabado"],
			dayNamesShort : ["Dom","Seg","Ter","Qua","Qui","Sex","Sab"],
			dayNamesMin : ["D","S","T","Q","Q","S","S"],
			monthNames : ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
			monthNamesShort : ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"],
			onSelect: function(response) {
				FornecedoresCapacidadeAgenda.getData(response, '.$codigo_fornecedor.', '.$codigo_lista_de_preco_produto_servico.');
			} 
		}).after($("<img>", {src: baseUrl + "img/calendar.gif", style: "margin-top: -8px"}));
		$("body").on("click", ".cancel-date", function() {
			if($(this).hasClass("block")) {
				$(this).css("background-color", "#6ae26a").removeClass("block");
			} else {
				$(this).css("background-color", "red").addClass("block");
			}
		});
		$("body").on("click", ".selecionar-todos-horarios", function() {
			$(".cancel-date").css("background-color", "red").addClass("block");
		});
		$("body").on("click", ".salvar-horarios", function() {
			var horarios = [];
			var i = 0;
			$(".cancel-date.block").each(function() {
				horarios[i] = parseInt($(this).attr("data-value"));
				i++;
			});
			if(horarios != "") {
				var diaInteiro = false;
				if($(".cancel-date").not(".block").length == 0) {
					diaInteiro = true;
				}
				FornecedoresCapacidadeAgenda.salvarHorarios(horarios, diaInteiro, $("#data-bloqueio").val(), '.$codigo_fornecedor.', '.$codigo_lista_de_preco_produto_servico.', function(response) {
						if(response.error == false) {
							var html = "<tr><td>" + $("#data-bloqueio").val() + "</td><td>";
							if(diaInteiro) {
								html += "<span class=\"mini-block\">Bloqueado o dia inteiro</span>";
							} else {
								$.each(horarios, function(index, val){
									if(val.toString().length < 4) {
										html += "<span class=\"mini-block\">0" + val.toString().substr(0, 1) + ":" +  val.toString().substr(1, 2) + "</span>";
									} else {
										html += "<span class=\"mini-block\">" + val.toString().substr(0, 2) + ":" +  val.toString().substr(2, 2) + "</span>";
									}
								});
							}
							html += "</td><td><span class=\"icon-remove pointer remover-horario\" data-codigo=" + response.codigo + " data-toggle=\"tooltip\" data-title=\"Remover horário\"></span></td></tr>";
							$(".table.blocked tbody").append(html);
						}
				});
			} else {
				swal({
					type: "warning",
					title: "Atenção",
					text: "Nenhum horário foi selecionado. Tente novamente."
				});
			}
		});
		$("body").on("click", ".remover-horario", function() {
			var este = $(this);
			swal({
				type: "info",
				title: "Atenção",
				text: "Tem certeza que deseja excluir esta informação?",
				showCancelButton: true,
				confirmButtonText: "Continuar",
				cancelButtonText: "Cancelar"
			}, function(isConfirm) {
				if(isConfirm) {
					FornecedoresCapacidadeAgenda.excluiHorario(este, este.attr("data-codigo"), '.$codigo_fornecedor.', '.$codigo_lista_de_preco_produto_servico.');
				}
			});
		});
	});
	'); ?>   

<?php echo $this->Buonny->link_js('fornecedores_capacidade_agenda'); ?>