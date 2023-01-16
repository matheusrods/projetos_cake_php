<?php echo $this->BForm->create('TViagViagem', array('action' => 'post', 'autocomplete' => 'off', 'url' => array('controller' => 'Viagens','action' => 'checklist',$cliente['Cliente']['codigo'],$viag_codigo)));?>

<?php $codigo_viagem_sm = $viagem['TViagViagem']['viag_codigo_sm']; ?>
<?php echo $this->BForm->hidden('TVcheViagemChecklist.vche_cmat_codigo') ?>
<div class='row-fluid inline well'>
	<div id="cliente">
		<strong>Código: </strong><?php echo $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social'] ?>
		<strong>PLACA: </strong><?php echo $viagem['TVeicVeiculo']['veic_placa'] ?>&nbsp;&nbsp;
		<strong>Tipo: </strong><?php echo $viagem['TTveiTipoVeiculo']['tvei_descricao'] ?>&nbsp;&nbsp;
		<?php $data_upos = date('Y-m-d H:i:s',strtotime('-2 hour')); ?>
		<?php if($viagem && date('Y-m-d H:i:s',Comum::dateToTimestamp($viagem['TUposUltimaPosicao']['upos_data_comp_bordo'])) >= $data_upos): ?>
			<span class="badge-empty badge badge-success" title="Posicionamento Normal"></span>
		<?php else: ?>
			<span class="badge-empty badge badge-empty" title="Sem Posicionamento"></span>
		<?php endif; ?>
	</div>

</div>
<div class='row-fluid inline well'>
	<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'label' => 'SM', 'readonly' => true)) ?>
	<?php echo $this->BForm->input('loadplans', array('class' => 'input-xxlarge', 'label' => 'LoadPlans', 'readonly' => true)) ?>
	<?php echo $this->BForm->input('viag_valor_carga', array('class' => 'input-medium', 'label' => 'Valor total da Carga', 'readonly' => true)) ?>
	<?php echo $this->BForm->input('notasfiscais', array('class' => 'input-xxlarge', 'label' => 'Nota fiscal', 'readonly' => true)) ?>
	<?php echo $this->BForm->hidden('codigo_cliente',array('value'=>$cliente['Cliente']['codigo'])) ?>
	<?php echo $this->BForm->hidden('TVcheViagemChecklist.vche_data_inicio',array('value'=>date('Y-m-d H:i:s'))) ?>
	<?php if($readonly_referencia): ?>
		<?php echo $this->BForm->hidden('TVcheViagemChecklist.vche_refe_codigo') ?>
		<?php echo $this->BForm->input('TVcheViagemChecklist.vche_refe_codigo_visual',array('readonly'=>true,'label'=>'CD')) ?>
	<?php else: ?>
		<?php echo $this->Buonny->input_referencia($this, '#TViagViagemCodigoCliente', 'TVcheViagemChecklist', 'vche_refe_codigo', false, 'CD', true, true) ?>
	<?php endif; ?>
</div>

<h4>Fotos</h4>
	<div class='row-fluid well' id="divFotos">
		<?php echo $this->element('viagens/fields_fotos_checklist') ?>
	</div>

	<h4>Motorista</h4>
	<div class="well">
		<div class='row-fluid inline'>
			<?php echo $this->BForm->hidden('Cliente.codigo',array('value' => $cliente['Cliente']['codigo'])) ?>
			<?php echo $this->BForm->hidden('Cliente.codigo_documento',array('value' => $cliente['Cliente']['codigo_documento'])) ?>
			<?php echo $this->BForm->hidden('Cliente.iniciar_por_checklist',array('value' => $cliente['Cliente']['iniciar_por_checklist'])) ?>
			<?php echo $this->BForm->hidden('TVeicVeiculo.veic_placa') ?>
			<?php echo $this->BForm->hidden('TViagViagem.viag_codigo') ?>
			<?php echo $this->BForm->hidden('TViagViagem.viag_codigo_sm') ?>
			<?php echo $this->BForm->hidden('TVveiViagemVeiculo.vvei_codigo') ?>
			<?php echo $this->BForm->hidden('TVveiViagemVeiculo.vvei_moto_pfis_pess_oras_codigo') ?>
			<?php echo $this->BForm->input('TPfisPessoaFisica.pfis_cpf', array('class' => 'input-medium formata-cpf', 'label' => 'CPF')) ?>
			<?php echo $this->BForm->input('TPessPessoa.pess_nome', array('class' => 'input-xlarge', 'label' => 'Nome', 'readonly' => true)) ?>
		</div>
		<div class='row-fluid-inline'>
		<?php echo $this->BForm->input('ProfissionalContato.telefone', array('value' => (isset($profissional_celular) ? $profissional_celular : ''), 'class' => 'input-medium', 'label' => 'Celular')) ?>
		<?php echo $this->BForm->hidden('ProfissionalContato.telefone_atual', array('value' => (isset($profissional_celular) ? $profissional_celular : ''))) ?>
		</div>
	</div>
	<?php echo $this->BForm->submit('Enviar', array('div' => false, 'class' => 'btn btn-success')) ?>
	<?php echo $html->link('Voltar', array('controller' =>'Viagens', 'action' => 'inicio_viagem'), array('class' => 'btn')) ;?>
