function setup_codigo_cliente(){
	$("#FichaScorecardCodigoCliente").blur(function(){
		limpar_campos_profissional(); 
		codigo = $("#FichaScorecardCodigoCliente");
		if(codigo.val()){
			$.ajax({
				url: baseUrl + "clientes/buscar/" + codigo.val() + "/" + Math.random(),
    			cache: false,

				type: "post",
				dataType: "json",
				beforeSend: function(){
					codigo.addClass("ui-autocomplete-loading");
				},
				success: function(data){
					if(data.sucesso == true){
						$("#ClienteCodigoDocumento").val(data.dados.codigo_documento);
						$("#ClienteRazaoSocial").val(data.dados.razao_social);
					} else {
						$("#ClienteCodigoDocumento").val("");
						$("#ClienteRazaoSocial").val("");
					}
				},
				complete: function(){
					codigo.removeClass("ui-autocomplete-loading");
				}
			});			
			url_pro = baseUrl + "clientes_produtos/lista_produtos_tlcs/" + codigo.val() + "/" +  Math.random();
			$('#FichaScorecardCodigoProduto').get(url_pro);			
		} else {
			$("#ClienteCodigoDocumento").val("");
			$("#ClienteRazaoSocial").val("");
		}
	
		return false;
	});
}

 
function setup_cliente( element ){
	if( element.val() ){
		$.ajax({
			url: baseUrl + "clientes/buscar/" + element.val() + "/" + Math.random(),
			cache: false,
			type: "post",
			dataType: "json",
			beforeSend: function(){
				element.addClass("ui-autocomplete-loading");
			},
			success: function(data){
				if(data.sucesso == true){
					$("#ClienteCodigoDocumento").val(data.dados.codigo_documento);
					$("#ClienteRazaoSocial").val(data.dados.razao_social);
				} else {
					$("#ClienteCodigoDocumento").val("");
					$("#ClienteRazaoSocial").val("");
				}
			},
			complete: function(){
				element.removeClass("ui-autocomplete-loading");
			}
		});
	} else {
		$("#ClienteCodigoDocumento").val("");
		$("#ClienteRazaoSocial").val("");
	}	
	return false;
}

function desabilitar_campo(seletor){
	if($.trim($(seletor).val()) != "")
		$(seletor).attr("readonly", true);
	else
		$(seletor).removeAttr("readonly");
}

function desabilitar_campos_profissional() {

}
	
function limpar_campos_profissional(){
	$("#ProfissionalCodigo").val("");
	$("#ProfissionalNome").val("");
	$("#ProfissionalNomePai").val("");
	$("#ProfissionalNomeMae").val("");
	$("#ProfissionalDataInclusao").val("");
	$("#ProfissionalRg").val("");
	$("#ProfissionalCodigoEstadoRg").val("");
	$("#ProfissionalRgDataEmissao").val("");
	$("#ProfissionalDataNascimento").val("");
	$("#ProfissionalCnh").val("");
	$("#ProfissionalCodigoTipoCnh").val("");
	$("#ProfissionalCnhVencimento").val("");
	$("#ProfissionalCodigoEnderecoEstadoEmissaoCnh").val("");
	$("#ProfissionalPossuiMopp").val("");
	$("#ProfissionalDataInicioMopp").val("");
	$("#ProfissionalDataPrimeiraCnh").val("");
	$('#ProfissionalCodigoSegurancaCnh').val("");
	$('#ProfissionalCodigoSegurancaCnh').val("");
	$('#ProfissionalCodigoEstadoNaturalidade').val("");
	$('#ProfissionalCodigoCidadeNaturalidade').val("");
	$("#ProfissionalCodigoEstadoNaturalidade").change();
	$("#ProfissionalEnderecoEnderecoCep").val("");
	$("#ProfissionalEnderecoNumero").val("");
	$("#ProfissionalEnderecoComplemento").val("");
	$("#FichaScorecardDataInclusao").val("");
	$("#FichaScorecardObservacao").val("");
}

