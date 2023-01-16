	<div id='dados_sm'>
		<div class="row-fluid inline">	
			<?php echo $this->BForm->hidden('codigo'); ?>
			<?php echo $this->BForm->input('sm', array('class' => 'input-small', 'label' => 'SM', 'after' => $html->link('...', "javascript:busca_dados_sm()", array('id' =>'busca_dados_sm','class' => 'btn btn-search-ellipsis', 'title' => 'Buscar dados')) )); ?>
			<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => 'Placa')) ?>
			<?php echo $this->BForm->hidden('codigo_profissional'); ?>
			<?php echo $this->BForm->input('codigo_documento_profissional', array('class' => 'input-medium cpf', 'label' => 'CPF Motorista')) ?>
			<?php echo $this->BForm->input('nome_profissional', array('class' => 'input-xlarge', 'label' => 'Nome Motorista', 'readonly' => true)) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_transportador', 'label' => 'Código', 'name_display' => array('label' => 'Transportador'), 'checklogin' => false)) ?>
			<?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_embarcador', 'label' => 'Código', 'name_display' => array('label' => 'Embarcador'), 'checklogin' => false)) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('codigo_seguradora', array('class' => 'input-xlarge', 'label'=>'Seguradora', 'options'=>$seguradoras, 'empty'=>'Selecione')); ?>
			<?php echo $this->Buonny->input_codigo_corretora($this, 'codigo_corretora', 'Corretora', true, 'Sinistro') ?>
		</div>
	</div>
	<div class="row-fluid inline">
		
		<?php echo $this->BForm->input('data_evento', array('class' => 'input-small data', 'type'=>'text', 'label' => 'Data Evento')); ?>
		<?php echo $this->BForm->input('hora', array('class' => 'input-small hora', 'label' => 'Hora Evento')); ?>		        
		<?php echo $this->BForm->input('natureza', array('class' => 'input-medium', 'label'=>'Natureza', 'options'=>$natureza,'empty'=>'Selecione')); ?>
		<?php echo $this->BForm->input('status_veiculo', array('class' => 'input-medium', 'label'=>'Status Veículo', 'options'=>$status_veiculo, 'empty'=>'Selecione')); ?>
		<?php echo $this->BForm->input('valor_carga', array('class' => 'input-small numeric moeda', 'label' => 'Valor Carga', 'maxlength'=>false)); ?>	
		<?php echo $this->BForm->input('valor_sinistrado', array('class' => 'input-small numeric moeda', 'label' => 'Valor Sinistrado','maxlength'=>false)); ?>
		<?php echo $this->BForm->input('valor_recuperado', array('class' => 'input-small numeric moeda', 'label' => 'Valor Recuperado','maxlength'=>false)); ?>
	</div>	

	<div class="row-fluid inline">
		<?php echo $this->BForm->input('endereco', array('class' => 'input-large', 'label' => 'Endereco')); ?>
		<?php echo $this->Buonny->input_codigo_endereco_cidade($this, 'codigo_endereco_cidade', 'Cidade', true); ?>
		<?php echo $this->BForm->input('latitude', array('class' => 'input-small', 'label' => 'Latitude')); ?>
		<?php echo $this->BForm->input('longitude', array('class' => 'input-small', 'label' => 'Longitude')); ?>
	    
	</div>

	<div class="row-fluid inline">
		<?php echo $this->BForm->input('modo_de_operacao', array('class' => 'input-xxlarge', 'type'=>'textarea', 'rows'=>4, 'label' => 'Modus Operandi')); ?>		
	</div>

	<div>
		<?php echo $this->BForm->input('observacao', array('class' => 'input-xxlarge', 'type'=>'textarea', 'rows'=>4,'label' => 'Observação')); ?>
	</div>

	<div>
		<?php echo $this->BForm->input('atuacao_central', array('class' => 'input-xxlarge', 'type'=>'textarea', 'rows'=>4,'label' => 'Atuação Central')); ?>
	</div>

	<div>
		<?php echo $this->BForm->input('avalicao_geral', array('class' => 'input-medium', 'label'=>'Avaliação Geral', 'options'=>$avaliacao_geral, 'empty'=>'Selecione')); ?>
	</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	function busca_dados_sm() {
		var sm = $("#SinistroSm").val();
		if (sm.length > 0) {
			$.ajax({
				url:baseUrl + "viagens/view/" + sm + "/" + Math.random(),
				dataType: "json",
				beforeSend: function() {
					bloquearDiv(jQuery("#dados_sm"));
				},
				success: function(data) {
					if (data) {
						$("#SinistroPlaca").val(data.TVeicVeiculo.veic_placa).blur();
						//$("#SinistroCodigoProfissional").val(data.TPfisPessoaFisica.codigo);
						$("#SinistroCodigoDocumentoProfissional").val(data.TPfisPessoaFisica.pfis_cpf);
						$("#SinistroNomeProfissional").val(data.TPessPessoa.pess_nome);
						$("#SinistroCodigoTransportador").val(data.Transportador.codigo);
						$("#SinistroCodigoTransportadorName").val(data.Transportador.pjur_razao_social);
						$("#SinistroCodigoEmbarcador").val(data.Embarcador.codigo);
						$("#SinistroCodigoEmbarcadorName").val(data.Embarcador.pjur_razao_social);
						$("#SinistroCodigoSeguradora").val(data.Seguradora.codigo);
						$("#SinistroCodigoCorretora").val(data.Corretora.codigo);
						$("#SinistroCodigoCorretoraVisual").val(data.Corretora.pjur_razao_social);
					}
				},
				complete: function() {
					jQuery("#dados_sm").unblock();					
				}
			});
		}
	}

	$(document).on("blur", "#SinistroCodigoDocumentoProfissional", function() {
		$("#SinistroNomeProfissional").val("Aguarde...");
		var cpf = $("#SinistroCodigoDocumentoProfissional").val();
		if (cpf) {
			$.ajax({
				url: baseUrl + "profissionais/carregarPorCpf/"+ cpf + "/" + Math.random(),
				type: "post",
				dataType: "json",
				success: function(data){
					jQuery(".motorista-nao-encontrado").remove();
					if(data && data.Profissional.nome){
						$("#SinistroCodigoProfissional").val(data.Profissional.codigo);
						$("#SinistroNomeProfissional").val(data.Profissional.nome);
					}else{
						$("#SinistroCodigoProfissional").val("");
						$("#SinistroNomeProfissional").val("");
						
						var a = $("<a class=\'btn btn-mini btn-primary\'>Adicionar Motorista</a>").click(function(event){ 
							open_dialog(baseUrl + "profissionais/incluir/" + cpf, "Adicionar motorista", 572)
							return false; 
						});
						jQuery("#SinistroCodigoDocumentoProfissional").parent().append(jQuery("<div class=\'control-group error motorista-nao-encontrado\' style=\'clear:both\'>").append("<div class=\'help-inline\' style=\'padding: 0;\'>Motorista não cadastrado</div>").append(a));
					}
				}

			});
		}
	})
 
    jQuery(document).ready(function(){
		setup_mascaras();
		setup_datepicker();
		setup_time();
    });', false);
?>