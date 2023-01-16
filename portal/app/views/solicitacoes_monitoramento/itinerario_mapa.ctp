<ul class="nav nav-tabs">
	<li class="active"><a href="#rota" data-toggle="tab">Mapa</a></li>
	<li><a href="#rota_info" data-toggle="tab">Informações da Rota</a></li>
</ul>

<?php //$this->data['TRotaRota']['itinerario'] = array();?>

<div class="tab-content ">
	<div class="tab-pane active" id="rota">
					<!DOCTYPE html>
					<html>
					<head>
					<title>Detalhe do Itinerário no Mapa</title>
					<style type="text/css">
						body{
							font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
							margin: 0 !important;
							padding: 0 !important;
						}
						#canvas_mapa {
							margin: 0 !important;
							padding: 0 !important;
						}
						#canvas_mapa img,
						.google-maps img {
						  max-width: none;
						}						
						a.historico {
							color: #08C;
							text-decoration: none;
							position: absolute;
							z-index: 100;
							bottom: 5px;
							left: 69px;
						}
						.message {
							width: 100%;
							position: absolute;
							z-index: 100;
							top: 0;
						}
						.alert {
							padding: 8px 35px 8px 14px;
							margin-bottom: 20px;
							text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
							background-color: #FCF8E3;
							border: 1px solid #FBEED5;
							-webkit-border-radius: 4px;
							-moz-border-radius: 4px;
							border-radius: 4px;
							color: #C09853;
							font-size: 13px;
						}
						.controles-poi{
							position: absolute;
							z-index: 100;
							top: 5px;
							right: 33%;		
						}
						.sm_comboio{
							position: absolute;
							z-index: 100;
							top: 5px;
							right: 26%;		
						}	
						.alert p {
							margin: 0;
						}
						.legenda {
							display: inline;
							white-space:nowrap
						}
					</style>
					<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js"></script>
					<script src="https://maps.googleapis.com/maps/api/js?sensor=false&v=3.x"></script>
					<div class='row-fluid inline'>
						<?php echo $this->BForm->input('alvos_compartilhados',array('label'=> FALSE, 'empty'=> 'Alvos Compartilhados','options'=> $classes, 'class'=> 'controles-poi','value'=> isset($classe) ? $classe : NULL));?>
					</div>
					<div class='row-fluid inline'>
						<div class='span12'>
							<div class="legenda"><img src="/portal/img/marker/truck.png" width="24"/>&nbsp;Posição atual do veículo</div>
							<?php if (empty($authUsuario['Usuario']['codigo_cliente'])): ?>
								<div class="legenda"><img src="/portal/img/marker/bullet-blue.png"/>Macro enviada</div>
							<?php endif ?>
							<div class="legenda"><img src="/portal/img/marker/bullet-green.png"/>Local onde veículo parou</div>
							<?php if ($exibe_pontos_admin): ?>
								&nbsp;&nbsp;<div class="legenda"><img src="/portal/img/marker/bullet-yellow.png" />&nbsp;Parada Irregular</div>
							<?php endif;?>
							<div class="legenda"><img src="/portal/img/marker/home_6.png" width="24"/>&nbsp;Origem</div>
							<div class="legenda"><img src="/portal/img/marker/home.png" width="24"/>&nbsp;Alvo</div>
							<?php if( !empty($dados_comboio) ): ?>	
								&nbsp;&nbsp;<div class="legenda"><img src="/portal/img/marker/icone_bandeira.png"/>Comboio</div>
							<?php endif;?>
							<?php if( !empty($dados_comboio) ): ?>	
								<div class='sm_comboio'>
								<?php echo $this->BForm->input('sm_comboio', array('type'=>'checkbox','label'=> 'Comboio', 'id'=> 'sm_comboio', 'checked'=>'checked'));?>
								</div>
							<?php endif;?>
							<?php if ($exibe_pontos_admin): ?>
								&nbsp;&nbsp;<div class="legenda"><img src="/portal/img/marker/flag-red.png" width="24"/>&nbsp;Parada Proibida</div>
								&nbsp;&nbsp;<div class="legenda"><img src="/portal/img/marker/flag-yellow.png" width="24"/>&nbsp;Área de Risco</div>
								&nbsp;&nbsp;<div class="legenda"><img src="/portal/img/marker/flag-blue.png" width="24"/>&nbsp;Ponto Permitido</div>
							<?php endif;?>
							&nbsp;&nbsp;
							<div class="legenda"><div style="width: 25px; height:3px; background-color: #68A0BF; display: inline-block"></div>&nbsp;Rota definida</div>
							&nbsp;&nbsp;
							<div class="legenda"><div style="width: 25px; height:3px; background-color: #FF0000; display: inline-block"></div>&nbsp;Rota realizada</div>
						</div>
					</div>
					<?php if(!empty($placa)): ?>
						<p>
						<?php echo $this->Html->link('Histórico de posições', array('controller'=>'veiculos', 'action'=>'historico_posicoes', $placa, preg_replace('/[-: ]/', '', $data_inicial), preg_replace('/[-: ]/', '', $data_final),$viag_viagem_completo['TViagViagem']['viag_codigo_sm']), array('class'=>'histdorico','onclick' => 'return open_popup(this);')); ?>
					</p></p>
					<?php endif; ?>
					<div class="row-fluid inline">
			 			<?php echo $this->BForm->input('rotas_historico',array('type'=>'checkbox','class' => 'input-large rotas-historico', 'label' => 'Histórico de rotas','checked' => false)) ?>
 					</div>
					<div class="message container">
						<?php echo $this->Buonny->flash(); ?>
					</div>
				    <div class="row-fluid" id="divMapaOut">
						<div id="canvas_mapa" ></div>
					</div>
			</head>
		</html>
	</div>	
    <div class="tab-pane" id="rota_info" >
    	<div class="row-fluid inline">
		<?php echo $this->BForm->input('TViagViagem.viag_codigo_sm', array('label' => 'SM', 'readonly' => true,  'class' => 'input-small', 'value' => $viag_viagem_completo['TViagViagem']['viag_codigo_sm'] )); ?>
		<?php echo $this->BForm->input('TTveiTipoVeiculo.tvei_descricao', array('label' => 'Tipo do Veículo', 'readonly' => true, 'class' => 'input-small', 'value' => $viag_viagem_completo['TTveiTipoVeiculo']['tvei_descricao'])); ?>
		<div class='control-group input'>
			<label>Placa</label>
			<?php 
				if( !Comum::isVeiculo($viag_viagem_completo['TVeicVeiculo']['veic_placa'])) {
					echo "REMONTA";
				}else
				{ 
					echo $this->Buonny->placa($viag_viagem_completo['TVeicVeiculo']['veic_placa'], $viag_viagem_completo['TViagViagem']['viag_data_cadastro'], (empty($viag_viagem_completo['TViagViagem']['viag_data_fim']) ? Date('d/m/Y H:i:s') : $viag_viagem_completo['TViagViagem']['viag_data_fim']) );
				}
			?>
		</div>
	</div>

		<?php if(!empty($this->data['TRotaRota']['rota_codigo'])): ?>
		    	<div class="row-fluid inline">
					<?php echo $this->BForm->input('TRotaRota.rota_descricao', array('label' => 'Descrição', 'readonly' => true, 'class' => 'input-xlarge', 'type' => 'text')); ?>
				</div>
				<h4>Origem</h4>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input('TRotaRota.refe_origem_descricao', array('label' => false, 'readonly' => true, 'class' => 'input-xlarge', 'type' => 'text')); ?>
				</div>
				<h4>Itinerario</h4>	
				<?php $incremento = 1;?>
				<?php foreach($this->data['TRotaRota']['itinerario'] as $key => $destino): ?>
					<div class="row-fluid inline">
						<div class="input numeracao-itinerario-rota"><?= $incremento?></div>
						<?php echo $this->BForm->input('TRotaRota.itinerario.'.$key, array('value' => $destino['descricao'],'label' => false, 'readonly' => true, 'class' => 'input-xlarge', 'type' => 'text')); ?>
						<?php echo $this->BForm->input("TRotaRota.tipo_parada.".$key, array('value' => $destino['tipo_entrega'],'class' => 'input-medium', 'options' => $tipo_parada, 'empty' => 'Tipo Itinerario','label' => false, 'disabled' => TRUE)) ?>
					</div>
					<?php $incremento++?>
				<?php endforeach; ?>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input('TRotaRota.rota_observacao', array('label' => 'Observação', 'readonly' => true, 'class' => 'input-xlarge', 'type' => 'textarea')); ?>
				</div>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input('TVrotViagemRota.vrot_previsao_valor_pedagio', array('label' => 'Valor do pedágio', 'readonly' => true, 'class' => 'input-small moeda numeric', 'type' => 'text')); ?>
					<?php echo $this->BForm->input('TVrotViagemRota.vrot_previsao_valor_combustivel', array('label' => 'Valor do combustível', 'readonly' => true, 'class' => 'input-small moeda numeric', 'type' => 'text')); ?>
				</div>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input('TVrotViagemRota.vrot_previsao_distancia', array('label' => 'Distância (km/h)', 'readonly' => true, 'class' => 'input-small numeric', 'type' => 'text')); ?>
					<?php echo $this->BForm->input('TVrotViagemRota.vrot_previsao_litros_combustivel', array('label' => 'Litros de combustível', 'readonly' => true, 'class' => 'input-small moeda numeric', 'type' => 'text')); ?>
				</div>
		<?php else: ?>
			<?php echo $this->BForm->error_menssage("SM não possui rota cadastrada") ?>
		<?php endif; ?>
    </div>