function preenche_campos_profissional(data){	
	$("#ProfissionalCodigo").val(data.ProfissionalLog.codigo_profissional);
	$("#ProfissionalNome").val(data.ProfissionalLog.nome);
	$("#ProfissionalNomePai").val(data.ProfissionalLog.nome_pai);
	$("#ProfissionalNomeMae").val(data.ProfissionalLog.nome_mae);
	$("#ProfissionalDataInclusao").val(data.Profissional.data_inclusao.substring(0,10));
	$("#ProfissionalRg").val(data.ProfissionalLog.rg);
	$("#ProfissionalCodigoEstadoRg").val(data.ProfissionalLog.codigo_estado_rg);
	if(data.ProfissionalLog.rg_data_emissao !== "" && data.ProfissionalLog.rg_data_emissao !== null)
		$("#ProfissionalRgDataEmissao").val(data.ProfissionalLog.rg_data_emissao.substring(0,10));
	if(data.ProfissionalLog.data_nascimento !== "" && data.ProfissionalLog.data_nascimento !== null)
		$("#ProfissionalDataNascimento").val(data.ProfissionalLog.data_nascimento.substring(0,10));
	$("#ProfissionalCnh").val(data.ProfissionalLog.cnh);
	$("#ProfissionalCodigoTipoCnh").val(data.ProfissionalLog.codigo_tipo_cnh);
	if(data.ProfissionalLog.cnh_vencimento !== "" && data.ProfissionalLog.cnh_vencimento !== null)
		$("#ProfissionalCnhVencimento").val(data.ProfissionalLog.cnh_vencimento.substring(0,10));
	$("#ProfissionalCodigoEnderecoEstadoEmissaoCnh").val(data.ProfissionalLog.codigo_endereco_estado_emissao_cnh);
	if(data.ProfissionalLog.data_primeira_cnh !== "" && data.ProfissionalLog.data_primeira_cnh !== null)
		$("#ProfissionalDataPrimeiraCnh").val(data.ProfissionalLog.data_primeira_cnh.substring(0,10));
	$('#ProfissionalCodigoSegurancaCnh').val(data.ProfissionalLog.codigo_seguranca_cnh);
	$('#ProfissionalPossuiMopp').val(data.ProfissionalLog.possui_mopp);
	if( data.ProfissionalLog.possui_mopp ){		
		$('#ProfissionalDataInicioMopp').val(data.ProfissionalLog.data_inicio_mopp.substring(0,10));
		$('#divMopp').show();
	}

	$('#ProfissionalCodigoEstadoNaturalidade').val(data.ProfissionalLog.codigo_endereco_estado_naturalidade);
	$('#codigo_cidade').val(data.ProfissionalLog.codigo_endereco_cidade_naturalidade);
	$('#codigo_estado').val(data.ProfissionalLog.codigo_endereco_estado_naturalidade);
	$('#ProfissionalCodigoEnderecoCidadeNaturalidade').val(data.ProfissionalLog.codigo_endereco_cidade_naturalidade);
	buscar_nome_cidade(data.ProfissionalLog.codigo_endereco_cidade_naturalidade);	
	if(typeof(data.ProfissionalEnderecoLog) !== 'undefined' && data.ProfissionalEnderecoLog !== null){
		if(data.ProfissionalEnderecoLog.cep !== undefined){
			cep = data.ProfissionalEnderecoLog.cep;
		} else{
			cep = data.ProfissionalEnderecoLog.endereco_cep;
		}
		$("#ProfissionalEnderecoEnderecoCep").val(cep);
		buscar_cep($("#ProfissionalEnderecoEnderecoCep"), "#ProfissionalEnderecoCodigoEndereco", data.ProfissionalEnderecoLog.codigo_endereco );
		$("#ProfissionalEnderecoNumero").val(data.ProfissionalEnderecoLog.numero);
		$("#ProfissionalEnderecoComplemento").val(data.ProfissionalEnderecoLog.complemento);
	} else {
		$("#ProfissionalEnderecoEnderecoCep").val("");
		$("#ProfissionalEnderecoNumero").val("");
		$("#ProfissionalEnderecoComplemento").val("");setup_sinalizar_criterios_insuficientes;
	}
	carrega_contatos_profissional( data.ProfissionalLog.codigo_profissional, data.FichaScorecard.codigo );
	desabilita_campos_profissional(data);
}


function carrega_contatos_profissional( codigo_profissional ) {		
	var div = $('#lista-contatos-profissional');
	div.html('');
	div.load(baseUrl + "fichas_scorecard/carregar_profissional_contatos/" + codigo_profissional + "/" + Math.random());
}

function carrega_contatos_proprietario( codigo_ficha, key ) {
	var div = $("#lista-contatos-proprietario"+key+"");	            
	div.html('');
	div.load(baseUrl + "fichas_scorecard/carregar_proprietario_contatos/" + codigo_ficha + "/" + key + "/" + Math.random());
}


function desabilita_campos_profissional(data){	
	$("#campos_profissional").each(function(){
		var name = $(this).attr('id');
	});
	if( data.ProfissionalLog.nome )
		desabilitar_campo( $("#ProfissionalNome") );
	if( data.ProfissionalLog.nome_pai )
		desabilitar_campo( $("#ProfissionalNomePai") );
	if( data.ProfissionalLog.nome_mae )
		desabilitar_campo( $("#ProfissionalNomeMae") );	
	if( data.ProfissionalLog.rg )
		desabilitar_campo( $("#ProfissionalRg") );
	if( data.ProfissionalLog.data_nascimento ){
		desabilitar_campo( $("#ProfissionalDataNascimento") );
		$("#ProfissionalDataNascimento").parent().find('.ui-datepicker-trigger').hide();
	}
	if( data.ProfissionalLog.codigo_endereco_cidade_naturalidade ){
		desabilitar_campo( $("#ProfissionalCidadeNaturalidadeProfissional") );		
	}
	if( data.ProfissionalLog.cnh ){
		desabilitar_campo( $("#ProfissionalCnh") );		
	}
}

function preenche_campos_proprietario(data){	
	if(typeof(data.ProprietarioLog) !== 'undefined' && data.ProprietarioLog !== null){
		$("#FichaScorecardVeiculo0ProprietarioCodigoDocumento").val(data.ProprietarioLog.codigo_documento);
		$("#FichaScorecardVeiculo0ProprietarioNomeRazaoSocial").val(data.ProprietarioLog.nome_razao_social);
		$("#FichaScorecardVeiculo0ProprietarioRg").val(data.ProprietarioLog.rg);
		$("#FichaScorecardVeiculo0ProprietarioRntrc").val(data.ProprietarioLog.rntrc);
	}
	if(typeof(data.ProprietarioEnderecoLog) !== 'undefined' && data.ProprietarioEnderecoLog !== null){
		$("#FichaScorecardVeiculo0ProprietarioEnderecoEnderecoCep").val(data.ProprietarioEnderecoLog.cep);
		buscar_cep($("#FichaScorecardVeiculo0ProprietarioEnderecoEnderecoCep"), 
			"#FichaScorecardVeiculo0ProprietarioEnderecoCodigoEndereco", 
			data.ProprietarioEnderecoLog.codigo_endereco);
		$("#FichaScorecardVeiculo0ProprietarioEnderecoNumero").val(data.ProprietarioEnderecoLog.numero);
		$("#FichaScorecardVeiculo0ProprietarioEnderecoComplemento").val(data.ProprietarioEnderecoLog.complemento);
	}else{
		$("#FichaScorecardVeiculo0ProprietarioEnderecoEnderecoCep").val("");
		$("#FichaScorecardVeiculo0ProprietarioEnderecoNumero").val("");
		$("#FichaScorecardVeiculo0ProprietarioEnderecoComplemento").val("");
	}

}

