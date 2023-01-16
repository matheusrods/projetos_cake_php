<?php echo $this->BForm->create('TRotaRota',array('url' => array('controller' => 'rotas', 'action' => 'editar_rota', $this->data['TRotaRota']['rota_codigo']),'type' => 'post') ) ?>
	<div style="float:left;width:475px;">
		<?php echo $this->BForm->hidden('Cliente.codigo_cliente', array('value' => $cliente['Cliente']['codigo'])) ?>
		<?php echo $this->BForm->hidden('TRotaRota.codigo_cliente', array('value' => $cliente['Cliente']['codigo'])) ?>
		<?php echo $this->BForm->hidden('TRotaRota.rota_codigo') ?>
		<?php echo $this->BForm->hidden('TRotaRota.rota_coordenada') ?>
		<?php echo $this->BForm->hidden('TRotaRota.rota_coordenadaspipe') ?>
		<?php echo $this->BForm->hidden('TRotaRota.rota_desvios') ?>
		<h4>Origem</h4>
		<div class='row-fluid inline'>
			<?php echo $this->Buonny->input_referencia($this, '#ClienteCodigoCliente', 'TRotaRota', 'refe_codigo_origem', false); ?>
			<?php echo $this->BForm->input('monitorar_retorno', array('label' => 'Monitorar retorno', 'type' => 'checkbox', 'disabled' => (!$usuario_administrador))) ?>	
		</div>
		<div id="itinerario">
			<table width="100%">
				<tr>
					<td>
						<h4>Itinerario</h4>	
					</td>
					<?php if ($usuario_administrador): ?>
						<td>
							<div class="actionbar-right">
								<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-destino', 'escape' => false)); ?>
							</div>
						</td>
					<?php endif; ?>
				</tr>
			</table>
			<div id="destino">
				<?php for($key = (isset($this->data['TRotaRota']['Itinerario']) ? count($this->data['TRotaRota']['Itinerario']) - 1 : 0); $key >= 0 ; $key--): ?>
					<div class='row-fluid inline destino' data-index="<?php echo $key ?>">
						<?php echo $this->Buonny->input_referencia($this, '#ClienteCodigoCliente', 'TRotaRota.Itinerario', 'refe_codigo_destino', $key); ?>
						<?php echo $this->BForm->input("TRotaRota.Itinerario.{$key}.tipo_parada", array('class' => 'input-medium', 'options' => $tipo_parada, 'empty' => 'Tipo Itinerario','label' => false, 'disabled' => (!$usuario_administrador))) ?>
						<?php if ($usuario_administrador): ?>
							<?php if($key > 0): ?>
								<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-destino-remove', 'escape' => false)); ?>
							<?php endif; ?>						
						<?php endif; ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('rota_ativo', array('class' => 'input-small', 'label' => 'Status', 'title' => 'Status', 'default'=> 'A', 'options'=>array('A'=>'Ativa', 'D'=>'Inativa'))) ?>
			<?php echo $this->BForm->input('rota_codigo_externo', array('label' => 'Código Externo','type' => 'text','class' => 'input-small', 'maxlength' => 4)) ?>
			<?php echo $this->BForm->input('rota_descricao', array('label' => 'Descrição','type' => 'text','class' => 'input-xlarge')) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('rota_observacao', array('label' => 'Observação','type' => 'textarea','class' => 'input-xlarge')) ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('rota_data_ultima_atualizacao_custos', array('label' => 'Data da ultima atualização', 'readonly' => true, 'class' => 'input-medium', 'type' => 'text')); ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('rota_previsao_valor_pedagio', array('label' => 'Valor do pedágio', 'readonly' => true, 'class' => 'input-small moeda', 'value' => $this->Buonny->moeda($this->data['TRotaRota']['rota_previsao_valor_pedagio'], array('nozero' => true, 'places' => 2)),'type' => 'text')); ?>
			<?php echo $this->BForm->input('rota_previsao_valor_combustivel', array('label' => 'Valor do combustível', 'readonly' => true, 'class' => 'input-small moeda', 'value' => $this->Buonny->moeda($this->data['TRotaRota']['rota_previsao_valor_combustivel'], array('nozero' => true, 'places' => 2)), 'type' => 'text')); ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('rota_previsao_distancia', array('label' => 'Distância (km/h)', 'readonly' => true, 'class' => 'input-small numeric', 'value' => $this->Buonny->moeda($this->data['TRotaRota']['rota_previsao_distancia'], array('nozero' => true, 'places' => 2)), 'type' => 'text')); ?>
			<?php echo $this->BForm->input('rota_previsao_litros_combustivel', array('label' => 'Litros de combustível', 'readonly' => true, 'class' => 'input-small moeda', 'value' => $this->Buonny->moeda($this->data['TRotaRota']['rota_previsao_litros_combustivel'], array('nozero' => true, 'places' => 2)), 'type' => 'text')); ?>
		</div>
	</div>
	<div id="mapa" style="float:left;width:525px;height:525px;">
		<iframe id="map_canvas" width="100%" height="100%" style="border:none;margin:0px;padding:0px;"></iframe>
	</div>
	<div class="form-actions" style="clear:left;">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success salvar-form')); ?>
		  <?= $html->link('Cancelar', array('controller' => 'rotas', 'action' => 'rotas'), array('class' => 'btn')); ?>
	</div>

