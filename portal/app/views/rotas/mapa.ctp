<?php echo $this->Buonny->link_js('combined'); ?>
<?php echo $this->Javascript->codeBlock('
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
',false); ?>
<?php 
if(isset($this->data['mapa']) && $this->data['mapa']){
	echo $this->GoogleMap->map();
}else{
	if(!empty($latitudes) && !empty($longitudes)){
		$options = array(
			'inicio' => array(
				'latitude' => $latitudes[0], 
				'longitude' => $longitudes[0],
			),
			'fim' => array(
				'latitude' => end($latitudes),
				'longitude' => end($longitudes),
			),
			'edit' => $edit
		);
		for($i = 1; $i < count($latitudes) - 1; $i++){
			$options['waypoints'][] = array('latitude' => $latitudes[$i], 'longitude' => $longitudes[$i]);
		}
		$options['desvios'] = $desvios;
		//debug($options);
		echo $this->GoogleMap->criaRota($options);
	}elseif(isset($this->data['rota_codigo']) && !empty($this->data['rota_codigo'])){
		echo 'Rota não encontrada.';
	}
}
?>