function preenche_campos_veiculos(data){
	if(typeof(data.FichaScorecardVeiculo) !== 'undefined' && data.FichaScorecardVeiculo !== null){
		veiculos_content = $('.veiculo-content');		
		for(i = 0; i <= 2; i++){
			if(typeof(data.FichaScorecardVeiculo[i]) == 'undefined'){
				continue;
			}
			veiculo = data.FichaScorecardVeiculo[i].VeiculoLog;			
			if(i==0 && veiculo !== null){
				$('#FichaScorecardVeiculoPossuiVeiculoS').prop('checked', true);
			}
			if(veiculo !== null){ 
				$("#FichaScorecardVeiculo"+i+"VeiculoVeiculoSnS").prop('checked', true);
			}
			veiculo_content = $(veiculos_content[i]);
			veiculo_content.find('#codigo_cidade').val(veiculo.codigo_estado_emplacamento);
	        veiculo_content.find('#codigo_estado').val(veiculo.codigo_cidade_emplacamento);
			veiculo_content.find('#codigo_veiculo').val(veiculo.codigo_veiculo);
			buscar_nome_cidade_veiculo(veiculo.codigo_cidade_emplacamento,i);
			
			veiculo_content.find('.tecnologia').val(veiculo.codigo_veiculo_tecnologia);
			veiculo_content.find('.placaveiculo').val(veiculo.placa);
			veiculo_content.find('.placa-veiculo').val(veiculo.placa);			
			veiculo_content.find('.placa-veiculo').mask("aaa-999?9"); 
			veiculo_content.find('.chassi').val(veiculo.chassi);
			veiculo_content.find('.renavam').val(veiculo.renavam);
			veiculo_content.find('.cor').val(veiculo.codigo_veiculo_cor);
			veiculo_content.find('.ano-fabricacao').val(veiculo.ano_fabricacao);
			veiculo_content.find('.ano').val(veiculo.ano);
			veiculo_content.find('.fabricante').val(veiculo.codigo_veiculo_fabricante);
			buscar_modelo(veiculo_content.find('.fabricante'), veiculo_content.find('.modelo'), veiculo.codigo_veiculo_modelo);
			motorista_proprietario = data.Motorista[i].proprietario;
			preenche_proprietario( data, i, motorista_proprietario );
		}
	}
}


function preenche_proprietario( data, key, motorista_proprietario ){
	dados_veiculo = data.FichaScorecardVeiculo[key];
	$("#Motorista"+key+"Proprietario0").prop('checked', true);
	if ( motorista_proprietario > 0 ){
		$("#Motorista"+key+"Proprietario1").prop('checked', true);
	}
	$("#FichaScorecardVeiculo"+key+"ProprietarioCodigoDocumento").val(dados_veiculo.Proprietario.codigo_documento);
	$("#FichaScorecardVeiculo"+key+"ProprietarioNomeRazaoSocial").val(dados_veiculo.Proprietario.nome_razao_social);
	$("#FichaScorecardVeiculo"+key+"ProprietarioRg").val(dados_veiculo.Proprietario.rg);
	$("#FichaScorecardVeiculo"+key+"ProprietarioRntrc").val(dados_veiculo.Proprietario.rntrc);

	$("#FichaScorecardVeiculo"+key+"ProprietarioEnderecoEnderecoCep").val(dados_veiculo.ProprietarioEndereco.endereco_cep);
	var id_campo_endereco = "#FichaScorecardVeiculo"+ key +"ProprietarioEnderecoCodigoEndereco";
	buscar_cep($("#FichaScorecardVeiculo"+key+"ProprietarioEnderecoEnderecoCep"), id_campo_endereco, dados_veiculo.ProprietarioEndereco.codigo_endereco );
	$("#FichaScorecardVeiculo"+key+"ProprietarioEnderecoNumero").val(dados_veiculo.ProprietarioEndereco.numero);
	$("#FichaScorecardVeiculo"+key+"ProprietarioEnderecoComplemento").val(dados_veiculo.ProprietarioEndereco.complemento);
	carrega_contatos_proprietario( data.FichaScorecard.codigo, key  );
}


function preenche_campos_ficha(data) {
	if(typeof(data.FichaScorecard) !== 'undefined'){
		if(data.FichaScorecard.data_inclusao !== "")
			$("#FichaScorecardDataInclusao").val(data.FichaScorecard.data_inclusao.substring(0,10));
		$("#FichaScorecardObservacao").val(data.FichaScorecard.observacao);
		$("#FichaScorecardCodigoCargaTipo").val(data.FichaScorecard.codigo_carga_tipo);
		$("#FichaScorecardCodigoCargaValor").val(data.FichaScorecard.codigo_carga_valor);
		$("#FichaScorecardCodigoEstadoOrigem").val(data.FichaScorecard.codigo_endereco_estado_carga_origem);
		buscar_cidade($('#FichaScorecardCodigoEstadoOrigem'), $('#FichaScorecardCodigoEnderecoCidadeCargaOrigem'), data.FichaScorecard.codigo_endereco_cidade_carga_origem);
		$("#FichaScorecardCodigoEstadoDestino").val(data.FichaScorecard.codigo_endereco_estado_carga_destino);
		buscar_cidade($('#FichaScorecardCodigoEstadoDestino'), $('#FichaScorecardCodigoEnderecoCidadeCargaDestino'), data.FichaScorecard.codigo_endereco_cidade_carga_destino);
	}
}

function preenche_campos_questionario(data) {
	if(typeof(data.FichaScorecardQuestaoResposta) !== 'undefined'){
		var questionario_content = $("div#questionario");
		for(i = 0; i < data.FichaScorecardQuestaoResposta.length; i++) {
			resposta = data.FichaScorecardQuestaoResposta[i];
			resposta_input = questionario_content.find('div.radio input[value='+resposta.codigo_questao_resposta+']');
			resposta_input.attr('checked', true);
			observacao_input = resposta_input.parent().parent().parent().find('input.observacao').val(resposta.observacao);
		}
		setup_questionario();
	}
}

