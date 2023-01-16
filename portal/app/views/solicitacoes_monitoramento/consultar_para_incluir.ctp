<?php echo $this->BForm->success_menssage($sm) ?>

<!-- MODELOS -->
<?php $css = "position:absolute;top:100px;right:50px;width:250px;" ?>
<?php $css .= (!$modelos)?"display:none;":NULL ?>
<div id="lista" style="<?php echo $css ?>">
	<h5>Modelos</h5>
	<ul class="well nav" style="overflow-y:scroll;max-height:210px">
	<?php if ($modelos): ?>
		<?php echo $this->element('viagens_modelos/lista_fields') ?>
	<?php endif; ?>
	
	</ul>
</div>
<!-- MODELOS FIM -->

<div class="span12" style="margin-left:0;">
	<?php echo $this->BForm->create('Recebsm', array('url' => array('controller' => 'solicitacoes_monitoramento', 'action' => ($remonta == 'S' ? 'consultar_para_incluir_remonta' : ($lg ? 'consultar_para_incluir_loadplan':'consultar_para_incluir')))), '');?>
		<?php if($mensagem): ?>
		<section class="form-actions alert-error veiculo-error" >
			<h5>Erros:</h5>
			<?php echo $mensagem ?>
		</section>
		<?php endif; ?>

		<?php if(!$authUsuario['Usuario']['codigo_cliente']):?>
			<h4>Cliente</h4>
		<?php elseif($authUsuario['Usuario']['codigo_cliente'] && $fields_view):?>
			<div class="control-group input select error">
			<h4 class="help-block error-message">Cliente</h4>
			<div class="help-block error-message">Usuario não habilitado para a inclusão de SM's</div>
			</div>
		<?php endif ?>

		<div class='row-fluid inline'>
			<?php echo $this->Buonny->input_cliente_usuario_cliente_monitora($this, $usuarios) ?>
		</div>
		<?php echo $this->BForm->input('codigo_pre_sm', array('type'=>'hidden', 'label' => false, 'value'=>'')) ?>
		<div id="fields-view" style="<?php echo $fields_view ?>">
			
			<h4>Gerenciadora de Risco</h4>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('gerenciadora', array('label' => false, 'empty' => 'Selecione uma Gerenciadora', 'options' => $gerenciadoras,'class'=>'input-xlarge')) ?>
				<?php echo $this->BForm->input('liberacao', array('label' => false, 'class' => 'input-medium', 'placeholder' => 'Nº Liberação', 'maxlength' => 15)) ?>
			</div>

			<div id="pre-modelo">
				<?php echo $this->element('solicitacoes_monitoramento/consultar_para_incluir') ?>
			</div>

		</div>

	<div class="form-actions">
	  <?php echo $this->BForm->submit('Avançar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	</div>
	<?php echo $this->BForm->end(); ?>
</div>

<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('sm_online')) ?>
<?php $this->addScript($this->Javascript->codeBlock("
	function setup_remove_linha(linha) {
		$(linha).find('a').attr('onclick', 'remove_placa(jQuery(this).parent().parent())').removeClass('btn-success');
		$(linha).find('i').removeClass('icon-plus').removeClass('icon-white').addClass('icon-minus');
	}	
	
	function adiciona_placa(linha) {
		if ($(linha).find('#RecebsmPlaca').val() != '' && $(linha).find('#RecebsmTipo').val() != '') {
			var insert_tr = $(linha).clone();
			setup_remove_linha(linha);
			insert_tr.find('div.error').removeClass('error').find('div.help-inline').remove();
            insert_tr.find('#RecebsmPlaca').removeClass('format-plate').val('');
            insert_tr.find('#RecebsmTipo').val('');
            insert_tr.find('#RecebsmTecnologia').val('');
			$(linha).after(insert_tr);
			setup_mascaras();
		} else {
			alert('Prencha as informações do veículo');
		}
	}

	function remove_placa(linha) {
		jQuery(linha).remove();
	}
		
	function valida_duplicidade(seletor, mensagem) {
		var valido = true;
		var campos = jQuery(seletor);
		var qtd = campos.length;
		for(var i = 0; i < qtd; i++) {
			for(var j = i-1; j >= 0; j--){
				if(jQuery(campos[i]).val() == jQuery(campos[j]).val()){
					jQuery(campos[i]).parent().addClass('error').append('<div class=\"help-inline\" style=\"padding: 0;\">'+mensagem+'</div>');
					valido = false;
					break;
				}
			}
		}
		return valido;
	}
	
	function verifica_pre_sm() {
	    var embarcador = $('#RecebsmEmbarcador').val();
	    var transportador = $('#RecebsmTransportador').val();
	    var retorno = true;
	    //var inputTecnologia = $(linha).find('#RecebsmTecnologia');

	    //var placa = $(linha).find('#RecebsmPlaca').val();
	    if (embarcador != '' && transportador!='') {
    	    var retorno = $.ajax({
    	        url: baseUrl + 'solicitacoes_monitoramento/verifica_pre_sm/embarcador:' + embarcador +'/transportador:'+transportador +'/' + Math.random(),
    	        dataType: 'json',
    	        async: false
    	    }).responseText;
			retorno = (isNaN(retorno)?true:parseInt(retorno));
			if (!retorno) {
                var link = '/portal/solicitacoes_monitoramento/pre_sm_pendentes_listagem/embarcador:' + embarcador +'/transportador:'+transportador + '/' + Math.random();
                open_dialog(link, 'Selecionar Pré-SM', 940);	
                return false;
			}
    	}
    	
    	return retorno;
	}

	$(document).on('submit','#RecebsmConsultarParaIncluirForm',function() {
		var valido = true;
		
		jQuery('.veiculos div.error').removeClass('error').find('div.help-inline').remove();
		
		valido = valido && valida_duplicidade('.placa-veiculo:visible', 'Placa já informada');
				
		if(valido){
		    var tem_carreta = false;
		    var tem_cavalo = false;
		    var tem_aguarde = false;
		    $('.tipo').each(function(idx, element) {
		        if (element.value == 'CARRETA') tem_carreta = true;
		        if (element.value == 'CAVALO') tem_cavalo = true;
		        if (element.value == 'Aguarde...') tem_aguarde = true;
		    });
		    if (tem_aguarde) {
		        alert('Aguarde o carregamento das informações das placas.');
		        return false;
		    }
		    var ret = true;
		    if ($('#RecebsmCodigoPreSm').val()!='') return true;
		    if (tem_cavalo && !tem_carreta)
		        ret = confirm('Continuar sem informar a carreta?');

		    if (ret) {
		    	return verifica_pre_sm();
		    } else {
		    	return false;
		    }
		    //return true;
		}
		return false;
	});

	function sem_motorista(check){
		var check = $(check);

		if(check.is(':checked')){
			$('.motorista-data').hide();
			$('.motorista-data :input').val('');
		}else
			$('.motorista-data').show();
	}

	$(document).ready(function () {
		sem_motorista('#RecebsmSemMotorista');
		
		$(document).on('change','#RecebsmSemMotorista',function(){
			sem_motorista('#RecebsmSemMotorista');
		});

		$(document).on('change','#RecebsmCodigoUsuario',function(){
			if($(this).val()){
				$('#fields-view').slideDown();
				embarcador_transportador();
				$.placeholder.shim();
			} else {
				$('#fields-view').slideUp();
			}
		});

		function embarcador_transportador(){
			var cliente_codigo 	= $('#RecebsmCodigoCliente').val();
			var embarcador 		= $('#RecebsmEmbarcador');
			var transportador 	= $('#RecebsmTransportador');
			var gerenciadora 	= $('#RecebsmGerenciadora');


			if(cliente_codigo){
				embarcador.html('<option value=\'\'>Aguarde...</option>');
			    transportador.html('<option value=\'\'>Aguarde...</option>');
			    gerenciadora.html('<option value=\'\'>Aguarde...</option>');

				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/lista_gerenciadoras_pessoa_jur/' + cliente_codigo + '/' + Math.random(),
			        //dataType: 'json',
			        'success': function(data) {			            
			            //gerenciadora.html(data.html);
			            gerenciadora.html(data);
			            gerenciadora.val('".$this->data['Recebsm']['gerenciadora']."');
		        	}
				});

				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/lista_embarcadores/' + cliente_codigo + '/' + Math.random(),
			        dataType: 'json',
			        'success': function(data) {
			            if(data.tipo == 4)
			            	embarcador.attr('readonly',false);
			            else
			            	embarcador.attr('readonly',true);
			            
			            embarcador.html(data.html);
		        	}
				});

				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/lista_transportadores/' + cliente_codigo + '/' + Math.random(),
			        dataType: 'json',
			        'success': function(data) {
			            if(data.tipo == 4)
			            	transportador.attr('readonly',true);
			            else
			            	transportador.attr('readonly',false);
			            
			            transportador.html(data.html);
		        	}
				});
			}
			return false;
		}

		setup_mascaras();

		hidde_liberacao($('select#RecebsmGerenciadora').val());

		$(document).on('change', 'select#RecebsmGerenciadora',function(){
			hidde_liberacao($(this).val());
		});

		function hidde_liberacao(valor){
			var liberacao = $('#RecebsmLiberacao');

			if( valor == 4 || valor == '' || valor == 1){
				liberacao.val('');
				liberacao.parent().css('display','none');

			} else {
				liberacao.parent().css('display','block');

			}
		}
		
		$(document).on('blur','#RecebsmCodigoDocumento',function(){
			busca_dados_motorista($(this).val());
			 $.placeholder.shim();
			return false;
		});

		busca_dados_motorista($('#RecebsmCodigoDocumento').val());
	})"
)) ?>