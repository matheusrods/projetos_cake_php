<div class='row-fluid inline'>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo']; ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
	</div>
</div>

<?php echo $this->BForm->create('TVeicVeiculo', array('type' => 'POST', 'url' => array('controller' => 'pesquisas_veiculos','action' => 'alterar', $this->data['Cliente']['codigo'],$this->data['TVeicVeiculo']['veic_placa'],$this->data['PesquisaVeiculo']['codigo'])));?>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('veic_placa',array('label' => 'Placa','class' => 'input-small', 'disabled' => TRUE)) ?>
	<?php echo $this->BForm->input('TTveiTipoVeiculo.tvei_descricao', array('label' => 'Tipo de Veiculo', 'class' => 'input-small cam-carr', 'disabled' => TRUE, 'data-id' => $this->data['TTveiTipoVeiculo']['tvei_codigo'])) ?>
	<?php echo $this->BForm->input('TMveiMarcaVeiculo.mvei_descricao', array('label' => 'Fabricante','class' => 'input-large fabricante', 'disabled' => TRUE)) ?>
	<?php echo $this->BForm->input('TMvecModeloVeiculo.mvec_descricao', array('label' => 'Modelo','class' => 'input-large modelo', 'disabled' => TRUE)) ?>
	<?php echo $this->BForm->input('VeiculoCor.descricao', array('label' => 'Cor','class' => 'input-medium' ,'disabled' => TRUE)) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('veic_ano_fabricacao', array('label' => 'Ano Fabricacao','class' => 'input-medium just-number','maxlength' => 4, 'disabled' => TRUE)) ?>
	<?php echo $this->BForm->input('veic_ano_modelo', array('label' => 'Ano Modelo', 'class' => 'input-medium just-number','maxlength' => 4,'disabled' => TRUE)) ?>
	<?php echo $this->BForm->input('veic_chassi', array('label' => 'Chassi', 'type' => 'text', 'maxlength' => 49, 'class' => 'input-small', 'disabled' => TRUE)) ?>
	<?php echo $this->BForm->input('veic_renavam', array('label' => 'Renavam', 'type' => 'text', 'class' => 'input-medium','maxlength' => 49,'disabled' => TRUE)) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('TEstaEstado.esta_sigla', array('label' => 'Estado','class' => 'input-small uf', 'disabled' => TRUE)) ?>
	<?php echo $this->BForm->input('TCidaCidade.cida_descricao', array('class' => 'cidade','label' => 'Cidade Emplacamento', 'disabled' => TRUE)) ?>
	<?php echo $this->BForm->input('veic_status', array('class' => 'input-small', 'label' => 'Status', 'disabled' => TRUE)) ?>

	<?php 

	if(isset($cliente_transportador) && $cliente_transportador==true): ?>
		<?php echo $this->BForm->hidden('TVtraVeiculoTransportador.vtra_tran_pess_oras_codigo') ?>
		<?php echo $this->BForm->input('TVtraVeiculoTransportador.vtra_tip_cliente', array('label' => 'Tipo de Veículo do Cliente','class' => 'input-medium', 'type' => 'text', 'maxlength'=>10, 'disabled' => TRUE)) ?>
		<?php echo $this->BForm->input('TVembVeiculoTransportador.vtra_refe_codigo_origem_visual', array('label' => 'Alvo Origem','class' => 'input-xlarge', 'type' => 'text', 'disabled' => TRUE)) ?>
		<?php if($exibe_cobranca || $isOperacional): ?>

		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('TVtraVeiculoTransportador.vtra_tvco_codigo', array('id'=>'TvcoCodigo','type' => 'radio','disabled' => TRUE,'options' => array(1 => 'Integrante da frota', 3 => 'De terceiro (Agregado)'),'div' => FALSE, 'legend' => FALSE,'label' => array('class' => 'radio inline'), 'disabled' => TRUE)) ?>
		<?php else: ?>
			<?php echo $this->BForm->hidden('TVtraVeiculoTransportador.vtra_tvco_codigo') ?>
		<?php endif; ?>

	<?php else: ?>
		<?php echo $this->BForm->hidden('TVembVeiculoEmbarcador.vemb_emba_pjur_pess_oras_codigo') ?>
		<?php echo $this->BForm->input('TVembVeiculoEmbarcador.vemb_tip_cliente', array('label' => 'Tipo de Veículo do Cliente','class' => 'input-medium', 'type' => 'text','disabled' => TRUE)) ?>
		<?php echo $this->BForm->input('TVembVeiculoEmbarcador.vemb_refe_codigo_origem_visual', array('label' => 'Alvo Origem','class' => 'input-xlarge', 'type' => 'text', 'disabled' => TRUE)) ?>

		<?php if($exibe_cobranca || $isOperacional): ?>

		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('TVembVeiculoEmbarcador.vemb_tvco_codigo', array('id'=>'TvcoCodigo','type' => 'radio','disabled' => TRUE,'options' => array(1 => 'Integrante da frota', 3 => 'De terceiro (Agregado)'),'div' => FALSE, 'legend' => FALSE,'label' => array('class' => 'radio inline') )) ?>
		</div>
		<div class='row-fluid inline'>
		
		<?php else: ?>
			<?php echo $this->BForm->hidden('TVembVeiculoEmbarcador.vemb_tvco_codigo') ?>
		<?php endif; ?>
	<?php endif; ?>
