<?php echo $this->BForm->create('TVcenViagemChecklistEntrada', array('action' => 'post', 'url' => array('controller' => 'Viagens','action' => 'checklist_entrada',$cliente['Cliente']['codigo'],$filtros['placa'],$filtros['checklist_dias_validos'])));?>

<?php echo $this->element('viagens/cliente') ?>

<?php echo $this->element('viagens/checklist_entrada') ?>

	<br>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')) ?>
	<?php echo $html->link('Voltar', array('controller' =>'Viagens', 'action' => 'inicio_viagem'), array('class' => 'btn')) ;?>
<?php echo $this->BForm->end() ?>

<?php echo $this->Javascript->codeBlock('

	function consulta_placa(tipo) {
		if (tipo=="carreta") {
			var campos = {
				codigo: $("#TVcenViagemChecklistEntradaVcenCarrVeicOrasCodigo"),
				placa: $("#TVeicVeiculoCarretaVeicPlaca"),
				cidade: $("#TCidaCidadeCarretaCidaDescricao"),
				uf: $("#TEstaEstadoCarretaEstaSigla"),
				modelo: $("#TMvecModeloVeiculoCarretaMvecDescricao"),
				ano: $("#TVeicVeiculoCarretaVeicAnoFabricacao"),
				cor: $("#TVeicVeiculoCarretaVeicCor"),
				tecnologia: $("#TTecnTecnologiaCarretaTecnDescricao"),
			};
		} else {
			var campos = {
				codigo: $("#TVcenViagemChecklistEntradaVcenVeicOrasCodigo"),
				placa: $("#TVeicVeiculoVeicPlaca"),
				cidade: $("#TCidaCidadeCidaDescricao"),
				uf: $("#TEstaEstadoEstaSigla"),
				modelo: $("#TMvecModeloVeiculoMvecDescricao"),
				ano: $("#TVeicVeiculoVeicAnoFabricacao"),
				cor: $("#TVeicVeiculoVeicCor"),
				tecnologia: $("#TTecnTecnologiaTecnDescricao"),
			};

		}
	    var placa = campos.placa.val();
	    if (placa.length > 0 && placa.indexOf(\'_\') < 0) {
    	    $.ajax({
    	        url: baseUrl + \'veiculos/dados_por_placa/placa:\' + placa + \'/\' + Math.random(),
    	        dataType: \'json\',
    	        beforeSend: function() {
					for (idx in campos) {
    	        		if (idx!="codigo" && idx!="placa") {
    	        			campos[idx].val("Aguarde...");
    	        		}
    	        	}
    	        },
    	        success: function(data) {
    	            if (data){
    	            	campos.codigo.val(data.TVeicVeiculo.veic_oras_codigo);
    	            	campos.cidade.val(data.TCidaCidade.cida_descricao);
    	            	campos.uf.val(data.TEstaEstado.esta_sigla);
    	            	campos.modelo.val(data.TMvecModeloVeiculo.mvec_descricao);
    	            	campos.ano.val(data.TVeicVeiculo.veic_ano_fabricacao);
    	            	campos.cor.val(data.TVeicVeiculo.veic_cor);
    	            	campos.tecnologia.val(data.TTecnTecnologia.tecn_descricao);
        	        } else {
						for (idx in campos) {
	    	        		if (idx!="codigo" && idx!="placa") {
	    	        			campos[idx];
	    	        			campos[idx].val("");
	    	        		}
	    	        	}
	    	        	campos.cidade.val("Não encontrado!");
        	        }
                },
                error: function() {
					for (idx in campos) {
    	        		if (idx!="codigo" && idx!="placa") {
    	        			campos[idx].val("");
    	        		}
    	        	}
    	        	campos.cidade.val("Não encontrado!");
                }
    	    });
    	}	    
	}

	function consulta_motorista() {
			var nome 		= $("#TPessPessoaPessNome");
			var cpf 		= $("#TPfisPessoaFisicaPfisCpf");
			var motorista 	= $("#TVcenViagemChecklistEntradaVcenMotoPfisPessOrasCodigo");
			var rg 			= $("#ProfissionalRg");
			var cnh 		= $("#ProfissionalCnh");

			var posicao_teleconsult = $("#TMotoMotoristaPosicaoTeleconsult");

			var codigo_cliente 	= $("#TVcenViagemChecklistEntradaCodigoCliente");
			var placa 			= $("#TVeicVeiculoVeicPlaca");
			var placa_carreta 	= $("#TVeicVeiculoCarretaVeicPlaca");

			nome.val(null);
			motorista.val(null);

			if(cpf.val()){
				$(".error-message").remove();
				$("#TPfisPessoaFisicaPfisCpf").removeClass("form-error");
				$("#TPfisPessoaFisicaPfisCpf").parents().find(".error").removeClass("error");

				$.ajax({
					url: baseUrl + "Profissionais/carregar_guardian_por_cpf/" + cpf.val() + "/S/S/" + Math.random(),
					dataType: "json",
					beforeSend: function(){
						nome.val("Aguarde...");
						$("#ProfissionalContatoTelefone, #ProfissionalContatoTelefoneAtual").val("...");

					},					
					success: function(data){
						if(data){
							//console.log(data);
							nome.val(data.TPessPessoa.pess_nome);
							motorista.val(data.TMotoMotorista.moto_pfis_pess_oras_codigo);
							rg.val(data.Profissional.rg);
							cnh.val(data.Profissional.cnh);
							
							if(data.ProfissionalCelular)
								$("#ProfissionalContatoTelefone, #ProfissionalContatoTelefoneAtual").val(data.ProfissionalCelular);
							else {
								$("#ProfissionalContatoTelefone, #ProfissionalContatoTelefoneAtual").val("");
							}
						} else {
							nome.val("NÃO LOCALIZADO");
							$("#ProfissionalContatoTelefone, #ProfissionalContatoTelefoneAtual").val("");
						}
					},
				});

				//if (codigo_cliente.val() && placa.val()) {
					$.ajax({
						url: baseUrl + "Profissionais/carregar_ultimo_status_teleconsult/cpf:" + cpf.val()  + "/" + Math.random(),
						dataType: "json",
						beforeSend: function(){
							posicao_teleconsult.val("Aguarde...");
						},					
						success: function(data){
							if(data){
								//console.log(data);
								posicao_teleconsult.val(data.Status.descricao);
							} else {
								posicao_teleconsult.val("NÃO LOCALIZADO");
							}
						},
					});
				//}
			}
	}


	function testaObrigatoriedadeJustificativa() {
		var objJustificativa = $("#TVcenViagemChecklistEntradaVcenJustificativa");

		var aprovado_calculado = $("#TVcenViagemChecklistEntradaVcenAprovadoEsperado").val();
		var aprovado_selecionado = $("#TVcenViagemChecklistEntradaVcenAprovado").val();

		objJustificativa.prop("required",(aprovado_calculado=="N" && aprovado_selecionado=="S" ? true : false));
	}

	$(document).ready(function(){
		setup_mascaras();

		$("#TPfisPessoaFisicaPfisCpf").blur(function(){
			consulta_motorista();
		});

		$(".item_checklist").change(function(){
			var posicao_checklist = $("#TCveiChecklistVeiculoPosicaoChecklist").val();
			var aprovado = true;

			var objAprovadoCalculado = $("#TVcenViagemChecklistEntradaVcenAprovadoEsperado");

			if (posicao_checklist!="1") aprovado = false;

			if (aprovado) {
				$(".item_checklist").each(function(){
	                if($(this).is(":checked")){
	                    aprovado = aprovado && ($(this).val()=="1");
	                }
	            });
			}

			objAprovadoCalculado.val((aprovado ? "S" : "N"));

			testaObrigatoriedadeJustificativa();

		});

		$(".radio_aprovado").change(function(){
			var objAprovadoSelecionado = $("#TVcenViagemChecklistEntradaVcenAprovado");

			objAprovadoSelecionado.val($(this).val());

			testaObrigatoriedadeJustificativa();
		});


		
	});', false);
?>