function validaCamposParaPesquisaProfissional() {
	var codigo_cliente  = $("#FichaScorecardCodigoCliente");
	var codigo_usuario  = $("#FichaScorecardCodigoUsuario");
	var codigo_tipo_profissional = $("#FichaScorecardCodigoProfissionalTipo");
	var codigo_documento_profissional = $("#ProfissionalCodigoDocumento");
	var codigo_documento_profissional_val = codigo_documento_profissional.val().replace('.','').replace('-','').replace('_','');
	$('.error').each(function() {$(this).removeClass('error')});
	$('.error-message').each(function() {$(this).remove()});
	if (codigo_cliente.val() == "" || codigo_usuario.val() == "" || codigo_tipo_profissional.val() == "" || codigo_documento_profissional_val == "") {
		if (codigo_cliente.val() == "") {
			codigo_cliente.parent().addClass('error').append('<div class="help-block error-message">Informe o cliente</div>');;
		}
		if (codigo_usuario.val() == "") {
			codigo_usuario.parent().addClass('error').append('<div class="help-block error-message">Informe o usuário</div>');;
		}
		if (codigo_tipo_profissional.val() == "") {
			codigo_tipo_profissional.parent().addClass('error').append('<div class="help-block error-message">Informe a Categoria</div>');;
		}
		if (codigo_documento_profissional_val == "") {
			codigo_documento_profissional.parent().addClass('error').append('<div class="help-block error-message">Informe o CPF</div>');;
		}
		return false;
	}
	return true;
}

function preenche_campos(data) {	
	preenche_campos_profissional(data);
	preenche_campos_proprietario(data);
	preenche_campos_veiculos(data);
	preenche_campos_ficha(data);
	preenche_campos_questionario(data);
}

function setup_formulario(data) {
	var carreteiro = 1;
	var agregado = 2;
	var funcionario_motorista = 3;
	var proprietario = 4;
	var funcionario = 5;
	var ajudante = 6;
	var conferente = 7;
	var buonny_rh = 8;
	var prestador_de_servicos = 9;
	var vigilande_de_escolta_armado = 10;

	var codigo_tipo_profissional_selecionado = parseInt($("#FichaScorecardCodigoProfissionalTipo").val());
	$('.dados-ficha').show(); ///
	setup_pergunta_possui_veiculo();
	if ([carreteiro,agregado,funcionario_motorista,proprietario].indexOf(codigo_tipo_profissional_selecionado) > -1) {
		$(".dados-cnh").show();
		$(".dados-veiculo").show();
		if (codigo_tipo_profissional_selecionado == carreteiro) {
			$(".pergunta-possui-veiculo").hide();
			$('#FichaScorecardVeiculoPossuiVeiculoS').prop('checked', true);
		} else {
			$(".pergunta-possui-veiculo").show();
		}
		$(".veiculo-content-0").hide();
		$(".veiculo-content-1").hide();
		$(".veiculo-content-2").hide();
		$("[name='data[FichaScorecardVeiculo][possui_veiculo]']:checked").click();
		$("[name='data[FichaScorecardVeiculo][1][Veiculo][veiculo_sn]']:checked").click();
		$("[name='data[FichaScorecardVeiculo][2][Veiculo][veiculo_sn]']:checked").click();
	} else {
		$(".dados-cnh").hide();
		$(".dados-veiculo").hide();
	}
}

function setup_pergunta_possui_veiculo() {
	$("div[id^=veiculo-data]").find('input:radio').click(function() {
		var level = $(this).attr('level');
		var content = $(this).parent().parent().parent().parent().find('.veiculo-content-'+level);		
		if ($(this).val() == 'S') {
			content.show();
		} else {
			content.hide();
			content.find('input').not(':radio').each(function() {$(this).val('')});
		}
	})
}

function libera_pesquisa(data) {
	var div = $("#ProfissionalCodigoDocumento").parent();
	var carreteiro = 1;
	var codigo_tipo_profissional_selecionado = parseInt($("#FichaScorecardCodigoProfissionalTipo").val());
	if (codigo_tipo_profissional_selecionado == carreteiro) {
		if (data.Carreteiro.total > 0) {
			div.addClass('error').append('<div id="lbl-error1" class="help-block error-message">O CPF está em pesquisa.</div>');
			return false;
		}
	} else {
		if (data.Cliente.total > 0) {
			div.addClass('error').append('<div id="lbl-error1" class="help-block error-message">O CPF está em pesquisa.</div>');
			return false
		}
	}
	return true;
}

function setup_codigo_documento_profissional() {
	$(document).on('click', '#btn-codigo_documento_profissional', function(){
		var cpf = $("#ProfissionalCodigoDocumento");
		var codigo_tipo_profissional = $("#FichaScorecardCodigoProfissionalTipo").val();
		var codigo_produto_teleconsult = 134;
		var codigo_servico_cadastro = 1;
		limpar_campos_profissional();
		$('#msg-valida_assinatura').remove();
		cpf.addClass("ui-autocomplete-loading");
		$('.dados-ficha').hide();
		if (validarCPF( cpf.val()) ) {
			if (validaCamposParaPesquisaProfissional()) {
				if(validaAssinaturaCliente(codigo_produto_teleconsult, codigo_servico_cadastro)){
					var codigo_cliente_transportador = $("#FichaScorecardCodigoTransportador").val();
					$.ajax({
						url: baseUrl + "fichas_scorecard/carregar_por_cpf/" + cpf.val() + "/" + codigo_tipo_profissional + "/" + codigo_cliente_transportador + "/" + Math.random(),
						type: "post",
						dataType: "json",
						beforeSend: function(){
							cpf.addClass("ui-autocomplete-loading");
						},
						success: function(data){
							var libera_formulario = true;
							if(data){
								if (libera_pesquisa(data)) {
									preenche_campos(data);
								} else {
									libera_formulario = false;
								}
							}
							if (libera_formulario) {
								setup_formulario(data);
							}
						},
						complete: function(){
							cpf.removeClass("ui-autocomplete-loading");
						}
					});
				} else {
					if ($('#msg_valida_assinatura').length == 0) {
						$("#FichaScorecardIncluirForm").prepend("<div id='msg_valida_assinatura' class='alert alert-error'>Serviço não disponível para o embarcador e transportador selecionados. Favor entrar em contato com o Departamento Comercial.</div>");
					}
					cpf.removeClass("ui-autocomplete-loading");
				}
			} else {
				cpf.removeClass("ui-autocomplete-loading");
			}
		}else {
			cpf.removeClass("ui-autocomplete-loading");
		}
		return false;
	});
}	

