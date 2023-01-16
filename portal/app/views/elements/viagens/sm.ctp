<? //$posiciona = true; ?>
<div class='row-fluid inline'>
	<table class='table table-striped' id="problemas">
		<thead>
			<th class='input-small'>SM</th>
			<th class='input-small'>Nº Pedido</th>
			<th class='input-medium'>Previsão Inicio</th>
			<th class='input-xlarge'>Transportador</th>
			<th class='input-xlarge'>Embarcador</th>
			<th class='input-small'>CPF</th>
			<th class='input-large'>Motorista</th>
			<!--<th class='input-mini numeric' title='Validade do Checklist'>Vld Chk</th>
			<th style="width:13px">&nbsp;</th>-->
			<th style="width:13px">&nbsp;</th>
		</thead>
		<tbody>
			<?php if($viagens): ?>
				<?php foreach ($viagens as $viag): ?>
					<?php $data_validade = strtotime($viag['0']['cvei_data_cadastro'].' +'.$viag['TRacsRegraAceiteSm']['racs_validade_checklist'].' days') ?>
					<?php $data_inicio = $viag['TViagViagem']['viag_data_inicio'] ? AppModel::dateToDbDate2($viag['TViagViagem']['viag_data_inicio']) : Date('Y-m-d'); ?>
					<tr>
						<td><?php echo $this->Buonny->codigo_sm($viag['TViagViagem']['viag_codigo_sm']) ?></td>
						<td><?php echo $viag['TViagViagem']['viag_pedido_cliente'] ?></td>
						<td><?php echo $viag['TViagViagem']['viag_previsao_inicio'] ?></td>
						<td><?php echo $viag['Transportador']['pess_nome'] ?></td>
						<td><?php echo $viag['Embarcador']['pess_nome'] ?></td>	
						<td><?php echo $viag['MotoristaCpf']['pfis_cpf'] ?></td>
						<td><?php echo $viag['Motorista']['pess_nome'] ?></td>
						<!--
						<td class='numeric'><?php echo $viag['TRacsRegraAceiteSm']['racs_validade_checklist'] ?></td>
						<td>
							<?php if($viag['TRacsRegraAceiteSm']['racs_verificar_checklist']): ?>
								<?php $bagde = ($viag['0']['cvei_posicao_checklist']==1 ? 'badge-success' : 'badge-cancelado') ?>
								<span class="badge-empty badge <?=$bagde?>" title="Checklist <?=(!empty($checklist_posicoes[$viag['0']['cvei_posicao_checklist']]) ? $checklist_posicoes[$viag['0']['cvei_posicao_checklist']] : $checklist_posicoes[TCveiChecklistVeiculo::POSICAO_NAO_REALIZADO])?>"></span>
							<?php else: ?>
								<span class="badge-empty badge" title="Viagem sem regra de checklist"></span>
							<?php endif ?>
						</td>
						-->
						<td>
							<?php if( $posiciona && !$viag['TViagViagem']['viag_checklist'] && !$viag['TViagViagem']['viag_data_inicio']): ?>
								<?php echo $html->link('', array('controller' => 'Viagens', 'action' => 'checklist',$cliente['Cliente']['codigo'], $viag['TViagViagem']['viag_codigo']), array('class' => 'icon-wrench', 'title' => 'Fazer o checklist de saída')); ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		
		$("#RecebsmMotoristaCpf").change(function(){
			consulta_motorista($(this).val());

			return false;
		});

		$("#RecebsmAjudanteCpf").change(function(){
			consulta_ajudante($(this).val());

			return false;
		});

		function consulta_motorista(cpf){
			var motorista 	= $("#RecebsmMotoristaCpf");
			var s_motorista	= $("#status-motorista");

			if(cpf){
				$.ajax({
					url: baseUrl + "Profissionais/carregarLogPorCpf/"+ cpf +"/"+ Math.random(),
					type: "post",
					dataType: "json",
					beforeSend: function(){
						s_motorista.html("<strong>Aguarde:</strong><br><br> ...");
					},
					success: function(data){
						if(data){
							s_motorista.html("<strong>Status:</strong><br>"+data.ProfissionalLog.nome+"<br> "+ data.Status.descricao);
						} else {
							s_motorista.html("<strong>Status:</strong><br><br> NÃO POSSUI STATUS");
						}
					},
					error: function(obj,msg,erro){
						s_motorista.html("<strong>Erro:</strong><br><br> "+ erro );
					}

				});
			}
		}

		function consulta_ajudante(cpf){
			var ajudante 	= $("#RecebsmAjudanteCpf");
			var s_ajudante	= $("#status-ajudante");

			if(cpf){
				$.ajax({
					url: baseUrl + "Profissionais/carregarLogPorCpf/"+cpf+"/"+ Math.random(),
					type: "post",
					dataType: "json",
					beforeSend: function(){
						s_ajudante.html("<strong>Aguarde:</strong><br><br> ...");
					},
					success: function(data){
						if(data){
							s_ajudante.html("<strong>Status:</strong><br>"+data.ProfissionalLog.nome+"<br> "+ data.Status.descricao);
						} else {
							s_ajudante.html("<strong>Status:</strong><br><br> NÃO POSSUI STATUS");
						}
					},
					error: function(obj,msg,erro){
						s_ajudante.html("<strong>Erro:</strong><br><br> "+ erro );
					}

				});
			}
		}

	});', false);
?>