<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	var legs = "";

	function bloqueiaReferencia() {
		$.each($(".referencia"), function(){
			$(this).prop( "disabled", true );
		});	
		$(".icon-search").each(function(){
			$(this).remove();
		});
	}

	function loadMapa(edita) {
		$("#map_canvas").attr("src","/portal/rotas/mapa/?rota_codigo='.$this->data['TRotaRota']['rota_codigo'].'&edit="+edita);
	}

    jQuery(document).ready(function(){
    	var contador_destino = $("div#destino .destino").length;
		numera_itinerario();


        setup_mascaras();
        //'.(!empty($this->data['TRotaRota']['Itinerario']) ? 'ver_mapa_rota();' : '$("#map_canvas").attr("src","/portal/rotas/mapa/?rota_codigo='.$this->data['TRotaRota']['rota_codigo'].'&edit=true");').'
		
	
       	'.(!$usuario_administrador ? "bloqueiaReferencia();" : "").'
       	loadMapa('.($usuario_administrador ? "true" : "false").');
		

        $("#TRotaRotaRefeCodigoOrigem").change(function(){
    		ver_mapa_rota();
        });
		$(document).on("change",".destino input[name*=\"[refe_codigo_destino]\"], #TRotaRotaMonitorarRetorno",function(){
    		ver_mapa_rota();
        });

		function ver_mapa_rota(){
			var refe_codigos = "";
			refe_origem = $("#TRotaRotaRefeCodigoOrigem").val();
			if($("#TRotaRotaMonitorarRetorno").is(":checked")){
				refe_destino = refe_origem;
			}else{
				refe_destino = $(".destino[data-index=0] input[name*=\"[refe_codigo_destino]\"]").val();
			}
			refe_codigos += refe_origem + "|";
			var refe_itinerario = $(".destino");
			if(refe_origem != "" && refe_destino != ""){
				var refe_codigos_itinerario = "";
				for(var i = 0; i < refe_itinerario.length && i < 8; i++){
					if($("#TRotaRotaMonitorarRetorno").is(":checked") || $(refe_itinerario[i]).attr("data-index") != 0){
						refe_itinerario_codigo = $(refe_itinerario[i]).find("input[name*=\"[refe_codigo_destino]\"]").val();
						if(refe_itinerario_codigo != ""){
							refe_codigos_itinerario += refe_itinerario_codigo + "|";
						}
					}
				}
				bloquearDiv($("#TRotaRotaEditarRotaForm"));
				refe_codigos += refe_codigos_itinerario;
				refe_codigos += refe_destino;
				if(refe_codigos != ""){
					var url = baseUrl + "rotas/mapa/"+refe_codigos+"/true";
					$("#map_canvas").attr("src",url);
					$("#TRotaRotaEditarRotaForm").unblock();
				}else{
					$("#TRotaRotaEditarRotaForm").unblock();
				}
			}else{
				var url = baseUrl + "rotas/mapa";
				$("#map_canvas").attr("src",url);
			}
			return false;
		}
        
    });

	function numera_itinerario(){
		var numeracao = 1;
		$(".numeracao-itinerario-rota").remove();
		$(".destino").each(function(){
			$(this).prepend("<div class=\"input numeracao-itinerario-rota\">"+numeracao+"</div>");
			numeracao++;
		});
	}

	function decode(encoded){

		// array that holds the points

		var points=[ ]
		var index = 0, len = encoded.length;
		var lat = 0, lng = 0;
		while (index < len) {
			var b, shift = 0, result = 0;
			do {

				b = encoded.charAt(index++).charCodeAt(0) - 63;//finds ascii                                                                                    //and substract it by 63
				result |= (b & 0x1f) << shift;
				shift += 5;
			} while (b >= 0x20);


			var dlat = ((result & 1) != 0 ? ~(result >> 1) : (result >> 1));
			lat += dlat;
			shift = 0;
			result = 0;
			do {
				b = encoded.charAt(index++).charCodeAt(0) - 63;
				result |= (b & 0x1f) << shift;
				shift += 5;
			} while (b >= 0x20);
			var dlng = ((result & 1) != 0 ? ~(result >> 1) : (result >> 1));
			lng += dlng;

			points.push({latitude:( lat / 1E5),longitude:( lng / 1E5)})  

		}
		return points
	}

