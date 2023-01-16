<?= $this->BForm->hidden('racs_codigo') ?>
<?= $this->BForm->hidden('racs_pjur_pess_oras_codigo') ?>
<?= $this->BForm->hidden('codigo_cliente') ?>
<div>
	<?if(!empty($this->data['TRacsRegraAceiteSm']['racs_usuario_alterou'])) :?>
		<div class='row-fluid inline'>
			<h6>Usuário alteração: <?= strtoupper($this->data['TRacsRegraAceiteSm']['racs_usuario_alterou']);?> <br />
			Data alteração: <?= strtoupper($this->data['TRacsRegraAceiteSm']['racs_data_alteracao']);?> </h6>
			<br />
		</div>
	<?endif;?>
	<div class='row-fluid inline'>
		<?= $this->BForm->input('racs_esta_codigo', array('options' => $esta_codigos, 'class' => 'input-mini', 'label' => 'UF Origem', 'empty' => 'Todos')) ?>
		<?= $this->BForm->input('racs_ttra_codigo', array('options' => $ttra_codigos, 'class' => 'input-medium', 'label' => 'Tipo de Transporte', 'empty' => 'Todos')) ?>
		<?= $this->BForm->input('racs_valor_maximo_viagem', array('class' => 'moeda input-medium numeric', 'label' => 'Valor Máximo Viagem')) ?>
		<?= $this->BForm->input('racs_prod_codigo', array('label' => 'Produtos','empty' => 'Selecione um produto' ,'options' => $produtos,'class' => 'input-xlarge')) ?>
	</div>
	</div>
	<div class='row-fluid inline'>
		<div class='span7'>
			<h6>Verificações</h6>
			<div class='span4'>
				<?= $this->BForm->input('racs_verificar_checklist', array('label' => 'Controlar checklist', 'type' => 'checkbox')) ?>
			</div>
			<div class='span4'>
				<?= $this->BForm->input('racs_verificar_checklist_carreta', array('label' => 'Controlar checklist carreta', 'type' => 'checkbox')) ?>
			</div>
		</div>
		<div class='span5'>
			<h6>Motorista</h6>
			<?= $this->BForm->input('racs_carreteiro', array('class' => 'input-small', 'label' => 'Carreteiro', 'type' => 'checkbox')) ?>
			<?= $this->BForm->input('racs_agregado', array('class' => 'input-small', 'label' => 'Agregado', 'type' => 'checkbox')) ?>
			<?= $this->BForm->input('racs_funcionario_motorista', array('class' => 'input-large', 'label' => 'Funcionário', 'type' => 'checkbox')) ?>
		</div>
	</div>
	<div class='row-fluid inline'>
		<div class='span7'>
			<h1></h1>
			<div class="span4">
				<div id ='validade_checklist'>
					<?= $this->BForm->input('racs_validade_checklist', array('class' => 'just-number input-small numeric', 'label' => 'Validade checklist', 'maxlength' => 5)) ?>
					<?= $this->BForm->input('racs_bloquear_sm_checklist', array('class' => 'input-small', 'label' => 'Bloquear Entrada de SM', 'type' => 'checkbox')) ?>
				</div>
			</div>
			<div div class="span4">
				<div id ='validade_checklist_carreta'>
					<?= $this->BForm->input('racs_validade_checklist_carreta', array('class' => 'just-number input-small numeric', 'label' => 'Validade checklist Carreta', 'maxlength' => 5)) ?>
					<?= $this->BForm->input('racs_bloquear_sm_checklist_carreta', array('class' => 'input-small', 'label' => 'Bloquear Entrada de SM por carreta', 'type' => 'checkbox')) ?>
				</div>
			</div>
		</div>
	</div>
	<div class='row-fluid inline'>
		<h4>Alvos Origem para checklist</h4>
		<div class='lista-alvos-clientes'>
			<?php echo $this->element('regras_aceite_sm_alvos', array(
				   'titulo'=>'Alvos',  
				   'index'=>'ccva_refe_codigo', 
				   'model'=>'TCcvaCdChecklistValido'
				 ))?>
			</div>
	</div>	
	<div class='row-fluid inline'>
		<div class='span7'>
			<h6>Escolta</h6>
			<?= $this->BForm->input('racs_escolta_velada', array('class' => 'input-small', 'label' => 'Velada', 'type' => 'checkbox')) ?>
			<?= $this->BForm->input('racs_escolta_armada', array('class' => 'input-small', 'label' => 'Armada', 'type' => 'checkbox')) ?>
			<?= $this->BForm->input('racs_escolta_parcial',array('class' => 'input-small', 'label' => 'Acompanhamento Parcial', 'type' => 'checkbox')) ?>
		</div>
		<div class='span2'>
			<h6>Isca</h6>
			<?= $this->BForm->input('racs_obrigar_isca', array('class' => 'input-small', 'label' => 'Solicitar Isca', 'type' => 'checkbox')) ?>		
		</div>	
		<div class='span2'>
			<h6>Veículo</h6>
			<?= $this->BForm->input('racs_idade_maxima_veiculo', array('class' => 'input-small just-number numeric', 'label' => 'Idade Máxima')) ?>
		</div>
	</div>
	<div class='row-fluid inline'>
		<div class='span3'>
			<div id ='qtd_escolta_velada'>
				<?= $this->BForm->input('racs_qtd_escolta_velada', array('class' => 'just-number input-small numeric', 'label' => 'Qtd. Velada', 'maxlength' => 5)) ?>
			</div>
		</div>			
		<div class='span3'>
			<div id ='qtd_escolta_armada'>
				<?= $this->BForm->input('racs_qtd_escolta_armada', array('class' => 'just-number input-small numeric', 'label' => 'Qtd. Armada', 'maxlength' => 5)) ?>
			</div>	
		</div>	
		<div class='span3'>
			<div id='qtd_iscas'>
				<?= $this->BForm->input('racs_qtd_isca', array('class' => 'just-number input-small numeric', 'label' => 'Qtd. Iscas', 'maxlength' => 5)) ?>
			</div>
		</div>		
	</div>
	<div class='row-fluid inline'>
		<div class='span7'>
			<h6>Comboio</h6>
			<?= $this->BForm->input('racs_quantidade_comboio', array('class' => 'just-number input-small numeric', 'label' => 'Qtd.Max.Comboio')) ?>
			<?= $this->BForm->input('racs_valor_maximo_comboio',array('class' => 'moeda input-small numeric', 'label' => 'Vr.Max.Comboio')) ?>
		</div>
		<div class='span4'>
			<h6>Horário Rodagem</h6>
			<?= $this->BForm->input('racs_horario_viagem_de', array('class' => 'input-mini hora', 'label' => 'De', 'type' => 'text')) ?>
			<?= $this->BForm->input('racs_horario_viagem_ate', array('class' => 'input-mini hora', 'label' => 'Até', 'type' => 'text')) ?>
		</div>
	</div>
	<h6>Tipos de Veículos</h6>
	<div class='row-fluid inline'>
		<?= $this->BForm->input('TRatvRegraAceiteTipoVeiculo.ratv_tvei_codigo', array('class' => 'checkbox inline input-xlarge', 'label' => false, 'multiple' => 'checkbox', 'options' => $tvei_codigos)) ?>
	</div>
	<h6>Periféricos Obrigatórios</h6>
	<div class='row-fluid inline'>
		<?= $this->BForm->input('TPracPerifericoRacs.prac_ppad_codigo', array('class' => 'checkbox inline input-xlarge', 'label' => false, 'multiple' => 'checkbox', 'options' => $ppad_codigos)) ?>
	</div>