</div>

<?
    $codigo_sm = $this->params['pass'][0];
	echo $this->GoogleMap->desenhaMapa($options_mapa);
?>



<?php echo $this->Html->scriptBlock('var baseUrl = "'.$this->webroot.'";'); ?>
<?php echo $this->Buonny->link_js('comum'); ?>
<?php echo $this->Buonny->link_js('estatisticas') ?>
<?php echo $this->Buonny->link_css('app'); ?>
<?php echo $this->Buonny->link_css('fam-icons/cus-icons'); ?>
<?php echo $this->Buonny->link_js('jquery.blockUI'); ?>
<?php echo $this->Javascript->codeBlock("
	$(document).ready(function(){
		$('.rotas-historico').prop('checked', false);
	});
	var markers = [];
	var markers_comboio = [];
	var line_rota = [];
	var macro_comboio = [];

	var rotas_historico = [];
		function desenhaRota(inicio, fim, desvios, cor) {
			
			var request = {
				origin: inicio,
				destination: fim,
				waypoints: desvios,
				travelMode: google.maps.TravelMode.DRIVING,
				provideRouteAlternatives: true
			};
			var dService = new google.maps.DirectionsService();
			dService.route(request, function(response, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					rend = new google.maps.DirectionsRenderer({
						draggable:false,
						markerOptions: {
							visible: false
						},
						polylineOptions: {strokeColor: '#'+cor}
					});
					rend.setDirections(response);
					rend.setMap(map);
					rotas_historico.push(rend);
				}
			});			
		}
        function carregar_pontos_mapa(codigo_sm) {
            bloquearDiv($('#divMapaOut'));
        
            $.ajax({
                type: 'POST',
                url: baseUrl + 'solicitacoes_monitoramento/retorna_historico_rotas_sm/'+codigo_sm+'/'+ Math.random(),
                dataType: 'json',
                beforeSend: function() {
                    bloquearDiv($('#divMapaOut'));
                },
                success: function(data) {
                    obj = data;
                     
                    for(i=0;i<obj.length;i++) {
                    	var inicio = new google.maps.LatLng(obj[i].inicio.latitude, obj[i].inicio.longitude);
                    	var fim = new google.maps.LatLng(obj[i].fim.latitude, obj[i].fim.longitude);
                    	wptsI = [];
                    	desviosI = [];
                    	
                    	for(j=0;j<=obj[i].waypoints.length;j++) {
                   
                    		var way = obj[i].waypoints[j];
                   		
                    		if (way!=undefined) {
                    			wptsI.push(new google.maps.LatLng(way.latitude, way.longitude));
                    		}
                    	}
                    	if (obj[i].desvios!=undefined) {
	                    	for(j=0;j<=obj[i].desvios.length;j++) {
	                    		var leg = obj[i].desvios[j];
	                    		if (!isNaN(j) && leg!=undefined) {
		                    		desviosI[j] = [];
		                    		for(k=0;k<=leg.length;k++) {
		                    			if (!isNaN(k) && leg[k]!=undefined) {
		                    				var pointK = new google.maps.LatLng(leg[k].latitude, leg[k].longitude);
		                    				desviosI[j].push({location: pointK, stopover: false});
		                    			}
		                    		}
		                    		
		                    	}
	                    	}
	                    }
                    	
                    	if (wptsI.length>0) {
                    		for(j=0;j<=wptsI.length;j++) {
                    			fimWP = (j<wptsI.length ? wptsI[j] : fim);
                    			desenhaRota(inicio, fimWP, desviosI[j],obj[i].cor);
                    			inicio = fimWP;
                    		}
                    	} else {
                    		desenhaRota(inicio, fim, desviosI[0],obj[i].cor);

                    	}
                    }
                },
                complete: function() {
                    $('#divMapaOut').unblock();
                }
            });

        }

	$('.rotas-historico').change(function(){
        var checked = ($(this)[0].checked);
        if (checked) {
            carregar_pontos_mapa(".$codigo_sm.");
        } else {
            for (var j=rotas_historico.length-1;j>=0;j--) {
                rotas_historico[j].setMap(null);
                rotas_historico.pop();
            }
        }
		
		
	});

	$('.controles-poi').change(function(){
		var div = $('#canvas_mapa');
		bloquearDiv(div);		
		var classe_alvo = $(this).val();
		carregarAlvosComuns( classe_alvo );
	});
	function carregarAlvosComuns( classe ){
		var codigo_sm = ".$this->params['pass'][0].";
		var newwindow = window.open('/portal/solicitacoes_monitoramento/itinerario_mapa/'+ codigo_sm + '/'+ classe + '','_self','scrollbars=yes,top=0,left=0,width=1000,height=800');
	}
	$('#sm_comboio').change(function(){
		if ( $('#sm_comboio').is(':checked') ) {
			esconderComboio(true);
		} else {
			esconderComboio(false);
		}
	});
	function esconderComboio(ocultar){
		for(var i = 0; i < markers.length; i++){
			markers[i].setVisible(ocultar);
		}
		for(var i = 0; i < markers_comboio.length; i++){
			markers_comboio[i].setVisible(ocultar);
		}		
		for (i=0; i<line_rota.length; i++) {                           
			line_rota[i].setVisible(ocultar);
		}
		for (i=0; i<macro_comboio.length; i++) {
			macro_comboio[i].setVisible(ocultar);
		}
	}
  
", false);?>