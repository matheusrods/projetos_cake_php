<?php echo $this->Buonny->flash(); ?>
<?php echo $this->Bajax->form('TRotaRota',array('autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'gerar_rota', $codigo_cliente, $refe_codigos, $tipos_parada, $monitora_retorno), 'callback' => 'recarrega_rotas', 'divupdate' => '#gerar-rota') ) ?>
<div style="float:left;width:310px;">
	<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente)) ?>
	<?php echo $this->BForm->hidden('rota_pess_oras_codigo_dono') ?>
	<?php echo $this->BForm->hidden('rota_coordenada') ?>
	<?php echo $this->BForm->hidden('rota_coordenadaspipe') ?>
	<?php echo $this->BForm->hidden('rota_desvios') ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('rota_codigo_externo', array('label' => 'Codigo Externo','type' => 'text','class' => 'input-mini just-number', 'maxlength' => 4)) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('rota_observacao', array('label' => 'Observação','type' => 'textarea','class' => 'input-xlarge')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('rota_descricao', array('label' => 'Descrição','type' => 'text','class' => 'input-xlarge')) ?>
	</div>
</div>
<div id="mapa" style="float:left;width:525px;height:525px;">
	<iframe id="canvas-mapa" src="/portal/rotas/mapa/<?php echo $refe_codigos ?>/true" width="100%" height="100%" style="border:none;margin:0px;padding:0px;"></iframe>
</div>
<div class="form-actions" style="clear:left;">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success salvar-form')); ?>
	<?= $html->link('Cancelar', 'javascript:closeGerarRota()', array('class' => 'btn')); ?>
</div>
<?php 
if(isset($this->data['rota_codigo']) && !empty($this->data['rota_codigo']) && isset($this->data['rota_descricao']) && !empty($this->data['rota_descricao'])){
	echo $this->Javascript->codeBlock('
		if(jQuery("#gerar-rota .alert-success").length > 0){
			$("#RecebsmVrotRotaCodigo").val("'.$this->data['rota_codigo'].'");
			$("#RecebsmVrotRotaCodigoVisual").val("'.$this->data['rota_descricao'].'");
		}
	');
}

echo $this->Javascript->codeBlock('

	function decode(encoded){
		var points=[ ]
		var index = 0, len = encoded.length;
		var lat = 0, lng = 0;
		while (index < len) {
			var b, shift = 0, result = 0;
			do {

				b = encoded.charAt(index++).charCodeAt(0) - 63;
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

	var legs = "";

	$(document).ready(function(){
		$(".salvar-form").click(function(e){
        	e.preventDefault();
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
        	$("#TRotaRotaGerarRotaForm").submit();
        });
	});
',false); ?>