function limpar_campos_proprietario(index){	
	$("#FichaScorecardVeiculo"+index+"ProprietarioCodigoDocumento").val("");
	$("#FichaScorecardVeiculo"+index+"ProprietarioNomeRazaoSocial").val("");
	$("#FichaScorecardVeiculo"+index+"ProprietarioRg").val("");
	$("#FichaScorecardVeiculo"+index+"ProprietarioEnderecoEnderecoCep").val("");
	$("#FichaScorecardVeiculo"+index+"ProprietarioEnderecoNumero").val("");
	$("#FichaScorecardVeiculo"+index+"ProprietarioEnderecoComplemento").val("");
	$("#FichaScorecardVeiculo"+index+"ProprietarioEnderecoCodigoEndereco").val("");
	contador =0;
	while (contador <=10 ) {
		$("#FichaScorecardVeiculo"+index+"ProprietarioContato" + contador + "Nome").val("");
		$("#FichaScorecardVeiculo"+index+"ProprietarioContato" + contador + "CodigoTipoContato").val("");
		$("#FichaScorecardVeiculo"+index+"ProprietarioContato" + contador + "CodigoTipoRetorno").val("");
		$("#FichaScorecardVeiculo"+index+"ProprietarioContato" + contador + "Descricao").val("");  
		contador ++;
	}
	$("#FichaScorecardVeiculo"+index+"ProprietarioEnderecoEnderecoCep").trigger("blur");
}

function copia_campos_proprietario(index){
	$("#FichaScorecardVeiculo"+index+"ProprietarioCodigoDocumento").val($("#ProfissionalCodigoDocumento").val());
	$("#FichaScorecardVeiculo"+index+"ProprietarioNomeRazaoSocial").val($("#ProfissionalNome").val());
	$("#FichaScorecardVeiculo"+index+"ProprietarioRg").val($("#ProfissionalRg").val());
	$("#FichaScorecardVeiculo"+index+"ProprietarioEnderecoEnderecoCep").val($("#ProfissionalEnderecoEnderecoCep").val());
	$("#FichaScorecardVeiculo"+index+"ProprietarioEnderecoNumero").val($("#ProfissionalEnderecoNumero").val());
	$("#FichaScorecardVeiculo"+index+"ProprietarioEnderecoComplemento").val($("#ProfissionalEnderecoComplemento").val());
	contador =0;
	while (contador <=10 ) {
		$("#FichaScorecardVeiculo"+index+"ProprietarioContato" + contador + "Nome").val($("#ProfissionalContato"+contador+"Nome").val());
		$("#FichaScorecardVeiculo"+index+"ProprietarioContato" + contador + "CodigoTipoContato").val($("#ProfissionalContato"+ contador +"CodigoTipoContato").val());
		$("#FichaScorecardVeiculo"+index+"ProprietarioContato" + contador + "CodigoTipoRetorno").val($("#ProfissionalContato"+ contador + "CodigoTipoRetorno").val());
		$("#FichaScorecardVeiculo"+index+"ProprietarioContato" + contador + "Descricao").val($("#ProfissionalContato"+contador+"Descricao").val());  
		contador ++;
	}
	buscar_cep($('#FichaScorecardVeiculo'+ index + 'ProprietarioEnderecoEnderecoCep'), 
		'#FichaScorecardVeiculo'+ index + 'ProprietarioEnderecoCodigoEndereco',
		$('#ProfissionalEnderecoCodigoEndereco').val()
	);
}

function setup_copia_dados_profissional(index){	
	$(".motorista-proprietario"+index).change(function(){
		if($(this).find(":input").val() == 1){
			copia_campos_proprietario(index);
		} else {
			limpar_campos_proprietario(index);
		}
		desabilitar_campo("#ProprietarioNomeRazaoSocial");
		desabilitar_campo("#ProprietarioRg");
	});
}

function setup_questionario() {
	var questionario = $("div#questionario");
	questionario.find('div.radio').find('input:last').addClass('sem-observacao');
	if(questionario.find('div.radio').find('input:checked').length == 0){
		questionario.find('div.radio').find('input:last').attr('checked', true);
	}
	questionario.find('div.radio').find('input:checked').each(function(){
		esconde_exibe_observacao_questao(this);
		adiciona_placeholder_observacao(this, false);
	});
	
	questionario.find('input:radio').click(function(){
		esconde_exibe_observacao_questao(this);
		adiciona_placeholder_observacao(this, true);
	});
}

function esconde_exibe_observacao_questao(element){
	var input_obs = $(element).parent().parent().parent().find('input.observacao');
	if($(element).hasClass('sem-observacao')){
		input_obs.hide();
		input_obs.attr('disabled', true);
	}else{
		input_obs.show();
		input_obs.removeAttr('disabled');
	}
}

function adiciona_placeholder_observacao(element, replace){
	var input_obs = $(element).parent().parent().parent().find('input.observacao');
	if(replace){
		input_obs.val('');
	}
	if($(element).val() == 1 || $(element).val() == 3){
		input_obs.attr('placeHolder', 'Quantas vezes?')
	}else if($(element).val() == 5){
		input_obs.attr('placeHolder', 'Quantos anos?')
	}else if($(element).val() == 6){
		input_obs.attr('placeHolder', 'Quantos meses?')
	}else if($(element).val() == 7){
		input_obs.attr('placeHolder', 'Quantas vezes?')
	}else if($(element).val() == 8){
		input_obs.attr('placeHolder', 'Qual?')
	}
}

function buscar_dados_placa(link){
	placa = $(link).parent().find('.placa-veiculo');
	veiculo_content  = placa.parent().parent().parent();
	placa_pesquisada = placa.val();
	if( placa_pesquisada ){
		$.ajax({
			url: baseUrl + "veiculos/buscar/" + placa.val() + "/" + Math.random(),
			type: "post",
			dataType: "json",
			beforeSend: function(){
				placa.addClass("ui-autocomplete-loading");
			},
			success: function(data){
				if(data != false){
					preenche_campos_veiculo(veiculo_content, data);
				} else {
					limpar_campos_veiculo(veiculo_content);
					veiculo_content.find('.placa-veiculo').val( placa_pesquisada );
				}
			},
			complete: function(){
				placa.removeClass("ui-autocomplete-loading");
			}
		});
	} else {
		limpar_campos_veiculo(veiculo_content);
	}
}