', false);
	if ($usuario_administrador) {
		echo $this->Javascript->codeBlock('
		    jQuery(document).ready(function(){
		    	var contador_destino = $("div#destino .destino").length;

		        $(".salvar-form").click(function(e){
		        	e.preventDefault();
					bloquearDiv($("#TRotaRotaEditarRotaForm"));
		        	if(legs != ""){
			        	var latitudes = "";
			            var longitudes = "";
			            var latitudesPipe = "";
			            var longitudesPipe = "";
			            var desvios = "";
			        	for(var l = 0; l < legs.length; l++){
			                var steps = legs[l].steps;
			                for(var i = 0; i < steps.length; i++){
			                    latitudes += steps[i].start_location.lat() + "|";
			                    longitudes += steps[i].start_location.lng() + "|";
			                    var points = decode(steps[i].polyline.points);
			                    for(var id in points){
			                        var point = points[id];
			                        if (point.latitude!=undefined) latitudesPipe += point.latitude + "|";
			                        if (point.longitude!=undefined) longitudesPipe += point.longitude + "|";
			                    }
			                }
			                var waypoints = legs[l].via_waypoints;
			                for(var j = 0; j < waypoints.length; j++){
			                	desvios += String(l)+"|"+waypoints[j].lat()+"|"+waypoints[j].lng()+";";
			                }	                
			            }
			            latitudes += legs[legs.length-1].end_location.lat() + "|";
			            longitudes += legs[legs.length-1].end_location.lng() + "|";
						$("#TRotaRotaRotaCoordenada").val(latitudes+";"+longitudes);
						$("#TRotaRotaRotaCoordenadaspipe").val(latitudesPipe+";"+longitudesPipe);
						$("#TRotaRotaRotaDesvios").val(desvios);
			        }
		        	$("#TRotaRotaEditarRotaForm").submit();
		        });

		    	$(document).on("click","a.novo-destino",function(){
					var conteiner = $("div#destino");
					if($(".destino").length < 8){
						bloquearDiv($("#itinerario"));
						contador_destino++;

						$.ajax({
							url: baseUrl + "rotas/novo_destino/"+ (contador_destino-1) +"/"+ Math.random(),
							dataType: "html",
							success: function(data){
								conteiner.prepend(data);
								numera_itinerario();
								$("#itinerario").unblock();
							}
						});
					}else{
						alert("Não é possível adicionar mais ponto.");
					}
				});

				$(document).on("click","a.novo-destino-remove",function(){
					$(this).parents(".destino").remove();
					ver_mapa_rota();
					numera_itinerario();
					return false;
				});
		        
		        $(document).on("change","#TRotaRotaRefeCodigoOrigemVisual",function(){
		        	$("#TRotaRotaRefeCodigoOrigem").val("");
		        	$("#TRotaRotaRefeCodigoOrigem").change();
		        });

				$(document).on("change",".destino input[name*=\"[refe_codigo_destino_visual]\"]",function(){
		        	$(this).parent().find("input[name*=\"[refe_codigo_destino]\"]").val("");
		        	$(this).parent().find("input[name*=\"[refe_codigo_destino]\"]").change();
		        });
		    });

		', false);
	}
?>