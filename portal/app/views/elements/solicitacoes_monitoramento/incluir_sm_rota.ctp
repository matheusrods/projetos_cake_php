<h4>Rota</h4>
<div id="rota-div" class='row-fluid inline'>
	<div class='form-error' >
		<?php echo $this->Buonny->input_rota_emb_transp($this, "#RecebsmEmbarcador", "#RecebsmTransportador", "Recebsm", "vrot_rota_codigo",false,'Rota',false,true, true); ?>
		<?php if(isset($nao_permitir_gerar_rota_vpp_rota_sm) && $nao_permitir_gerar_rota_vpp_rota_sm == FALSE): ?>
			<?php echo $this->Html->link('Gerar Rota', 'javascript:gerarRota()', array('title' => 'Gerar Rota', 'class' => 'btn btn-success'));?>
		<?php endif; ?>
	</div>
	
</div>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry"></script>
<div id="gerar-rota" class="well" style="display:none;">
</div>

<?php $this->data['Recebsm']['vrot_rota_codigo_visual'] = isset($this->data['Recebsm']['vrot_rota_codigo_visual']) ? $this->data['Recebsm']['vrot_rota_codigo_visual'] : NULL; ?>
<?php echo $this->Javascript->codeBlock("
	$('#ver_rota_link').click(function(e){
		e.preventDefault();
		if($('#RecebsmVrotRotaCodigo').val() != ''){
			var url = '/portal/rotas/mapa?rota_codigo='+$('#RecebsmVrotRotaCodigo').val()+'&edit=false';
			$(this).attr('href',url);
			return open_popup(this,520,520);
		}
		alert('Selecione uma rota');

	});

	$(document).on('click','.novo-destino, .novo-destino-remove',function(){
		closeGerarRota();
	})
	$(document).on('change','.destino input[name*=\'[refe_codigo]\'], .destino input[name*=\'[tipo_parada]\'], #RecebsmRefeCodigoOrigemVisual, #RecebsmMonitorarRetorno',function(){
		closeGerarRota();
    });
		
	function gerarRota(){
		var refe_codigo_origem = $('#RecebsmRefeCodigoOrigem').val();
		var destinos = $('#destino input[name*=\'[refe_codigo]\']').closest('table');
		var refe_referencias = [];
		var qtd_itinerario = 8;
		if($('#RecebsmMonitorarRetorno').is(':checked')){
			qtd_itinerario = 7;
		}
		for(var i = 0; i < destinos.length && i < qtd_itinerario; i++){
			if($(destinos[i]).find('input[name*=\'[refe_codigo]\']').val() != ''){
				refe_referencias.push({
					'data-index': $(destinos[i]).attr('data-index'),
					'refe_codigo': $(destinos[i]).find('input[name*=\'[refe_codigo]\']').val(),
					'tipo_parada': $(destinos[i]).find('select[name*=\'[tipo_parada]\']').val(),
					'dataFinal': $(destinos[i]).find('input[name*=\'[dataFinal]\']').val() + ' ' + $(destinos[i]).find('input[name*=\'[horaFinal]\']').val(),
				});
			}
		}
		refe_referencias.sort(function(a,b){ return a.dataFinal > b.dataFinal; });
		
		if(refe_codigo_origem != ''){
			var refe_codigos = '';
			var tipos_parada = '';

			refe_codigos += refe_codigo_origem + '|';
			if(refe_referencias.length > 0){
				var valida = true;
				for(var i = 0; i < refe_referencias.length; i++){
					refe_codigos += refe_referencias[i].refe_codigo + '|';
					if(refe_referencias[i].tipo_parada != '' && refe_referencias[i].tipo_parada != undefined ){
						tipos_parada += refe_referencias[i].tipo_parada + '|';
					}else{
						valida = false;
						break;
					}
				}
				if(valida){
					$('#gerar-rota').html('Aguarde...').slideDown();
					bloquearDiv($('#gerar-rota'));
					var monitora_retorno = false;
					if($('#RecebsmMonitorarRetorno').is(':checked')){
						refe_codigos += refe_codigo_origem + '|';
						tipos_parada += '5|';
						monitora_retorno = true;
					}
					var url = baseUrl + 'solicitacoes_monitoramento/gerar_rota/{$this->data['Recebsm']['codigo_cliente']}/'+refe_codigos+'/'+tipos_parada+'/'+monitora_retorno+'/'+Math.random();
					$('#gerar-rota').load(url,function(){
						$('#gerar-rota').unblock();					
					});
				}else{
					alert('Informe o tipo do itinerário');
				}
			}else{
				alert('Informe o itinerário');
			}
		}else{
			alert('Informe o alvo de origem');
		}
	}

	function closeGerarRota(){
		$('#gerar-rota').html('').slideUp();
	}

	function recarrega_rotas(){
		if(jQuery('#gerar-rota .alert-success').length > 0){
			closeGerarRota();
		}
	}

	function valida_campos_rota_com_intinerario(referencias) {
					if($('#RecebsmVrotRotaCodigo').val() != '') {
						validar_campo_autocomplete('#RecebsmVrotRotaCodigo','#RecebsmVrotRotaCodigoVisual','rota');
							if ($('#RecebsmVrotRotaCodigo').val()!='') {
								var itinerario_preenchido = false;
								$.each($('.referencia'), function(){
									itinerario_preenchido = itinerario_preenchido || ($(this).val()!='');
								});
								campo_rota = document.getElementById('RecebsmVrotRotaCodigo');
								if (itinerario_preenchido) {
									var validado = (valida_itinerario_rota(campo_rota)=='1'?true:false);
								} else {
									validado = true;
								}
							if (validado) {
						        $('#RecebsmVrotRotaCodigoVisual').removeClass('form-error').parent().removeClass('error').find('#lbl-error').remove();                
							} else {
						        $('#RecebsmVrotRotaCodigoVisual').removeClass('form-error').parent().removeClass('error').find('#lbl-error').remove();                
						        $('#RecebsmVrotRotaCodigoVisual').addClass('form-error').parent().addClass('error inline').append('<div id=\'lbl-error\' class=\'help-block error-message\'><br><br>Pontos da rota estão divergentes com o Itinerário.</div>');
								validar_campo_autocomplete('#RecebsmVrotRotaCodigo','#RecebsmVrotRotaCodigoVisual','rota');
							}
						}		
					}
	}

	jQuery(document).ready(function() {
		$('.referencia').change(function() {
			valida_campos_rota_com_intinerario(this);
		});
	});



"); ?> 