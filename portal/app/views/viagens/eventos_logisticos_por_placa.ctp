<?php if (!isset($sac) || !$sac): ?>
	<div class='well'>
		<?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'eventos_logisticos_por_placa', $nova_janela))) ?>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa', 'readonly' => $this->layout == 'new_window')) ?>
				<?php echo $this->Buonny->input_periodo($this) ?>
			</div>
			<?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $this->BForm->end() ?>
	</div>
<?php endif ?>
<?php if (!empty($this->data) && !empty($this->data['TViagViagem']['placa'])): ?>
	<div class='well inline'>
		<strong>Placa: </strong><?= $dataVeicVeiculo['TVeicVeiculo']['veic_placa'] ?>
		<?php if ($sac): ?>
			<strong>Estação: </strong><?= $dataVeicVeiculo['TErasEstacaoRastreamento']['eras_descricao'] ?>
		<?php endif ?>
	</div>
	<h4>Última Posição</h4>
	<div id="ultima_posicao">
		<table class='table'>
			<thead>
				<th class='input-medium'>Data</th>
				<th>Posição</th>
			</thead>
		</table>
	</div>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#macros" data-toggle="tab" class="macros">Macros Logísticas</a></li>
		<li><a href="#sms" data-toggle="tab" class="sms">Solicitações de Monitoramento</a></li>
		<li><a href="#veiculo" data-toggle="tab" class="veiculo">Cadastro do Veiculo</a></li>
		<?php if (!$sac): ?>
			<li><a href="#eventos" data-toggle="tab" class="eventos">Eventos</a></li>
		<?php endif ?>
		<li><a href="#msg_livre" data-toggle="tab" class="msg_livre">Mensagem Livre</a></li>
		<?php if (!$sac): ?>
			<li><?php echo $this->Html->link('Histórico de posições', array('controller'=>'veiculos', 'action'=>'historico_posicoes', strtoupper($this->data['TViagViagem']['placa']), preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3$2$1 000000', $this->data['TViagViagem']['data_inicial']), preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3$2$1 235959', $this->data['TViagViagem']['data_final'])), array('onclick'=>'return open_popup(this);')); ?></li>
		<?php endif ?>
		<li><a href="#checklist" data-toggle="tab" class="checklist">Histórico de Checklists</a></li>
		<?php if (isset($authUsuario['Usuario']['codigo_uperfil']) && $authUsuario['Usuario']['codigo_uperfil'] == 3): ?>
			<li><a href="#sensores" data-toggle="tab" class="sensores">Sensores</a></li>
		<?php endif ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="macros">
			<h4>Tempos</h4>
			<div id='tempos_por_placa'>
				<table class='table table-striped'>
					<thead>
						<th>Inicio</th>
						<th>Fim</th>
						<th>Posição</th>
						<th class='numeric'>Em Viagem</th>
						<th class='numeric'>Parado</th>
					</thead>
				</table>
			</div>
			<h4>Macros Enviadas pelo Motorista</h4>
			<div id='macros_por_placa'>
				<table class='table table-striped'>
					<thead>
						<th>Hora</th>
						<th>Posição</th>
						<th>Macro</th>
					</thead>
				</table>
			</div>
		</div>
		<div class="tab-pane" id="sms" style='min-height:50px'>&nbsp;</div>
		<!-- Aba VEICULO criada por George Rotteny -->
		<div class="tab-pane" id="veiculo">
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('veiculo_placa', array('label' => 'Placa', 'empty' => 'Placa','type' => 'text', 'value' => $dataVeicVeiculo['TVeicVeiculo']['veic_placa'], 'readonly' => true, 'class' => 'input-small')) ?>
				<?php echo $this->BForm->input('veiculo_tipo', array('label' => 'Tipo', 'empty' => 'Tipo Veículo','type' => 'text', 'value' => $dataVeicVeiculo['TTveiTipoVeiculo']['tvei_descricao'], 'readonly' => true, 'class' => 'input-large')) ?>
				<?php echo $this->BForm->input('veiculo_fabricante', array('label' => 'Fabricante', 'empty' => 'Fabricante','type' => 'text', 'value' => $dataVeicVeiculo['TMveiMarcaVeiculo']['mvei_descricao'], 'readonly' => true)) ?>
				<?php echo $this->BForm->input('veiculo_modelo', array('label' => 'Modelo', 'empty' => 'Modelo','type' => 'text', 'value' => $dataVeicVeiculo['TMvecModeloVeiculo']['mvec_descricao'], 'readonly' => true)) ?>
				
			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('estado_emplacamento', array('label' => 'UF', 'empty' => 'UF','type' => 'text', 'value' => $dataVeicVeiculo['TEstaEstado']['esta_sigla'], 'readonly' => true, 'class' => 'input-small')) ?>
				<?php echo $this->BForm->input('cidade_emplacamento', array('label' => 'Cidade', 'empty' => 'Cidade','type' => 'text', 'value' => $dataVeicVeiculo['TCidaCidade']['cida_descricao'], 'readonly' => true, 'class' => 'input-large')) ?>
				<?php echo $this->BForm->input('veiculo_cor', array('label' => 'Cor', 'empty' => 'Cor','type' => 'text', 'value' => $dataVeicVeiculo['TVeicVeiculo']['veic_cor'], 'readonly' => true, 'class' => 'input-medium')) ?>
				<?php echo $this->BForm->input('veiculo_chassi', array('label' => 'Chassi', 'empty' => 'Chassi','type' => 'text', 'value' => $dataVeicVeiculo['TVeicVeiculo']['veic_chassi'], 'readonly' => true)) ?>
				<?php echo $this->BForm->input('renavam', array('label' => 'Renavam', 'empty' => 'Renavam','type' => 'text', 'value' => $dataVeicVeiculo['TVeicVeiculo']['veic_renavam'], 'readonly' => true, 'class' => 'input-medium')) ?>
				
			</div>

			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('ano_fabricacao', array('label' => 'Ano Fabricacao', 'empty' => 'Ano Fabricacao','type' => 'text', 'value' => $dataVeicVeiculo['TVeicVeiculo']['veic_ano_fabricacao'], 'readonly' => true, 'class' => 'input-small')) ?>
				<?php echo $this->BForm->input('ano_modelo', array('label' => 'Ano Modelo', 'empty' => 'Ano Modelo','type' => 'text', 'value' => $dataVeicVeiculo['TVeicVeiculo']['veic_ano_modelo'], 'readonly' => true, 'class' => 'input-small')) ?>
				<?php echo $this->BForm->input('tecnologia', array('label' => 'Tecnologia', 'empty' => 'Tecnologia','type' => 'text', 'value' => $dataVeicVeiculo['TTecnTecnologia']['tecn_descricao'], 'readonly' => true)) ?>
				<?php echo $this->BForm->input('serie', array('label' => 'Numero de Série', 'empty' => 'Numero de Série','type' => 'text', 'value' => $dataVeicVeiculo['TTermTerminal']['term_numero_terminal'] , 'readonly' => true)) ?>
			</div>

			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('telefone', array('label' => 'Telefone', 'empty' => 'Telefone','type' => 'text', 'value' => $dataVeicVeiculo['TVeicVeiculo']['veic_telefone'], 'readonly' => true, 'class' => 'input-small')) ?>
				<?php echo $this->BForm->input('radio', array('label' => 'Radio', 'empty' => 'Radio','type' => 'text', 'value' => $dataVeicVeiculo['TVeicVeiculo']['veic_radio'], 'readonly' => true, 'class' => 'input-small')) ?>
				<?php echo $this->BForm->input('status', array('label' => 'Status', 'empty' => 'Status','type' => 'text', 'value' => $dataVeicVeiculo['TVeicVeiculo']['veic_status'], 'readonly' => true)) ?>
			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('tip_cliente', array('label' => 'Tipo de Veículo do Cliente', 'empty' => 'Tipo de Veículo do Cliente','type' => 'text', 'value' => ($tip_cliente)?$tip_cliente:NULL, 'readonly' => true, 'class' => 'input-large')) ?>
				<?php echo $this->BForm->input('cd_origem', array('label' => 'CD de Origem', 'empty' => 'CD de Origem','type' => 'text', 'value' => ($cd_origem)?$cd_origem:NULL, 'readonly' => true, 'class' => 'input-xxlarge')) ?>
			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('transportador', array('label' => 'Transportador Padrão', 'empty' => 'Transportador Padrão','type' => 'text', 'value' => ($dataTransport)?$dataTransport['ClientEmpresa']['raz_social']:NULL, 'readonly' => true, 'class' => 'input-large')) ?>
				<?php echo $this->BForm->input('motorista', array('label' => 'Motorista Padrão', 'empty' => 'Motorista Padrão','type' => 'text', 'value' => ($dataMotorista)?$dataMotorista['TPessPessoa']['pess_nome']:NULL, 'readonly' => true, 'class' => 'input-xxlarge')) ?>
			</div>			
			
			<div class='row-fluid inline'>
				<h4>Atuadores</h4>
				
				<?php foreach ($all_atuadores as $key => $valor): ?>
				<?php $flag = false ?>
				<label class="checkbox inline input-large" for="atuadores">
					<?php if ($atuadores): ?>
						<?php foreach ($atuadores as $veiculo_key => $veiculo_valor): ?>
							<?php 
								if(trim($key) == trim($veiculo_valor)){
									$flag = true; 
									break;
								}
							?>
						<?php endforeach; ?>
					<?php endif ?>
					<?php if($flag): ?>
					<input id="<?php echo 'Atuadores'.$key ?>" type="checkbox" value="<?php echo $key ?> " name="data[atuadores][]" checked="checked" disabled="disabled">
					<?php else: ?>
					<input id="<?php echo 'Atuadores'.$key ?>" type="checkbox" value="<?php echo $key ?> " name="data[atuadores][]" disabled="disabled">
					<?php endif; ?>

				<?php echo $valor ?>
				</label>
				
				<?php endforeach; ?>
			</div>
		</div>
		<!-- FIM da Aba de Veiculo -->
		<div class="tab-pane" id="eventos">&nbsp;</div>
		<div class="tab-pane" id="msg_livre">&nbsp;</div>
		<div class="tab-pane" id="checklist">&nbsp;</div>
		<div class="tab-pane" id="sensores"></div>
	</div>
	<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>

	<?php
	 //$inicial = comum::dateToTimestamp($this->data['TViagViagem']['data_inicial']);
	 //$final = comum::dateToTimestamp($this->data['TViagViagem']['data_final']);
	 $this->addScript($this->Javascript->codeBlock("jQuery(document).ready(function() {
		ultima_posicao('{$this->data['TViagViagem']['placa']}');
		tempos_por_placa('{$this->data['TViagViagem']['placa']}', '{$this->data['TViagViagem']['data_inicial']}', '{$this->data['TViagViagem']['data_final']}');
		macros_por_placa('{$this->data['TViagViagem']['placa']}', '{$this->data['TViagViagem']['data_inicial']}', '{$this->data['TViagViagem']['data_final']}');		
		$('.sms').click(function(){
			if( $('#sms').html() == '&nbsp;' )
				listar_sm_eventos_logisticos_por_placa('".serialize($this->data)."');
		});

		$('.eventos').click(function(){
			if( $('#eventos').html() == '&nbsp;' )
				eventos_logisticos_por_placa('{$this->data['TViagViagem']['placa']}', '{$this->data['TViagViagem']['data_inicial']}', '{$this->data['TViagViagem']['data_final']}');
		});

		$('.checklist').click(function(){					
			if( $('#checklist').html() == '&nbsp;' )
				checklist_por_placa('{$this->data['TViagViagem']['placa']}');
		});

		$('.msg_livre').click(function(){		
			carregar_mensagem_livre('".serialize($this->data)."','#msg_livre');
		});

		$('.sensores').click(function(){		
			carregar_sensores('{$this->data['TViagViagem']['placa']}','#sensores');
		});
		
		function listar_sm_eventos_logisticos_por_placa(this_data){
			var div = $('#sms');
			$.ajax({
				type: 'post',
				url: baseUrl + 'viagens/listar_sm_eventos_logisticos_por_placa/' + Math.random(),
				cache: false,
				data: {'dados':this_data },
				beforeSend : function(){
					bloquearDiv(div);
				},								
				success: function(data){
					div.html(data);
				},				
				error: function(erro,objeto,qualquercoisa){ 
					alert(erro+' - '+objeto+' - '+qualquercoisa); 
					div.unblock();
				}
			});
		}
		function checklist_por_placa(this_data){
			var div = $('#checklist');					
			$.ajax({
				type: 'get',
				url: baseUrl + 'checklists/listar_checklist_por_placa/'+this_data,
				cache: false,				
				beforeSend : function(){
					bloquearDiv(div);
				},								
				success: function(data){
					div.html(data);
				},				
				error: function(erro,objeto,qualquercoisa){ 
					alert(erro+' - '+objeto+' - '+qualquercoisa); 
					div.unblock();
				}
			});
		}

	})")) ?>
<?php endif ?>
<?php $this->addScript($this->Javascript->codeBlock("jQuery(document).ready(function() {
	setup_mascaras();
})")) ?>