function limpar_campos_veiculo(veiculo_content){
	veiculo_content.find('.estado').val("");
	buscar_cidade(veiculo_content.find('.estado'), veiculo_content.find('.cidade'), "")
	veiculo_content.find('.tecnologia').val("");
	veiculo_content.find('.chassi').val("");
	veiculo_content.find('.renavam').val("");
	veiculo_content.find('.cidade_nome').val("");
	veiculo_content.find('.cor').val("");
	veiculo_content.find('.ano-fabricacao').val("");
	veiculo_content.find('.ano').val("");
	veiculo_content.find('.fabricante').val("");
	veiculo_content.find('#codigo_cidade').val("");
	veiculo_content.find('#codigo_estado').val("");
	veiculo_content.find('#codigo_veiculo').val("");
	buscar_modelo(veiculo_content.find('.fabricante'), veiculo_content.find('.modelo'), "");
}

function preenche_campos_veiculo(veiculo_content, data){
	veiculo_content.find('.estado').val(data.EnderecoEstado.codigo);
	buscar_cidade(veiculo_content.find('.estado'), veiculo_content.find('.cidade'), data.EnderecoCidade.codigo)
	veiculo_content.find('.tecnologia').val(data.Veiculo.codigo_veiculo_tecnologia);
	veiculo_content.find('.chassi').val(data.Veiculo.chassi);
	veiculo_content.find('.renavam').val(data.Veiculo.renavam);
	veiculo_content.find('.cor').val(data.Veiculo.codigo_veiculo_cor);
	veiculo_content.find('.ano-fabricacao').val(data.Veiculo.ano_fabricacao);
	veiculo_content.find('.ano').val(data.Veiculo.ano);
	veiculo_content.find('.fabricante').val(data.VeiculoFabricante.codigo);
	buscar_modelo(veiculo_content.find('.fabricante'), veiculo_content.find('.modelo'), data.Veiculo.codigo_veiculo_modelo);
	if( data.EnderecoCidade.descricao && data.EnderecoEstado.abreviacao)
		veiculo_content.find('.cidade_nome').val(data.EnderecoCidade.descricao+' - '+data.EnderecoEstado.abreviacao);
	veiculo_content.find('#codigo_cidade').val(data.EnderecoCidade.codigo);
	veiculo_content.find('#codigo_estado').val(data.EnderecoEstado.codigo);
	veiculo_content.find('#codigo_pais').val(data.Veiculo.codigo_pais);
	veiculo_content.find('#codigo_veiculo').val(data.Veiculo.codigo);	
}

function setup_codigo_seguranca(){
	$(".codigo-seguranca").parent().find("i").mouseover(function(){
		$(this).popover({placement:"left", title:false, content:"<img src='" + baseUrl + "img/codigo_seguranca_cnh.jpg'/>", html:true, trigger:"manual"});
		$(this).popover("show");
	});
	
	$(".codigo-seguranca").parent().find("i").mouseout(function(){
		$(this).popover("destroy");
	});
}

function setup_limpar_sessao() {
	$(".btn-limpar").click(function(){	
		$(this).parent().parent().find(':input').val("").blur().change();
		//remover erro quando limpar campos
		$(this).parent().parent().find(':input').removeClass('form-error').parent().removeClass('error').find('#lbl-error').remove();		
	});
}

function setup_categoria() {
	$("#FichaScorecardCodigoProfissionalTipo").change(function(){
		var conteiner = $("table.contato-profissional tbody");
		if($(this).val() == 1){
			contador_contato_profissional = conteiner.find('.contato-profissional-item').length;

			$.ajax({
				url: baseUrl + "fichas_scorecard/novo_contato_profissional/"+ contador_contato_profissional +"/tipo_retorno_fixo:5/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.append(data);
				}
			});
		} else {
			conteiner.find('.contato-profissional-item').each(function(){
				if($(this).find('.tipo_retorno').val() == 5 && $(this).find('.remove-contato-profissional').length == 0){
					$(this).remove();
				}
			});
		}
	})
}

function setup_exibir_observacao_criterio(){
	$('.exibe-observacao').click(function(){
		var link = $(this);
		var div_observacao = link.parent().find('.observacao-criterio'); 
		if (div_observacao.is(':visible')){
			div_observacao.slideUp();
			link.html("Exibir observação");
		}else{
			div_observacao.slideDown();
			link.html('Esconder observação');
		}
	});
}

function setup_desabilita_formulario() {
	$('div#ficha :input').attr('disabled', 'disabled');
	$('div#ficha .actionbar-right, div#ficha .btn-group, div#ficha .icon-search').remove();
}

function setup_desabilita_formulario_pesquisa() {   
	$('div#ficha :input').attr('disabled', 'disabled');
}

function setup_sinalizar_criterios_divergentes() {
	$('#pesquisa select').each(function(){ 
		if (parseInt($(this).val()) == 0){
			$(this).parent().addClass('info');
		}
	});
	
	$('#pesquisa select').change(function(){
	  $(this).parent().removeClass('error');
		if (parseInt($(this).val()) == 0){
			$(this).parent().addClass('info');
		} else {
		  $(this).parent().removeClass('info');
		}
	}); 
}

function setup_sinalizar_criterios_insuficientes() {
	$('#pesquisa select').each(function(){ 
		if ($(this).val() == -1){
			$(this).parent().addClass('warning');
		}
	});
	
	$('#pesquisa select').change(function(){
	  $(this).parent().removeClass('error');
		if ($(this).val() == -1){
			$(this).parent().addClass('warning');
		} else {
		  $(this).parent().removeClass('warning');
		}
	}); 
}

