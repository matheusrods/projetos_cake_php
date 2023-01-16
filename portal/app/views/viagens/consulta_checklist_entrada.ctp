<?php echo $this->BForm->create('TVcenViagemChecklistEntrada', array('action' => 'post', 'url' => array('controller' => 'Viagens','action' => 'checklist_entrada',$cliente['Cliente']['codigo'],$filtros['placa'],$filtros['checklist_dias_validos'])));?>

<?php echo $this->element('viagens/cliente') ?>

<?php echo $this->element('viagens/checklist_entrada') ?>

<br>
<?php echo $this->BForm->end() ?>

<?php echo $this->Javascript->codeBlock('

	function carregaPosicaoTeleconsult() {
		var posicao_teleconsult = $("#TMotoMotoristaPosicaoTeleconsult");

		var codigo_cliente 	= $("#TVcenViagemChecklistEntradaCodigoCliente");
		var placa 			= $("#TVeicVeiculoVeicPlaca");
		var placa_carreta 	= $("#TVeicVeiculoCarretaVeicPlaca");		
		
		var cpf 			= $("#TPfisPessoaFisicaPfisCpf");

		$.ajax({
			//url: baseUrl + "Profissionais/carregar_posicao_teleconsult/codigo_cliente:" + codigo_cliente.val() + "/cpf:" + cpf.val() + "/placa:" + placa.val() + "/placa_carreta:" + placa_carreta.val() + "/" + Math.random(),
			url: baseUrl + "Profissionais/carregar_ultimo_status_teleconsult/cpf:" + cpf.val()  + "/" + Math.random(),
			dataType: "json",
			beforeSend: function(){
				posicao_teleconsult.val("Aguarde...");
			},					
			success: function(data){
				if(data){
					posicao_teleconsult.val(data.Status.descricao);
				} else {
					posicao_teleconsult.val("NÃO LOCALIZADO");
				}
			},
		});		
	}

	$(document).ready(function(){
		setup_mascaras();
		carregaPosicaoTeleconsult();
		
	});', false);
?>