<?php echo $this->BForm->end() ?>

	

<?php echo $this->Javascript->codeBlock('
	function fecharMsg(){
	    setInterval(
	        function(){
	            $("div.message.container").css({ "opacity": "0", "display": "none" });
	        },
	        9000
	    );     
	}
	function gerarMensagem(css, mens){
	    $("div.message.container").css({ "opacity": "1", "display": "block" });
	    $("div.message.container").html("<div class=\"alert alert-"+css+"\"><p>"+mens+"</p></div>");
	    fecharMsg();
	}	
	function verifica_atraso() {
	    var codigo_cliente = $("#ClienteCodigo").val();
	    var placa = $("#TVeicVeiculoVeicPlaca").val();

        var retorno = $.ajax({
            url: baseUrl + \'viagens/verifica_atraso_checklist/codigo_cliente:\' + codigo_cliente + \'/placa:\' + placa + \'/\' + Math.random(),
            dataType: \'json\',
            async: false
        }).responseText;
		if (isNaN(retorno)) {
			gerarMensagem("error",retorno);
			return false;
		}

		if (retorno==1) {
	        var link = "/portal/viagens/seleciona_motivo_atraso/" + codigo_cliente +"/"+placa+"/" + Math.random();
	        open_dialog(link, "Selecione o Motivo do Atraso", 490);	
	        return false;

		}

		return true;
	}

	$(document).on("submit","#TViagViagemPostForm",function() {
		var valido = true;
		var motivo_atraso = $("#TVcheViagemChecklistVcheCmatCodigo").val();
		if (motivo_atraso=="") {
			return verifica_atraso();
		}

		return true;
	});


	$(document).ready(function(){

		var viag_codigo = '.$viag_codigo.';
		var viag_codigo_sm = '.$codigo_viagem_sm.';

		$(".uplodify").css("display","none");
		function excluir_foto(num_processo,ano_processo,codigo) {
		    if (confirm("Deseja realmente excluir?")){
		        if( codigo == undefined  )
		            location.href = "/portal/viagens/excluir_foto" + "/" + num_processo + "/" + ano_processo ;
		        else
		            location.href = "/portal/viagens/excluir_foto" + "/" + num_processo + "/" + ano_processo + "/" + codigo ;
		    }
		 }
		setup_mascaras();

		$("#TPfisPessoaFisicaPfisCpf").blur(function(){
			var nome 		= $("#TPessPessoaPessNome");
			var cpf 		= $("#TPfisPessoaFisicaPfisCpf");
			var motorista 	= $("#TVveiViagemVeiculoVveiMotoPfisPessOrasCodigo");

			nome.val(null);
			motorista.val(null);

			if(cpf.val()){
				$(".error-message").remove();
				$("#TPfisPessoaFisicaPfisCpf").removeClass("form-error");
				$("#TPfisPessoaFisicaPfisCpf").parents().find(".error").removeClass("error");

				$.ajax({
					url: baseUrl + "Profissionais/carregar_guardian_por_cpf/" + cpf.val() + "/" + Math.random(),
					dataType: "json",
					beforeSend: function(){
						nome.val("Aguarde...");
						$("#ProfissionalContatoTelefone, #ProfissionalContatoTelefoneAtual").val("...");

					},					
					success: function(data){
						if(data){
							nome.val(data.TPessPessoa.pess_nome);
							motorista.val(data.TMotoMotorista.moto_pfis_pess_oras_codigo);
							
							if(data.ProfissionalCelular)
								$("#ProfissionalContatoTelefone, #ProfissionalContatoTelefoneAtual").val(data.ProfissionalCelular);
								
						} else {
							nome.val("NÃO LOCALIZADO");
							$("#ProfissionalContatoTelefone, #ProfissionalContatoTelefoneAtual").val("");
						}
					},
				});
			}
		});
		
		
		$(document).on("click",".remove",function(){
			$(this).parents("tr:eq(0)").remove();
			return false;
		});

		
	});', false);
?>