</div>
<div class='row-fluid inline'>
<div id='caminhao' >
	<div class='row-fluid inline' style='<?php echo ($this->data['TVeicVeiculo']['veic_tvei_codigo'] == 1)?'display:none':NULL ?>' >
		<?php echo $this->BForm->input('veic_telefone', array('label' => 'Telefone', 'class' => 'input-small telefone','type' => 'text','disabled' => TRUE)) ?>
		<?php echo $this->BForm->input('veic_radio', array('label' => 'Radio', 'class' => 'input-small','type' => 'text','maxlength' => 15,'disabled' => TRUE)) ?>

		<?php echo $this->BForm->input('PesquisaVeiculo.transportador_default', array('label' => 'Transportador Padrão','class' => 'input-xlarge', 'disabled' => TRUE)) ?>
		<?php echo $this->BForm->input('Veiculo.motorista', array('label' => 'Motorista Padrão','class' => 'input-small formata-cpf','type' => 'text', 'disabled' => TRUE)) ?>
		<?php echo $this->BForm->hidden('Veiculo.codigo_motorista_default') ?>
		<?php echo $this->BForm->input('Veiculo.nome_motorista', array('label' => 'Nome Motorista','class' => 'input-large','type' => 'text','disabled' => TRUE)) ?>
	</div>

	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('TTecnTecnologia.tecn_descricao', array('label' => 'Tecnologia', 'disabled' => TRUE, 'data-id' => $this->data['TTecnTecnologia']['tecn_codigo'])) ?>
		<?php echo $this->BForm->input('TVtecVersaoTecnologia.vtec_versao', array('label' => 'Versão','class' => 'tecnologia','disabled' => TRUE)) ?>
		<?php echo $this->BForm->input('TTermTerminal.term_numero_terminal', array('label' => 'Numero', 'class' => 'input-medium tecnologia','maxlength' => 15,'disabled' => TRUE)) ?>
		<label>&nbsp;</label>
		<?php echo $this->BForm->input('TTermTerminal.term_sem_conta_ade',array('type' => 'checkbox', 'label' => 'Sem Conta ADE', 'class' => 'sem_conta_ade','disabled' => TRUE)); ?>
	</div>
	<h5>Dados do proprietário</h5>
	<?php if(empty($this->data['Proprietario']['codigo_documento'])):?>
		<label>Cliente é proprietário do veículo ?</label>
		<?php echo $this->BForm->input('proprietario', array('type' => 'radio', 'disabled'=>TRUE,'options' => array(1 => 'Sim', 2 => 'Não'),'div' => FALSE, 'legend' => FALSE,'label' => array('class' => 'radio inline proprietario', 'disabled' => TRUE))) ?>
	<?php endif;?>
	<div class="proprietario_veiculo">	
		<div class="row-fluid inline">
		    <?php echo $this->BForm->input('Proprietario.codigo_documento', array('label' => 'CPF/CNPJ', 'class' => 'enderecoProprietario input-medium', 'disabled' => TRUE)); ?>
		    <?php echo $this->BForm->input('Proprietario.nome_razao_social', array('class' => 'enderecoProprietario input-xlarge','type'=>'text', 'label' => 'Nome/Razão Social', 'disabled' => TRUE)); ?>
		    <?php echo $this->BForm->input('Proprietario.inscricao_estadual',array('class' => 'enderecoProprietario input-medium','type'=>'text', 'label' => 'RG/Inscrição Estadual', 'disabled' => TRUE)); ?>
		    <?php echo $this->BForm->input('Proprietario.rntrc',array('class' => 'enderecoProprietario input-medium','type'=>'text', 'label' => 'RNTRC', 'disabled' => TRUE)); ?>
		</div>
		<h5>Endereço do proprietário</h5>
		<div class="row-fluid inline">
		    <?php echo $this->BForm->input('VEndereco.endereco_cep', array('label' => 'CEP', 'class' => 'input-medium', 'disabled' => TRUE)); ?>
		    <?php 
		    $endereco = $this->data['VEndereco']['endereco_logradouro'].' - '.$this->data['VEndereco']['endereco_bairro'].' - '.$this->data['VEndereco']['endereco_cidade'].' - '.$this->data['VEndereco']['endereco_estado_abreviacao'].' - '.$this->data['VEndereco']['endereco_cidade_ibge'];
		    echo $this->BForm->input('VEndereco.endereco_logradouro', array('label' => 'Endereço', 'class' => 'input-xxlarge', 'disabled' => TRUE, 'value' => $endereco)); ?>
		    <?php echo $this->BForm->input('ProprietarioEndereco.numero', array('label' => 'Número', 'class' => 'input-small', 'disabled' => TRUE)); ?>
		    <?php echo $this->BForm->input('ProprietarioEndereco.complemento', array('label' => 'Complemento', 'class' => 'input-small', 'disabled' => TRUE)); ?>		    
		</div>		
	</div>