function setup_produto() {
	var toggleProprietario = function(element){
		var produto_selecionado = $(element).find('option:selected').text().toUpperCase();
		if(produto_selecionado.indexOf("TELECONSULT") == 0) {
			$('.proprietario-content-1').hide();
			$('.proprietario-content-1 :input').attr('disabled', 'disabled');
		} else {
			$('.proprietario-content-1').show();
			$('.proprietario-content-1 :input').removeAttr('disabled');
		}
	}
	
	$('#FichaScorecardCodigoProduto').change(function(){
		toggleProprietario(this);
	});
	
	toggleProprietario($('#FichaScorecardCodigoProduto'));
}

/*Desabilitar os campos dos dados do veiculo da ficha*/
function ocultaCamposVeiculoBitrem( elemento ){
	if( $(elemento).val() == 'N' ){
		
		$("#veiculo-content-2").hide();
		$(".proprietario-content2").hide();

		limpar_campos_proprietario(tipo_veiculo);
		limpar_campos_proprietario(prox_tipo_veiculo);
	} 
	if($(elemento).val() == 'S'){
		$(".proprietario-content" + $(elemento).attr('for') ).show(); 
		$("#veiculo-content-" + $(elemento).attr('for') ).show();
		tipo_veiculo = parseInt( $(elemento).attr('for') );		
		prox_tipo_veiculo = ++tipo_veiculo;
		$(".tipo_veiculo"+prox_tipo_veiculo).show();
        $('#tipo_veiculo1').show();
		$('#tipo_veiculo2').show();
		$('#possui_veiculo2').show();
		$("#possui_veiculo_2").show();
		
	}
    
}
/*Desabilitar os campos dos dados do veiculo da ficha*/
function ocultaCamposVeiculo( elemento ){
	if( $(elemento).val() == 'N' ){
		$(".proprietario-content" + $(elemento).attr('for') ).hide(); 
		$("#veiculo-content-" + $(elemento).attr('for') ).hide();
		tipo_veiculo = parseInt( $(elemento).attr('for') );		
		prox_tipo_veiculo = ++tipo_veiculo;
		$(".tipo_veiculo"+prox_tipo_veiculo).hide();
		$("#possui_veiculo_2").hide();
		$("#veiculo-content-2").hide();
		$(".proprietario-content2").hide();

		limpar_campos_proprietario(tipo_veiculo);
		limpar_campos_proprietario(prox_tipo_veiculo);
	} 
	if($(elemento).val() == 'S'){
		$(".proprietario-content" + $(elemento).attr('for') ).show(); 
		$("#veiculo-content-" + $(elemento).attr('for') ).show();
		tipo_veiculo = parseInt( $(elemento).attr('for') );		
		prox_tipo_veiculo = ++tipo_veiculo;
		$(".tipo_veiculo"+prox_tipo_veiculo).show();
        $('#tipo_veiculo1').show();
		$('#tipo_veiculo2').show();
		$('#possui_veiculo2').show();
		$("#possui_veiculo_2").show();
		$("#veiculo-content-2").hide();
		$(".proprietario-content2").hide();
		
	}
}

function carrega_profissional_por_cpf(codigo_cliente,carregar_ficha) {
	cpf = $("#ProfissionalCodigoDocumento");
	codigo_cliente  = $("#FichaScorecardCodigoCliente");
	usu_cliente     = $("#FichaScorecardCodigoUsuario");
	//$("#FichaScorecardIncluirForm").prepend("<div class='alert alert-error'>Serviço não disponível para o embarcador e transportador selecionados. Favor entrar em contato com o Departamento Comercial.</div>");
	if(carregar_ficha == 1){
		ultima_ficha = codigo_ultima_ficha_profissional(cpf.val());
		if(ultima_ficha){
			cpf.parent().parent().append('<label>&nbsp;</label><a href="javascript:consulta_ficha_scorecard('+ultima_ficha+')" class="icon-briefcase"></a>')
		}
	}
	$.ajax({
		url: baseUrl + "fichas_scorecard/carregar_por_cpf/" + cpf.val() + "/" + Math.random(),
		type: "post",
		dataType: "json",
		beforeSend: function(){
			cpf.addClass("ui-autocomplete-loading");
		},
		success: function(data){
			if(data){
					preenche_campos_profissional(data);
					$("#ProfissionalCodigoDocumento").search_ultima_ficha_scorecard(codigo_cliente);
			}else {
				limpar_campos_profissional();
			}	
			desabilitar_campos_profissional();
		},
		complete: function(){
			cpf.removeClass("ui-autocomplete-loading");
		}
	});
}

function sinalizar_criterios_divergentes() {	
	$('#pesquisa select').change( function(){ 
		var campo_criterio = $(this);
		var codigo_status_criterio = campo_criterio.val();
		var codigo_criterio = campo_criterio.attr('for');
		if ( codigo_criterio ){			
			$.ajax({
				url: baseUrl + "pontuacoes_status_criterios/verifica_campo_divergente/" + codigo_status_criterio +"/" + codigo_criterio,
				type: "post",
				dataType: "json",
				success: function( data ){ 
					if( data ){
						campo_criterio.parent().addClass('info');
					} else {						
						campo_criterio.parent().removeClass('info');
					}
				}
			});			
		}
	});
}

function sinalizar_criterios_insuficientes() {	
	$('#pesquisa select').change( function(){ 
		var campo_criterio = $(this);
		var codigo_status_criterio = campo_criterio.val();
		var codigo_criterio = campo_criterio.attr('for');
		if ( codigo_criterio ){			
			$.ajax({
				url: baseUrl + "pontuacoes_status_criterios/verifica_campo_insuficiente/" + codigo_status_criterio +"/" + codigo_criterio,
				type: "post",
				dataType: "json",
				success: function( data ){ 
					if( data ){
						campo_criterio.parent().addClass('warning');
					} else {						
						campo_criterio.parent().removeClass('warning');
					}
				}
			});			
		}
	});
}

function carregar_usuario( element ){
	codigo_usuario = element.val();	
	if ( parseInt(codigo_usuario) > 0 ){
		$.ajax({
			url: baseUrl + 'usuarios/carregar_usuario/' + codigo_usuario,
			async:false,
			type:"post",
			dataType: "json",
			success: function( data ){
				cpf_cnpj = data.Usuario.codigo_documento;
				$("#UsuarioCodigoDocumento").val( cpf_cnpj );
			}
		});
	}
}