<div>
<?= $this->Javascript->codeBlock("
	function escolta_velada() {		
		var selecao = document.getElementById('TRacsRegraAceiteSmRacsEscoltaVelada');
		
		if(selecao.checked) {
			$('div#qtd_escolta_velada').show();
		}else {
			$('div#qtd_escolta_velada').hide();
		}
	}

	function escolta_armada() {		
		var selecao = document.getElementById('TRacsRegraAceiteSmRacsEscoltaArmada');		
		if(selecao.checked) {
			$('div#qtd_escolta_armada').show();
		}else {
			$('div#qtd_escolta_armada').hide();

		}
	}

	function iscas() {		
		var selecao = document.getElementById('TRacsRegraAceiteSmRacsObrigarIsca');		
		if(selecao.checked) {
			$('div#qtd_iscas').show();
		}else {
			$('div#qtd_iscas').hide();

		}
	}

	function validade_checklist() {		
		var selecao = document.getElementById('TRacsRegraAceiteSmRacsVerificarChecklist');		
		if(selecao.checked) {
			$('div#validade_checklist').show();
		}else {
			$('div#validade_checklist').hide();
			$('#TRacsRegraAceiteSmRacsBloquearSmChecklist').prop('checked',false);
		}
	}

	function validade_checklist_carreta() {		
		var selecao = document.getElementById('TRacsRegraAceiteSmRacsVerificarChecklistCarreta');		
		if(selecao.checked) {
			$('div#validade_checklist_carreta').show();
		}else {
			$('div#validade_checklist_carreta').hide();
			$('#TRacsRegraAceiteSmRacsBloquearSmChecklistCarreta').prop('checked',false);
		}
	}

	$(document).ready(function(){
		setup_time();
		escolta_velada();
		escolta_armada();
		iscas();
		validade_checklist();
		validade_checklist_carreta();

		$(document).on('change','#TRacsRegraAceiteSmRacsEscoltaVelada',function(){
				escolta_velada();
		});
		$(document).on('change','#TRacsRegraAceiteSmRacsEscoltaArmada',function(){
				escolta_armada();
		});
		$(document).on('change','#TRacsRegraAceiteSmRacsObrigarIsca',function(){
				iscas();
		});
		$(document).on('change','#TRacsRegraAceiteSmRacsVerificarChecklist',function(){
				validade_checklist();
		});
		$(document).on('change','#TRacsRegraAceiteSmRacsVerificarChecklistCarreta',function(){
				validade_checklist_carreta();
		});

	})") ?>
