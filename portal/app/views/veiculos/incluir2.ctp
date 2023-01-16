<div class='row-fluid inline'>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo']; ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
	</div>
</div>
<?php echo $this->BForm->create('TVeicVeiculo', array('url' => array('controller' => 'Veiculos','action' => 'incluir2',$this->data['Cliente']['codigo'],$this->data['TVeicVeiculo']['veic_placa'])));?>

	<?php echo $this->BForm->hidden('Cliente.codigo') ?>
	<?php echo $this->BForm->hidden('Cliente.codigo_documento') ?>
	<?php echo $this->BForm->hidden('TPessPessoa.pess_tipo') ?>
	<?php echo $this->BForm->hidden('frota',array('value' => 1)) ?>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('veic_codigo_externo',array('label' => 'Código Cliente','class' => 'input-small just-number', 'maxlength' => 3)) ?>
	<?php echo $this->BForm->input('veic_placa',array('label' => 'Placa','class' => 'input-small', 'readonly' => TRUE)) ?>
	<?php echo $this->BForm->input('veic_tvei_codigo', array('label' => 'Tipo de Veiculo', 'empty' => 'Tipo', 'class' => 'input-small cam-carr' ,'options' => $tipos)) ?>
	<?php echo $this->BForm->input('TMvecModeloVeiculo.mvec_mvei_codigo', array('label' => 'Fabricante','class' => 'input-large fabricante', 'empty' => 'Fabricante', 'options' => $fabricantes)) ?>
	<?php echo $this->BForm->input('veic_mvec_codigo', array('label' => 'Modelo','class' => 'input-large modelo', 'empty' => 'Modelo', 'options' => $modelos)) ?>
	<?php echo $this->BForm->input('VeiculoCor.codigo', array('label' => 'Cor','class' => 'input-medium' ,'empty' => 'Cor', 'options' => $cores)) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('veic_ano_fabricacao', array('label' => 'Ano Fabricacao', 'empty' => 'Ano Fabricacao','class' => 'input-medium just-number','maxlength' => 4)) ?>
	<?php echo $this->BForm->input('veic_ano_modelo', array('label' => 'Ano Modelo', 'empty' => 'Ano Modelo','class' => 'input-medium just-number','maxlength' => 4)) ?>
	<?php echo $this->BForm->input('veic_chassi', array('label' => 'Chassi', 'type' => 'text', 'maxlength' => 49, 'class' => 'input-small')) ?>
	<?php echo $this->BForm->input('veic_renavam', array('label' => 'Renavam', 'type' => 'text', 'class' => 'input-medium just-number','maxlength' => 49)) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('TPaisPais.pais_codigo', array('label' => 'Pais','class' => 'input-medium pais', 'empty' => 'Pais', 'options' => $paises)) ?>
	<?php echo $this->BForm->input('TEstaEstado.esta_codigo', array('label' => 'Estado','class' => 'input-small uf', 'empty' => 'Estado', 'options' => $estados)) ?>
	<?php echo $this->BForm->input('veic_cida_codigo_emplacamento', array('class' => 'cidade','label' => 'Cidade Emplacamento', 'empty' => 'Cidade', 'options' => $cidades)) ?>
	<?php echo $this->BForm->input('veic_status', array('label' => 'Status','class' => 'input-small', 'empty' => false, 'options' => $status)) ?>

	<?php if($cliente_transportador): ?>
		<?php echo $this->BForm->hidden('TVtraVeiculoTransportador.vtra_tran_pess_oras_codigo') ?>
		<?php echo $this->BForm->input('TVtraVeiculoTransportador.vtra_tip_cliente', array('label' => 'Tipo de Veículo do Cliente','class' => 'input-medium', 'type' => 'text')) ?>
		<?php echo $this->Buonny->input_referencia($this, '#ClienteCodigo', 'TVtraVeiculoTransportador', 'vtra_tran_pess_oras_codigo', FALSE, 'Alvo Origem', TRUE) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('TVtraVeiculoTransportador.vtra_tvco_codigo', array('id'=>'TvcoCodigo','type' => 'radio','options' => array(1 => 'Integrante da frota', 3 => 'De terceiro (Agregado)'),'div' => FALSE, 'legend' => FALSE,'label' => array('class' => 'radio inline'))) ?>
	<?php else: ?>
		<?php echo $this->BForm->hidden('TVembVeiculoEmbarcador.vemb_emba_pjur_pess_oras_codigo') ?>
		<?php echo $this->BForm->input('TVembVeiculoEmbarcador.vemb_tip_cliente', array('label' => 'Tipo de Veículo do Cliente','class' => 'input-medium', 'type' => 'text', 'maxlength' => 10)) ?>
		<?php echo $this->Buonny->input_referencia($this, '#ClienteCodigo', 'TVembVeiculoEmbarcador', 'vemb_refe_codigo_origem', FALSE, 'Alvo Origem', TRUE) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('TVembVeiculoEmbarcador.vemb_tvco_codigo', array('id'=>'TvcoCodigo','type' => 'radio','options' => array(1 => 'Integrante da frota', 3 => 'De terceiro (Agregado)'),'div' => FALSE, 'legend' => FALSE,'label' => array('class' => 'radio inline'))) ?>
		</div>
		<div class='row-fluid inline'>
	<?php endif; ?>