function codigo_ultima_ficha_profissional(codigo_documento){
   codigo_documento = codigo_documento.replace(/[^\d]+/g,'');
    if ( parseInt(codigo_documento) > 0 ){
            codigo_ficha = $.ajax({
                    url: baseUrl + 'fichas_scorecard/codigo_ultima_ficha_profissional/' + codigo_documento,
                    async:false,
                    type:"post",
                    dataType: "json",
                    success: function( data ){}
            });
            codigo = codigo_ficha.responseText;
            return codigo;
    }
}

$.fn.search_ultima_ficha_scorecard = function(interno) {
    return this.each(function() {
        var input = $(this);
        var root_id = input.attr('id')+'-search';
        if ($('#' + root_id).length == 0) {
			a = (interno !== '') ? '': '<a id="' + root_id + '"href="javascript:void(0)" class="icon-briefcase"></a>';
            input.after(a);
            var icon_search = $('#' + root_id);
            icon_search.css('display', input.css('display'));
            icon_search.click(function() {
                codigo_ficha = codigo_ultima_ficha_profissional( input.val() );
                var link = "/portal/fichas_status_criterios/resultado_ficha/" + codigo_ficha + "/" + Math.random();
                open_dialog(link, "Ficha", 940);
            });
        }
    });
}

function preenche_cidade_inline(){
	$(function() {
	  $('.ui-autocomplete-input').autocomplete({        
	  source: baseUrl + 'enderecos/autocompletar/',
	  focus: function(){return false;},
	  minLength: 3,
	  select: function( event, ui ) {		      	
	  	veiculo_content = $(this).parent().parent().parent();
	    codigo_cidade   = ui.item.value;
	    cidade_nome     = ui.item.label;
	    codigo_estado   = ui.item.uf_value;
	    codigo_pais     = ui.item.codigo_pais;
	    veiculo_content.find('#codigo_cidade').val(codigo_cidade);
	    veiculo_content.find('#codigo_estado').val(codigo_estado);
	    veiculo_content.find('#codigo_pais').val(codigo_pais);
	  	$(this).val( cidade_nome );
	    return false;
	  }});
	});
}

function buscar_nome_cidade(codigo){
	if ( parseInt(codigo) > 0){
		$.post(
			baseUrl + 'enderecos/carrega_combo_cidade_nome/' + codigo + '/' + Math.random(),
			function(data) {
				if( data ){
	                $("#ProfissionalCidadeNaturalidadeProfissional").val(data);
					desabilitar_campo( $("#ProfissionalCidadeNaturalidadeProfissional") );
				}
			}
		);
	} 
}

function buscar_nome_cidade_veiculo(codigo,i){
	if ( parseInt(codigo) > 0){
		$.post(
			baseUrl + 'enderecos/carrega_combo_cidade_nome/' + codigo + '/' + Math.random(),
			function(data) {
                if (parseInt(i)==0){
                   $("#FichaScorecardVeiculo0EnderecoCidadeCidadeEmplacamento").val(data);
			    }
			    if (parseInt(i)==1){
                   $("#FichaScorecardVeiculo1EnderecoCidadeCidadeEmplacamento").val(data);
			    }
			    if (parseInt(i)==2){
                   $("#FichaScorecardVeiculo2EnderecoCidadeCidadeEmplacamento").val(data);
			    }
			         
			}
		);
	} 
}

function validaAssinaturaCliente(codigo_produto, codigo_servico){ 
	codigo_cliente = $("#FichaScorecardCodigoCliente").val();
	codigo_cliente_embarcador = $("#FichaScorecardCodigoEmbarcador").val();
	codigo_cliente_transportador = $("#FichaScorecardCodigoTransportador").val();
	var assinatura = true;

	$.ajax({
		data:{  
			'data[ClienteProduto][codigo_cliente]' : codigo_cliente,
			'data[ClienteProduto][codigo_produto]' : codigo_produto,
			'data[ClienteProduto][codigo_servico]' : codigo_servico,
			'data[ClienteProduto][codigo_cliente_transportador]' : codigo_cliente_transportador,
			'data[ClienteProduto][codigo_cliente_embarcador]' : codigo_cliente_embarcador
		},
		url: baseUrl + 'clientes_produtos/buscar_assinatura_cliente/'+Math.random(),
		async:false,
        type:"post",
        dataType: "json",
		success: function(data) {
			data = $.parseJSON(data);
			if(!data){
				assinatura = false;
			}
		}
	});
	return assinatura;
}

jQuery('.nome_sobrenome').each( function(){
if(!jQuery(this).hasClass('valida-nome_sobronome')){
	$(this).blur(function() {
		if (validaNomeSobrenome($(this).val())) {
			$(this).removeClass('form-error').parent().removeClass('error').find('#lbl-error').remove();
		} else {
			if (!$(this).hasClass('form-error')) {
				$(this).addClass('form-error').parent().addClass('error').append('<div id="lbl-error" class="help-block error-message">Nome inválido</div>');
			}
		}
	})
}
});

function validaNomeSobrenome( nome ){
	if(regex = /[a-z]+\s[a-z]+/gi.exec(nome)){
		return true;
	}
	else{
		return false;
	}
}

function previsualizar_score( codigo_ficha_scorecard ){
	$('#pre_score').load( baseUrl + '/fichas_status_criterios/pre_visualizar_score/'+codigo_ficha_scorecard, $('#perguntas select').serialize() );	
}


function bloqueia_campos_ficha() {	
	var codigo_documento = $("#ProfissionalCodigoDocumento").val();
	var codigo_tipo_profissional = $("#FichaScorecardCodigoProfissionalTipo").val();
	var codigo_cliente_transportador = $("#FichaScorecardCodigoTransportador").val();
	$.ajax({
		url: baseUrl + "fichas_scorecard/carregar_por_cpf/" + codigo_documento+ "/" + codigo_tipo_profissional+"/"+ codigo_cliente_transportador + "/" + Math.random(),
		type: "post",
		dataType: "json",
		success: function(data){
			if(data){
				desabilita_campos_profissional(data);
			}
		}
	});		
	return false;
}	