</div>
<div class='row-fluid inline'><br>
<?php echo $this->BForm->hidden('PesquisaVeiculo.codigo'); ?>
<?php echo $this->BForm->input('PesquisaVeiculo.codigo_status', array(
	'label'    => 'Status do Veículo', 
	'empty'    => 'Status', 
	'class'    => 'input-medium' , 
	'disabled' => $pesquisa ? false : true, 
	'options'  => array(
			3 => 'Aprovado',
			4 => 'Reprovado')
		)
	) ?>
</div>
<div class="form-actions">
	<?php 
		if($pesquisa)
			echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); 
	?>
	<?php echo $html->link('Voltar', $pesquisa ? 'listagem_pesquisa' : 'listagem_finaliza', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	$(function(){
		var cobranca = 0;

		setup_mascaras();
		busca_profissional("#VeiculoMotorista","#VeiculoNomeMotorista","#VeiculoCodigoMotoristaDefault");

		$("#TMvecModeloVeiculoMvecMveiCodigo").change(function(){
			buscar_t_modelo("#TMvecModeloVeiculoMvecMveiCodigo", "#TVeicVeiculoVeicMvecCodigo");
		});

		$("#TEstaEstadoEstaCodigo").change(function(){
			buscar_t_cidade("#TEstaEstadoEstaCodigo", "#TVeicVeiculoVeicCidaCodigoEmplacamento");
		});

		$("#TTecnTecnologiaTecnDescricao").change(function(){
			verificaTecnologia();
			buscar_t_versao("#TTecnTecnologiaTecnDescricao", "#TVtecVersaoTecnologiaVtecCodigo");
		});

		
		function verificaTecnologia(){

			if($("#TTecnTecnologiaTecnDescricao").data("id") == 8){
				$(".sem_conta_ade").parent().show();
			}else{				
				$(".sem_conta_ade").parent().hide();
			}
		}

		verificaTecnologia();

		$("#TVeicVeiculoVeicTveiCodigo").change(function(){
			showContent();
		});

		showContent();

		function showContent(){			
			if($("#TTveiTipoVeiculoTveiDescricao").data("id") == 1){
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

		$(document).on("submit","form#TVeicVeiculoAlterar2Form", function(){
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