</div>
<div id='caminhao' >
	<div class='row-fluid inline'  style='<?php echo ($this->data['TVeicVeiculo']['veic_tvei_codigo'] == 1)?'display:none':NULL ?>' >
		<?php echo $this->BForm->input('veic_telefone', array('label' => 'Telefone', 'class' => 'input-small telefone','type' => 'text')) ?>
		<?php echo $this->BForm->input('veic_radio', array('label' => 'Radio', 'class' => 'input-small','type' => 'text','maxlength' => 15)) ?>
		<?php echo $this->BForm->input('Veiculo.codigo_cliente_transportador_default', array('label' => 'Transportador Padrão','class' => 'input-xlarge', 'empty' => 'Selecione um Transportador', 'options' => $transportadoras)) ?>
		<?php echo $this->BForm->input('Veiculo.motorista', array('label' => 'Motorista Padrão','class' => 'input-small formata-cpf','type' => 'text')) ?>
		<?php echo $this->BForm->hidden('Veiculo.codigo_motorista_default') ?>
		<?php echo $this->BForm->input('Veiculo.nome_motorista', array('label' => 'Nome Motorista','class' => 'input-large','type' => 'text','readonly' => true)) ?>
	</div>

	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('TTecnTecnologia.tecn_codigo', array('label' => 'Tecnologia', 'empty' => 'Tecnologia', 'options' => $tecnologias)) ?>
		<?php echo $this->BForm->input('TVtecVersaoTecnologia.vtec_codigo', array('label' => 'Versão', 'empty' => 'Versão da Tecnologia', 'options' => $versoes)) ?>
		<?php echo $this->BForm->input('TTermTerminal.term_numero_terminal', array('label' => 'Numero', 'class' => 'input-medium','maxlength' => 15)) ?>
		<label>&nbsp;</label>
		<?php echo $this->BForm->input('TTermTerminal.term_sem_conta_ade',array('type' => 'checkbox', 'label' => 'Sem Conta ADE', 'class' => 'sem_conta_ade')); ?>
	</div>
	<h5>Dados do proprietário</h5>
	<label>Cliente é proprietário do veículo ?</label>
	<?php echo $this->BForm->input('proprietario', array('type' => 'radio','options' => array(1 => 'Sim', 2 => 'Não'),'div' => FALSE, 'legend' => FALSE,'label' => array('class' => 'radio inline proprietario'))) ?>
	<div class="proprietario_veiculo">	
		<?= $this->element('/veiculos/proprietario_veiculo') ?>
	</div>
</div>

<div id="dialog-confirm" title="Cobrança mensal" style="display:none">
	<h5><i>Prezado Cliente,</i></h5>
	<p style="font-size:12px;text-align:justify;">
		O veículo <strong>frota</strong> terá cobrança mensal independente da quantidade de solicitações envidas e será tarifado a partir do momento da inclusão e no valor  acordado na contratação do serviço de monitoramento.
	</p>
	<p style="font-size:12px;text-align:justify;">
		Gostaria de confirmar a inclusão deste veículo nesta modalidade de cobrança? <strong>Sim</strong> ou <strong>Não</strong>.<br />
	</p>
	<p style="font-size:12px;text-align:justify;">
		Caso opte por não, informar ao cliente que ele será tarifado por solicitação de monitoramento.<br />
	</p>
	<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
		<div class="ui-dialog-buttonset">
			<?php echo $this->BForm->button('Sim', array('div' => false, 'class' => 'btn btn-success confirmar')); ?>
			<?php echo $html->link('Não', 'javascript:void(0)', array('class' => 'btn cancelar')) ;?>
		</div>
	</div>
</div>
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?php echo $html->link('Voltar', 'adicionar_veiculo', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(function(){
		var cobranca = 0;

		setup_mascaras();

		busca_profissional("#VeiculoMotorista","#VeiculoNomeMotorista","#VeiculoCodigoMotoristaDefault");


		$("#TPaisPaisPaisCodigo").change(function(){
			buscar_t_estado("#TPaisPaisPaisCodigo", "#TEstaEstadoEstaCodigo");
		});


		$("#TMvecModeloVeiculoMvecMveiCodigo").change(function(){
			buscar_t_modelo("#TMvecModeloVeiculoMvecMveiCodigo", "#TVeicVeiculoVeicMvecCodigo");
		});

		$("#TTecnTecnologiaTecnCodigo").change(function(){
			verificaTecnologia();
			buscar_t_versao("#TTecnTecnologiaTecnCodigo", "#TVtecVersaoTecnologiaVtecCodigo");
		});

		function verificaTecnologia(){
			if($("#TTecnTecnologiaTecnCodigo").val() == 8){
				$(".sem_conta_ade").parent().show();
			}else{
				$(".sem_conta_ade").parent().hide();
			}
		}

		verificaTecnologia();

		$("#TEstaEstadoEstaCodigo").change(function(){
			buscar_t_cidade("#TEstaEstadoEstaCodigo", "#TVeicVeiculoVeicCidaCodigoEmplacamento");
		});

		$("#TVeicVeiculoVeicTveiCodigo").change(function(){
			showContent();
		});

		showContent();

		function showContent(){
			if($("#TVeicVeiculoVeicTveiCodigo option:selected").val() == 1){
				$("#caminhao").hide();
				$("#caminhao input").val("");
			} else {
				$("#caminhao").show();
			}
		}

		$(document).on("click","#dialog-confirm .cancelar",function(){
			$( "#dialog-confirm" ).dialog( "close" );
			return false;
		});

		$(document).on("click","#dialog-confirm .confirmar",function(){
			cobranca = 1;
			$("form").submit();
		});

		$(document).on("submit","form#TVeicVeiculoIncluir2Form", function(){
			var tvco = $("#TvcoCodigo1");

			if(tvco.is(":checked") && !cobranca){
				$("html, body").animate({ scrollTop: 0 });
				$( "#dialog-confirm" ).dialog({
					resizable: false,
					height:310,
					width:500
				});

				return false;
			}

		});

	});', false);
?>