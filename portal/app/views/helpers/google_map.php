<?php

class GoogleMapHelper extends Helper{

	var $helpers = array('Form', 'Number', 'Session', 'Html', 'Javascript', 'Text');
	
	var $defaultId = "map_canvas";
	var $defaultWidth = "500px";
	var $defaultHeight = "500px";
	var $defaultStyle = "style";
	var $defaultLatitude = -23.61369;
	var $defaultLongitude = -46.63997;
	var $defaultZoom = 12;
	var $rota_gerada = false;

	private $map_id;
	private $canvas_id;

	public function setup($options = null){
		if($options != null){
			extract($options);
		}

		if(!isset($id)) $id = $this->defaultId;
		if(!isset($div_id)) $div_id = $id;
		if(!isset($width)) $width = $this->defaultWidth;
		if(!isset($height)) $height = $this->defaultHeight; 
		if(!isset($style)) $style = $this->defaultStyle;
		if(!isset($separate_code)) $separate_code = false;
		if(!isset($draw_div)) $draw_div = true;

		$map = '';
		if ($draw_div) {
			$map = "<script src='https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry&key=".Ambiente::getGoogleKey(1)."'></script>";
			$map .= "<div id='$id' style='width:$width; height:$height; $style'></div>";
		}
		$this->map_id = $id;
		$this->canvas_id = $div_id;

		if ($separate_code) $map .= $this->iniciaBloco($options);

		return $map;
	}

	public function iniciaBloco($options = null) {
		if($options != null){
			extract($options);
		}

		if(!isset($id)) $id = $this->map_id;
		if(!isset($div_id)) $div_id = $this->canvas_id;
		if(!isset($width)) $width = $this->defaultWidth;
		if(!isset($height)) $height = $this->defaultHeight; 
		if(!isset($style)) $style = $this->defaultStyle;
		if(!isset($zoom)) $zoom = $this->defaultZoom;

		if (!isset($latitude_center))  $latitude_center = $this->defaultLatitude;
		if (!isset($longitude_center)) $longitude_center = $this->defaultLongitude;
		if (!isset($resizable)) $resizable = false;

		$rota = "
			<script type='text/javascript'>
				var {$id};
				var obj = document.getElementById('{$div_id}');
				$(document).ready(function(){
					var map_coords = new google.maps.LatLng({$latitude_center},{$longitude_center});
					var mapOptions = { zoom: {$zoom}, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
					function resize(){
						$('#".$div_id."').css({'height':$(window).height()});
						google.maps.event.trigger({$id}, 'resize');
					}
					function initMap() {
						
					{$id} = new google.maps.Map(obj, mapOptions);
		";

		if ($resizable) {
			$rota .= "
				$('.alert').delay(4000).animate({opacity:0,height:0,margin:0},function(){jQuery(this).slideUp()});	
				resize();
				
				$(window).resize(function(){
					resize();
				});
				
			";
		}
		$rota .= "
				}
			initMap();
		";
		return $rota;
	}

	public function encerraBloco($options = null) {
		if($options != null){
			extract($options);
		}

		if(!isset($id)) $id = $this->map_id;
		if(!isset($width)) $width = $this->defaultWidth;
		if(!isset($height)) $height = $this->defaultHeight; 
		if(!isset($style)) $style = $this->defaultStyle;
		if(!isset($zoom)) $zoom = $this->defaultZoom;

		if (!isset($latitude_center))  $latitude_center = $this->defaultLatitude;
		if (!isset($longitude_center)) $longitude_center = $this->defaultLongitude;

		$rota = "
				});
			</script>
		";	

		return $rota;
	}

	public function carregaArraysArmazenamento($arrays) {
		$txt_ret = '';
		foreach ($arrays as $seq => $nome_array) {
			$txt_ret .= "var ".$nome_array." = [];\n";

		}
		return $txt_ret;
	}

	public function map($options = null){
		if($options != null)
			extract($options);

		if(!isset($id)) $id = $this->defaultId;
		if(!isset($latitude)) $latitude = $this->defaultLatitude;
		if(!isset($longitude)) $longitude = $this->defaultLongitude;

		$map = $this->setup($options);
		$map .="
			<script type='text/javascript'>
			$(document).ready(function(){
				var {$id};
				var waypts = [];

				function initialize() {
					 var center = new google.maps.LatLng({$latitude}, {$longitude});
					 var mapOptions = {
						   zoom:7,
						   center: center
					 }
					 {$id} = new google.maps.Map(document.getElementById('{$id}'), mapOptions);
				}

				google.maps.event.addDomListener(window, 'load', initialize);
			});
		</script>";

		return $map;
	}

	function criaRota($options = null, $seq_rota = null){
		$wp = null;
		$txt_leg = null;
		$marker_events = null;

		if($options != null){
			extract($options);
		}
		if(!isset($setup)) $setup = true;
		if(!isset($id)) {
			if ($setup) {
				$id = $this->defaultId;
			} else {
				$id = $this->map_id;
			}
		}
		if(!isset($latitude)) $latitude = $this->defaultLatitude;
		if(!isset($longitude)) $longitude = $this->defaultLongitude;
		if(!isset($exibe_pontos)) $exibe_pontos = true;
		if(!isset($cor)) $cor = null;
		if(!isset($inicio) || !isset($fim)) return false;
		if(isset($waypoints)){
			$key = 0;
			foreach($waypoints as $key => $waypoint){
				if (!empty($desvios[$key])) {
					$wp .= "desvios[$key] = [];\n";
					foreach($desvios[$key] as $keyDesvio => $desvio){
						$wp .= "desvios[$key].push({location: new google.maps.LatLng({$desvio['latitude']},{$desvio['longitude']}), stopover: false} );\n";
					}
				}
				$wp .= "wpts.push({location: new google.maps.LatLng({$waypoint['latitude']},{$waypoint['longitude']}), stopover: true} );";
			}
			$key++;
			if (!empty($desvios[$key])) {
				$wp .= "desvios[$key] = [];\n";
				foreach($desvios[$key] as $keyDesvio => $desvio){
					$wp .= "desvios[$key].push({location: new google.maps.LatLng({$desvio['latitude']},{$desvio['longitude']}), stopover: false} );\n";
				}
			}
		} else {
			$key = 0;
			if (!empty($desvios[$key])) {
				$wp .= "desvios[$key] = [];\n";
				foreach($desvios[$key] as $keyDesvio => $desvio){
					$wp .= "desvios[$key].push({location: new google.maps.LatLng({$desvio['latitude']},{$desvio['longitude']}), stopover: false} );\n";
				}
			}
		}

		if($edit){
			$marker_events = "
							function addListener(objeto) {
								google.maps.event.addListener(objeto, 'directions_changed', function() {
									var response = objeto.getDirections();
									carrega_steps(response.routes[0].legs);
								});	
							}
			";
			
		}

		if ($setup) {
			$rota = $this->setup($options);
			$rota .= 
				"<script type='text/javascript'>
					$(document).ready(function(){
			";
		} else $rota = '';
			if ($setup || !$this->rota_gerada) {
				$rota .= "
					var request = {};
					var inicio = new google.maps.LatLng({$inicio['latitude']}, {$inicio['longitude']});
					var fim = new google.maps.LatLng({$fim['latitude']}, {$fim['longitude']});
					if ({$id}==null || {$id}==undefined) {
						{$id} = null;
					}
					var waypts = [];
					var wpts = [];
					var desvios = [];
					var legs = [];
					var leg;
					var directionsDisplay;
					var directionsService = new google.maps.DirectionsService();
					var ds = [];
					var infowindow = new google.maps.InfoWindow();
					var map_coords = new google.maps.LatLng({$inicio['latitude']}, {$inicio['longitude']});
					var i;
					var edit;

					var markers_rota = [];
					var pinShadow = new google.maps.MarkerImage('http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
						new google.maps.Size(40, 37),
						new google.maps.Point(0, 0),
						new google.maps.Point(12, 35)
					);		  
				";
				
			}

			$rota .= "".($edit?$marker_events:'')."";
			$rota .= "
					function initialize{$seq_rota}() {
						request = {};
						waypts = [];
						wpts = [];
						desvios = [];
						ds = [];
						i = 0;
						edit = ".($edit?'true':'false').";

						inicio = new google.maps.LatLng({$inicio['latitude']}, {$inicio['longitude']});
						fim = new google.maps.LatLng({$fim['latitude']}, {$fim['longitude']});
						map_coords = new google.maps.LatLng({$inicio['latitude']}, {$inicio['longitude']});

						directionsDisplay = new google.maps.DirectionsRenderer({
							draggable:".($edit?'true':'false').",
							markerOptions: {
								draggable: false
							},
							suppressMarkers: ".($exibe_pontos?'false':'true')."
							".(!empty($cor)?", polylineOptions: {strokeColor: '#".$cor."'}":'')."
						});
						var center = new google.maps.LatLng({$latitude}, {$longitude});
						var mapOptions = {
							zoom:7,
							center: map_coords
						}
						".($setup ? "{$id} = new google.maps.Map(document.getElementById('{$id}'), mapOptions);" : "")."
						directionsDisplay.setMap({$id});

						criaRota{$seq_rota}(wpts, desvios);

					}

					function criaRota{$seq_rota}(wpts, desvios){
						var waypoints;

						".$wp."
					   
						waypoints = wpts;
						
						if ((desvios.length>0) || (edit==false)) {

							delete_steps();
							if (waypoints.length>0) {
								var dest = waypoints[0].location;
							} else {
								var dest = fim;
							}

							request = {
								origin: inicio,
								destination: dest,
								waypoints: desvios[0],
								travelMode: google.maps.TravelMode.DRIVING,
								provideRouteAlternatives: true
							};
							directionsService.route(request, function(response, status) {
								if (status == google.maps.DirectionsStatus.OK) {
									ds[0] = new google.maps.DirectionsRenderer({
										draggable:".($edit?'true':'false').",
										markerOptions: {
											visible: false
										}
										".(!empty($cor)?", polylineOptions: {strokeColor: '#".$cor."'}":'')."
									});
									ds[0].setDirections(response);
									ds[0].setMap({$id});
									".($edit?'addListener(ds[0]);':'')."
									add_steps(response.routes[0].legs);
									
									markers_rota[0] = new google.maps.Marker({
										position: response.routes[0].legs[0].start_location, 
										animation: google.maps.Animation.DROP,
										map: {$id},
										icon: 'https://mts.googleapis.com/vt/icon/name=icons/spotlight/spotlight-waypoint-a.png&text=O&psize=14&color=ff333333&ax=44&ay=48&scale=1',
										shadow: pinShadow,
										visible: ".($exibe_pontos?'true':'false')."
									});
									
								}
							});
							for (i=0;i<waypoints.length;i++) {

								if (i==waypoints.length-1) {
									var dest = fim;
								} else {
									var dest = waypoints[i+1].location;
								}
								request = {
									origin: waypoints[i].location,
									destination: dest,
									waypoints: desvios[i+1],
									travelMode: google.maps.TravelMode.DRIVING,
									provideRouteAlternatives: true
								};
								directionsService.route(request, function(response, status) {
									if (status == google.maps.DirectionsStatus.OK) {
										ds[i] = new google.maps.DirectionsRenderer({
											draggable:".($edit?'true':'false').",
											markerOptions: {
												visible: false,
												draggable: false
											}
											".(!empty($cor)?", polylineOptions: {strokeColor: '#".$cor."'}":'')."
										});
										ds[i].setDirections(response);
										ds[i].setMap({$id});
										".($edit?'addListener(ds[i]);':'')."
										add_steps(response.routes[0].legs);
										
										markers_rota[i] = new google.maps.Marker({
											position: response.routes[0].legs[0].start_location, 
											animation: google.maps.Animation.DROP,
											map: {$id},
											icon: 'https://mts.googleapis.com/vt/icon/name=icons/spotlight/spotlight-waypoint-b.png&text=E&psize=14&color=ff333333&ax=44&ay=48&scale=1',
											shadow: pinShadow,
											visible: ".($exibe_pontos?'true':'false')."
										});
									}
								});
							}
							if (fim!=inicio) {
								markers_rota[i+1] = new google.maps.Marker({
									position: fim, 
									animation: google.maps.Animation.DROP,
									map: {$id},
									icon: 'https://mts.googleapis.com/vt/icon/name=icons/spotlight/spotlight-waypoint-b.png&text=E&psize=14&color=ff333333&ax=44&ay=48&scale=1',
									shadow: pinShadow,
									visible: ".($exibe_pontos?'true':'false')."
								});
							};
							request = {
								origin: inicio,
								destination: fim,
								waypoints: waypoints,
								travelMode: google.maps.TravelMode.DRIVING,
								provideRouteAlternatives: true
							};
							
							directionsService.route(request, function(response, status) {
								if (status == google.maps.DirectionsStatus.OK) {
									var dsCenter = new google.maps.DirectionsRenderer({
											draggable:false,
											suppressMarkers: true,
											suppressPolylines: true
									});
									dsCenter.setDirections(response);
									dsCenter.setMap({$id});
								}
							});
							
						} else {

							request = {
								origin: inicio,
								destination: fim,
								waypoints: waypoints,
								travelMode: google.maps.TravelMode.DRIVING,
								provideRouteAlternatives: true
							};
							".($edit?'addListener(directionsDisplay);':'')."							
							directionsService.route(request, function(response, status) {
								if (status == google.maps.DirectionsStatus.OK) {
									directionsDisplay.setDirections(response);
									carrega_steps(response.routes[0].legs);
								}
							});
						}
						
					}
				";
				if ($setup || !$this->rota_gerada) {
					$rota .= "

					function delete_steps(){
						if (window.parent.legs) window.parent.legs.length=0;
					}

					function add_steps(legs){
						if (!window.parent.legs) window.parent.legs = [];
						for (var l = 0; l < legs.length; l++){
							window.parent.legs.push(legs[l]);
						}

					}

					function carrega_steps(legs){
						window.parent.legs = legs;
					}

					function clear_markers() {
						for(var i=0; i<waypts.length; i++){
							waypts[i].setMap(null);
						}
						waypts = waypts.filter(function(wpt){ return wpt.map != null;});
					}
					";
				}

		if ($setup) {
			$rota .= "
					google.maps.event.addDomListener(window, 'load', initialize{$seq_rota});  
				});
			</script>
			";
		}
		$this->rota_gerada = true;
		return $rota;
	}

	function carregaRotasGeradas($qtd_rotas) {
		$fnc_rota = '';
		for ($i = 0;$i<$qtd_rotas;$i++) {
			if ($i>0) $fnc_rota.="; ";
			$fnc_rota.="initialize{$i}()";
		}
		$rota = "
			google.maps.event.addDomListener(window, 'load', function() { $fnc_rota } );  
		";
		return $rota;

	}

	function carregaSteps($options = null){
		$wp = null;

		if($options != null){
			extract($options);
		}
		if(!isset($id)) $id = $this->defaultId;
		if(!isset($latitude)) $latitude = $this->defaultLatitude;
		if(!isset($longitude)) $longitude = $this->defaultLongitude;
		if(!isset($inicio) || !isset($fim)) return false;
		if(isset($waypoints)){
			foreach($waypoints as $waypoint){
				$wp .= "waypts.push({
					location:new google.maps.LatLng({$waypoint['latitude']},{$waypoint['longitude']}),
					stopover:true
					});";
			}
		}

		$url = "http://maps.googleapis.com/maps/api/directions/json?origin={$inicio['latitude']},{$inicio['longitude']}&destination={$fim['latitude']},{$fim['longitude']}&sensor=false";

		$jsonfile = file_get_contents($url);

		return $jsonfile;
	}

	function criaMarcadores($options = null){
		$wp = null;
		$txt_leg = null;
		$marker_events = null;

		if($options != null){
			extract($options);
		}
		if(!isset($id)) $id = $this->map_id;
		if(!isset($latitude)) $latitude = $this->defaultLatitude;
		if(!isset($longitude)) $longitude = $this->defaultLongitude;
		if(!isset($setup)) $setup = true;
		if(!isset($marcadores)) return false;

		$txt_ret = "
			var posicao;
			var marker_title = '';
			var marker_image = '';
			var map_marker;
		";


		foreach($marcadores as $key => $marcador){
			if (!empty($marcador['latitude']) && !empty($marcador['longitude'])) {
				$txt_ret .= "posicao = new google.maps.LatLng(".$marcador['latitude'].", ".$marcador['longitude'].");\n";
				if (!empty($marcador['titulo'])) $txt_ret .= "marker_title = '".$marcador['titulo']."';\n";
				if (!empty($marcador['icone'])) $txt_ret .= 'marker_image = new google.maps.MarkerImage("'.$marcador['icone'].'", new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(15, 15), new google.maps.Size(30, 30));'."\n";
				$txt_ret .= "map_marker = new google.maps.Marker({ position: posicao, map: {$id}, title: marker_title".((!empty($marcador['icone']))?', icon: marker_image':'').((!empty($marcador['zIndex']))?', zIndex: '.$marcador['zIndex']:'')." });\n";

				if (!empty($marcador['array_armazenamento'])) {
					$txt_ret .= "{$marcador['array_armazenamento']}.push(map_marker);\n";
				}

			}
		}

		return $txt_ret;
	}

	function criaRetangulos($options = null){
		$wp = null;
		$txt_leg = null;
		$marker_events = null;

		if($options != null){
			extract($options);
		}
		if(!isset($id)) $id = $this->map_id;
		if(!isset($latitude)) $latitude = $this->defaultLatitude;
		if(!isset($longitude)) $longitude = $this->defaultLongitude;
		if(!isset($setup)) $setup = true;
		if(!isset($retangulos)) return false;

		$txt_ret = "
			var rectangle;
			var path;
			var poly;
		";

		foreach($retangulos as $seq_retangulo => $retangulo){

			if (!isset($retangulo['cor_borda'])) $retangulo['cor_borda'] = 'AAAAEE';
			if (!isset($retangulo['cor_preenchimento'])) $retangulo['cor_preenchimento'] = 'AAAAFF';
			if (!empty($retangulo['refe_poligono'])) {
				$txt_ret .= "path = new google.maps.MVCArray;";
				$points = explode(",", $retangulo['refe_poligono']);
				foreach ($points as $key => $point) {
					$point = explode(" ", $point);
					$txt_ret .= "path.insertAt({$key}, new google.maps.LatLng({$point['0']}, {$point['1']}));";
				}
				$txt_ret .= "
					poly = new google.maps.Polygon({
						strokeColor: '#{$retangulo['cor_borda']}',
						strokeOpacity: 0.6,
						strokeWeight: 2,
						fillColor: '#{$retangulo['cor_preenchimento']}'
					});
					poly.setMap({$id});
					poly.setPaths(new google.maps.MVCArray([path]));
				";
			} else {
				$txt_ret .= "
				rectangle = new google.maps.Rectangle({
					strokeColor: '#".$retangulo['cor_borda']."',
					strokeOpacity: 0.6,
					strokeWeight: 2,
					fillColor: '#".$retangulo['cor_preenchimento']."',
					fillOpacity: 0.50,
					map: {$id},
					bounds: new google.maps.LatLngBounds(
				";
				foreach($retangulo['posicoes'] as $key => $posicao){
					if (!empty($posicao['latitude']) && !empty($posicao['longitude'])) {
						if ($key>0) $txt_ret .= ",";
						$txt_ret .= "new google.maps.LatLng(".$posicao['latitude'].", ".$posicao['longitude'].")";

					}
				}
				$txt_ret .= "
					)
				});
				";
			}
		}

		return $txt_ret;
	}   

	function criaLinhas($options = null){
		$wp = null;
		$txt_leg = null;
		$marker_events = null;

		if($options != null){
			extract($options);
		}
		if(!isset($id)) $id = $this->map_id;
		if(!isset($latitude)) $latitude = $this->defaultLatitude;
		if(!isset($longitude)) $longitude = $this->defaultLongitude;
		if(!isset($setup)) $setup = true;
		if(!isset($linhas)) return false;

		$txt_ret = "
			var linha;
			var posicoes = [];
		";

		foreach($linhas as $seq_linha => $linha){
			if (!isset($linha['cor'])) $linha['cor'] = 'FF0000';
			$txt_ret .= "posicoes = [];\n";
			foreach($linha['posicoes'] as $key => $posicao){
				if (!empty($posicao['latitude']) && !empty($posicao['longitude'])) {
					$txt_ret .= "
						posicoes.push(new google.maps.LatLng(".$posicao['latitude'].", ".$posicao['longitude']."));
					";

				}
			}		 
			$txt_ret .= "
				 var rota_config = new google.maps.Polyline({
					path: posicoes,
					strokeColor: '#".$linha['cor']."',
					strokeOpacity: 1.0,
					strokeWeight: 2,
					geodesic: true
				});
				rota_config.setMap({$id});
			";
			if (!empty($marcador['array_armazenamento'])) {
				$txt_ret .= "{$marcador['array_armazenamento']}.push(rota_config);\n";
			}		 

		}


		return $txt_ret;
	}   

	public function desenhaMapa($options) {

		if($options != null){
			extract($options);
		}

		if(!isset($id)) $id = $this->defaultId;
		if(!isset($width)) $width = $this->defaultWidth;
		if(!isset($height)) $height = $this->defaultHeight; 
		if(!isset($style)) $style = $this->defaultStyle;
		if(!isset($zoom)) $zoom = $this->defaultZoom;
		if(!isset($latitude_center))  $latitude_center = $this->defaultLatitude;
		if(!isset($longitude_center)) $longitude_center = $this->defaultLongitude;
		if(!isset($separate_code)) $separate_code = true;
		if(!isset($draw_div)) $draw_div = false;
		if(!isset($resizable)) $resizable = false;

		$ret = '';

		$ret .= $this->setup($options);

		if (isset($marcadores) && is_array($marcadores) && count($marcadores)>0) {
			$ret .= $this->criaMarcadores($options);
		}

		if (isset($retangulos) && is_array($retangulos) && count($retangulos)>0) {
			$ret .= $this->criaRetangulos($options);
		}

		if (isset($linhas) && is_array($linhas) && count($linhas)>0) {
			$ret .= $this->criaLinhas($options);
		}

		if (isset($rotas) && is_array($rotas) && count($rotas)>0) {
			foreach ($rotas as $key_rota => $dados_rota) {
				$ret .= $this->criaRota($dados_rota,$key_rota);
			}
			$ret .= $this->carregaRotasGeradas(count($rotas));
		}

		$ret .= $this->encerraBloco($options);

		return $ret;
	}

	public function mapaEdicaoAlvo($options) {
		if($options != null){
			extract($options);
		}

		if(!isset($id)) $id = $this->defaultId;
		if(!isset($width)) $width = $this->defaultWidth;
		if(!isset($height)) $height = $this->defaultHeight; 
		if(!isset($style)) $style = $this->defaultStyle;
		if(!isset($zoom)) $zoom = $this->defaultZoom;
		if(!isset($latitude_center))  $latitude_center = $this->defaultLatitude;
		if(!isset($longitude_center)) $longitude_center = $this->defaultLongitude;
		if(!isset($separate_code)) $separate_code = true;
		if(!isset($draw_div)) $draw_div = false;
		if(!isset($resizable)) $resizable = false;
		if(!isset($title)) $title = '';
		if(!isset($polygon_string)) $polygon_string = '[]';
		if(!isset($rectangle)) $rectangle = array('lat_min' => 0, 'lng_min' => 0, 'lat_max' => 0, 'lng_max' => 0);
		$polygon = json_encode(array(array($latitude_center, $longitude_center)));
		if(isset($polygon_string)) {
			$polygon = split(",", $polygon_string);
			foreach ($polygon as $key => $points) {
				$polygon[$key] = split(" ", $points);
			}
			$polygon = json_encode($polygon);
		}

		$map = $this->setup($options);
		$map .="
			<script type='text/javascript'>
				$(document).ready(function(){
					var {$id};
					var marker_title = '{$title}';
					var data = {$polygon};
					var poly;
					var path = new google.maps.MVCArray;
					var markers = [];
					var latitude = $latitude_center;
					var longitude = $longitude_center;
					var range = parseFloat($('#{$range_input}').val()) / 1000;
					var bounds = new google.maps.LatLngBounds(
					  new google.maps.LatLng({$rectangle['lat_min']}, {$rectangle['lng_min']}),
					  new google.maps.LatLng({$rectangle['lat_max']}, {$rectangle['lng_max']})
					);
					var latRange = ({$rectangle['lat_min']} - {$rectangle['lat_max']})/2;
					var lngRange = ({$rectangle['lng_min']} - {$rectangle['lng_max']})/2;
					var rectangle;

					function initialize() {
						var center = new google.maps.LatLng(latitude, longitude);
						var mapOptions = {
							zoom:15,
							center: center
						}
						{$id} = new google.maps.Map(document.getElementById('{$id}'), mapOptions);
						var map_coords = new google.maps.LatLng(latitude, longitude);
						var map_marker = new google.maps.Marker({ position: map_coords, map: {$id}, title: marker_title + ' - Lat:' + map_coords.lat() + ' Long:' + map_coords.lng(), draggable: true });
						path.insertAt(path.length, map_marker.position);
						markers.push(map_marker);
						poly = new google.maps.Polygon({
							strokeColor: '#AAAAEE',
							strokeOpacity: 0.6,
						    strokeWeight: 2,
						    fillColor: '#5555FF'
					    });
					    poly.setMap({$id});
					    poly.setPaths(new google.maps.MVCArray([path]));

					    rectangle = new google.maps.Rectangle({
							strokeColor: '#AAAAEE',
							strokeOpacity: 0.6,
							strokeWeight: 2,
							fillColor: '#AAAAFF',
							fillOpacity: 0.50,
							map: {$id},
							bounds: bounds
						});

						for (var pointToAdd = 1; pointToAdd < data.length - 1; pointToAdd++) {
					    	var point = {latLng: new google.maps.LatLng(data[pointToAdd][0], data[pointToAdd][1])};
					    	addPoint(point);
					    }

						google.maps.event.addListener({$id}, 'click', addPoint);
						google.maps.event.addListener(map_marker, 'dragend', function() {
								for (var i = 0, I = markers.length; i < I && markers[i] != map_marker; ++i);
									path.setAt(i, map_marker.getPosition());
								extractPath();
							}
						);
						google.maps.event.addListener(map_marker, 'drag', function() {
							updateMarkerPosition(map_marker.getPosition());
						});

						$('#{$latitude_input}').blur(function(){
							resetPath();
						});

						$('#{$longitude_input}').blur(function(){
							resetPath();
						});

						$('#{$range_input}').blur(function(){
							resetPath();
						});
					}

					function resetPath() {
						latitude = parseFloat($('#{$latitude_input}').val());
						longitude = parseFloat($('#{$longitude_input}').val());
						range = parseFloat($('#{$range_input}').val()) / 1000;
						var lat_min = latitude - range / 111.319;
						var lat_max = latitude + range / 111.319;
						var lng_min = longitude - range / 111.319;
						var lng_max = longitude + range / 111.319;
						latRange = (lat_min - lat_max)/2;
						lngRange = (lng_min - lng_max)/2;
						for (var i = (markers.length -1); i > 0; i--) {
							markers[i].setMap(null);
							markers.splice(i, 1);
							path.removeAt(i);
						}
						var map_coords = new google.maps.LatLng(latitude , longitude);
						markers[0].setPosition(map_coords);
						path.setAt(i, map_coords);
						{$id}.setCenter(map_coords);
						extractPath();
					}

					function extractPath() {
						var latLng;
						var stringPath = '';
						var minLat, maxLat, minLng, maxLng;
						if (markers.length>1) {
							for (var i = 0; i < markers.length; ++i) {
								latLng = markers[i].getPosition();
								stringPath += latLng.lat() + ' ' + latLng.lng() + ',';
								if ((typeof(minLat) == 'undefined') || (latLng.lat() < minLat)) minLat = latLng.lat();
								if ((typeof(maxLat) == 'undefined') || (latLng.lat() > maxLat)) maxLat = latLng.lat();
								if ((typeof(minLng) == 'undefined') || (latLng.lng() < minLng)) minLng = latLng.lng();
								if ((typeof(maxLng) == 'undefined') || (latLng.lng() > maxLng)) maxLng = latLng.lng();
							}
							latLng = markers[0].getPosition();
							stringPath += latLng.lat() + ' ' + latLng.lng();
							bounds = new google.maps.LatLngBounds(
								new google.maps.LatLng(minLat,minLng),
								new google.maps.LatLng(maxLat,maxLng)
							);
						} else {
							latLng = markers[0].getPosition();
							bounds = new google.maps.LatLngBounds(
								new google.maps.LatLng(latLng.lat()+latRange,latLng.lng()+lngRange),
								new google.maps.LatLng(latLng.lat()-latRange,latLng.lng()-lngRange)
							);
						}
						rectangle.setBounds(bounds);
						rectangle.setVisible((markers.length == 1));
						window.parent.document.getElementById('{$polygon_input}').value  = stringPath;
					}

					function addPoint(event) {
						path.insertAt(path.length, event.latLng);

						var marker = new google.maps.Marker({
							position: event.latLng,
							map: {$id},
							draggable: true
						});
						markers.push(marker);
						marker.setTitle('#' + path.length + ' - Lat:' + event.latLng.lat() + ' Long:' + event.latLng.lng());

						google.maps.event.addListener(marker, 'click', function() {
							marker.setMap(null);
							for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
							markers.splice(i, 1);
							path.removeAt(i);
							extractPath();
						});

						google.maps.event.addListener(marker, 'dragend', function() {
								for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
								path.setAt(i, marker.getPosition());
								marker.setTitle('#' + i + ' - Lat:' + marker.getPosition().lat() + ' Long:' + marker.getPosition().lng());
								extractPath();
							}
						);
						extractPath();
					}

					function updateMarkerPosition(latLng) {	  
						latitude = latLng.lat();
						longitude = latLng.lng()
						$('#{$latitude_input}').val(latitude);
						$('#{$longitude_input}').val(longitude);
						bounds = new google.maps.LatLngBounds(
							new google.maps.LatLng(latLng.lat()+latRange,latLng.lng()+lngRange),
					  		new google.maps.LatLng(latLng.lat()-latRange,latLng.lng()-lngRange)
						);
						rectangle.setBounds(bounds);
					}

					google.maps.event.addDomListener(window, 'load', initialize);
				});
			</script>";

		return $map;
	}

	public function mapaFornecedores($options) {
		if($options != null){
			extract($options);
		}

		if(!isset($id)) $id = $this->defaultId;
		if(!isset($width)) $width = $this->defaultWidth;
		if(!isset($height)) $height = $this->defaultHeight; 
		if(!isset($style)) $style = $this->defaultStyle;
		if(!isset($zoom)) $zoom = $this->defaultZoom;
		if(!isset($latitude_center))  $latitude_center = $this->defaultLatitude;
		if(!isset($longitude_center)) $longitude_center = $this->defaultLongitude;
		if(!isset($separate_code)) $separate_code = true;
		if(!isset($draw_div)) $draw_div = false;
		if(!isset($resizable)) $resizable = false;
		if(!isset($title)) $title = '';
		if(!isset($polygon_string)) $polygon_string = '[]';
		$polygon = json_encode(array(array($latitude_center, $longitude_center)));
		if(isset($polygon_string)) {
			$polygon = split(",", $polygon_string);
			foreach ($polygon as $key => $points) {
				$polygon[$key] = split(" ", $points);
			}
			$polygon = json_encode($polygon);
		}
		$map = $this->setup($options);
		$map .="
			<script type='text/javascript'>
				$(document).ready(function(){
					var {$id};
					var marker_title = '{$title}';
					var data = {$polygon};
					var poly;
					var path = new google.maps.MVCArray;
					var markers = [];
					var latitude = $latitude_center;
					var longitude = $longitude_center;
					var range = parseFloat($('#{$range_input}').val()) / 1000;
					var bounds = new google.maps.LatLngBounds(
					  new google.maps.LatLng({$rectangle['lat_min']}, {$rectangle['lng_min']}),
					  new google.maps.LatLng({$rectangle['lat_max']}, {$rectangle['lng_max']})
					);
					var latRange = ({$rectangle['lat_min']} - {$rectangle['lat_max']})/2;
					var lngRange = ({$rectangle['lng_min']} - {$rectangle['lng_max']})/2;
					var rectangle;

					function initialize() {
						var center = new google.maps.LatLng(latitude, longitude);
						var mapOptions = {
							zoom:15,
							center: center
						}
						{$id} = new google.maps.Map(document.getElementById('{$id}'), mapOptions);
						var map_coords = new google.maps.LatLng(latitude, longitude);
						var map_marker = new google.maps.Marker({ position: map_coords, map: {$id}, title: marker_title + ' - Lat:' + map_coords.lat() + ' Long:' + map_coords.lng(), draggable: true });
						path.insertAt(path.length, map_marker.position);
						markers.push(map_marker);
						poly = new google.maps.Polygon({
							strokeColor: '#AAAAEE',
							strokeOpacity: 0.6,
						    strokeWeight: 2,
						    fillColor: '#5555FF'
					    });
					    poly.setMap({$id});
					    poly.setPaths(new google.maps.MVCArray([path]));

					    rectangle = new google.maps.Rectangle({
							strokeColor: '#A5BFFF',
							strokeOpacity: 0.6,
							strokeWeight: 2,
							fillColor: '#E0E9FF',
							fillOpacity: 0.50,
							map: {$id},
							bounds: bounds
						});

						for (var pointToAdd = 1; pointToAdd < data.length - 1; pointToAdd++) {
					    	var point = {latLng: new google.maps.LatLng(data[pointToAdd][0], data[pointToAdd][1])};
					    	addPoint(point);
					    }

						google.maps.event.addListener({$id}, 'click', addPoint);
						google.maps.event.addListener(map_marker, 'dragend', function() {
								for (var i = 0, I = markers.length; i < I && markers[i] != map_marker; ++i);
									path.setAt(i, map_marker.getPosition());
								extractPath();
							}
						);
						google.maps.event.addListener(map_marker, 'drag', function() {
							updateMarkerPosition(map_marker.getPosition());
						});

						$('#{$latitude_input}').blur(function(){
							resetPath();
						});

						$('#{$longitude_input}').blur(function(){
							resetPath();
						});

						$('#{$range_input}').blur(function(){
							resetPath();
						});
					}

					function resetPath() {
						latitude = parseFloat($('#{$latitude_input}').val());
						longitude = parseFloat($('#{$longitude_input}').val());
						range = parseFloat($('#{$range_input}').val()) / 1000;
						var lat_min = latitude - range / 111.319;
						var lat_max = latitude + range / 111.319;
						var lng_min = longitude - range / 111.319;
						var lng_max = longitude + range / 111.319;
						latRange = (lat_min - lat_max)/2;
						lngRange = (lng_min - lng_max)/2;
						for (var i = (markers.length -1); i > 0; i--) {
							markers[i].setMap(null);
							markers.splice(i, 1);
							path.removeAt(i);
						}
						var map_coords = new google.maps.LatLng(latitude , longitude);
						markers[0].setPosition(map_coords);
						path.setAt(i, map_coords);
						{$id}.setCenter(map_coords);
						extractPath();
					}

					function extractPath() {
						var latLng;
						var stringPath = '';
						var minLat, maxLat, minLng, maxLng;
						if (markers.length>1) {
							for (var i = 0; i < markers.length; ++i) {
								latLng = markers[i].getPosition();
								stringPath += latLng.lat() + ' ' + latLng.lng() + ',';
								if ((typeof(minLat) == 'undefined') || (latLng.lat() < minLat)) minLat = latLng.lat();
								if ((typeof(maxLat) == 'undefined') || (latLng.lat() > maxLat)) maxLat = latLng.lat();
								if ((typeof(minLng) == 'undefined') || (latLng.lng() < minLng)) minLng = latLng.lng();
								if ((typeof(maxLng) == 'undefined') || (latLng.lng() > maxLng)) maxLng = latLng.lng();
							}
							latLng = markers[0].getPosition();
							stringPath += latLng.lat() + ' ' + latLng.lng();
							bounds = new google.maps.LatLngBounds(
								new google.maps.LatLng(minLat,minLng),
								new google.maps.LatLng(maxLat,maxLng)
							);
						} else {
							latLng = markers[0].getPosition();
							bounds = new google.maps.LatLngBounds(
								new google.maps.LatLng(latLng.lat()+latRange,latLng.lng()+lngRange),
								new google.maps.LatLng(latLng.lat()-latRange,latLng.lng()-lngRange)
							);
						}
						rectangle.setBounds(bounds);
						rectangle.setVisible((markers.length == 1));
						window.parent.document.getElementById('{$polygon_input}').value  = stringPath;
					}

					function addPoint(event) {
						path.insertAt(path.length, event.latLng);

						var marker = new google.maps.Marker({
							position: event.latLng,
							map: {$id},
							draggable: true
						});
						markers.push(marker);
						marker.setTitle('#' + path.length + ' - Lat:' + event.latLng.lat() + ' Long:' + event.latLng.lng());

						google.maps.event.addListener(marker, 'click', function() {
							marker.setMap(null);
							for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
							markers.splice(i, 1);
							path.removeAt(i);
							extractPath();
						});

						google.maps.event.addListener(marker, 'dragend', function() {
								for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
								path.setAt(i, marker.getPosition());
								marker.setTitle('#' + i + ' - Lat:' + marker.getPosition().lat() + ' Long:' + marker.getPosition().lng());
								extractPath();
							}
						);
						extractPath();
					}

					function updateMarkerPosition(latLng) {	  
						latitude = latLng.lat();
						longitude = latLng.lng()
						$('#{$latitude_input}').val(latitude);
						$('#{$longitude_input}').val(longitude);
						bounds = new google.maps.LatLngBounds(
							new google.maps.LatLng(latLng.lat()+latRange,latLng.lng()+lngRange),
					  		new google.maps.LatLng(latLng.lat()-latRange,latLng.lng()-lngRange)
						);
						rectangle.setBounds(bounds);
					}

					google.maps.event.addDomListener(window, 'load', initialize);
				});
			</script>";
		return $map;
	}

	public function mapaClientes($options) {
		if($options != null){
			extract($options);
		}

		if(!isset($id)) $id = $this->defaultId;
		if(!isset($width)) $width = $this->defaultWidth;
		if(!isset($height)) $height = $this->defaultHeight; 
		if(!isset($style)) $style = $this->defaultStyle;
		if(!isset($zoom)) $zoom = $this->defaultZoom;
		if(!isset($latitude_center))  $latitude_center = $this->defaultLatitude;
		if(!isset($longitude_center)) $longitude_center = $this->defaultLongitude;
		if(!isset($separate_code)) $separate_code = true;
		if(!isset($draw_div)) $draw_div = false;
		if(!isset($resizable)) $resizable = false;
		if(!isset($title)) $title = '';
		if(!isset($polygon_string)) $polygon_string = '[]';
		$polygon = json_encode(array(array($latitude_center, $longitude_center)));
		if(isset($polygon_string)) {
			$polygon = split(",", $polygon_string);
			foreach ($polygon as $key => $points) {
				$polygon[$key] = split(" ", $points);
			}
			$polygon = json_encode($polygon);
		}
		$map = $this->setup($options);
		$map .="
			<script type='text/javascript'>
				$(document).ready(function(){
					var {$id};
					var marker_title = '{$title}';
					var data = {$polygon};
					var poly;
					var path = new google.maps.MVCArray;
					var markers = [];
					var latitude = $latitude_center;
					var longitude = $longitude_center;
					var range = parseFloat($('#{$range_input}').val()) / 1000;
					var bounds = new google.maps.LatLngBounds(
					  new google.maps.LatLng({$rectangle['lat_min']}, {$rectangle['lng_min']}),
					  new google.maps.LatLng({$rectangle['lat_max']}, {$rectangle['lng_max']})
					);
					var latRange = ({$rectangle['lat_min']} - {$rectangle['lat_max']})/2;
					var lngRange = ({$rectangle['lng_min']} - {$rectangle['lng_max']})/2;
					var rectangle;

					function initialize() {
						var center = new google.maps.LatLng(latitude, longitude);
						var mapOptions = {
							zoom:15,
							center: center
						}
						{$id} = new google.maps.Map(document.getElementById('{$id}'), mapOptions);
						var map_coords = new google.maps.LatLng(latitude, longitude);
						var map_marker = new google.maps.Marker({ position: map_coords, map: {$id}, title: marker_title + ' - Lat:' + map_coords.lat() + ' Long:' + map_coords.lng(), draggable: true });
						path.insertAt(path.length, map_marker.position);
						markers.push(map_marker);
						poly = new google.maps.Polygon({
							strokeColor: '#AAAAEE',
							strokeOpacity: 0.6,
						    strokeWeight: 2,
						    fillColor: '#5555FF'
					    });
					    poly.setMap({$id});
					    poly.setPaths(new google.maps.MVCArray([path]));

					    rectangle = new google.maps.Rectangle({
							strokeColor: '#A5BFFF',
							strokeOpacity: 0.6,
							strokeWeight: 2,
							fillColor: '#E0E9FF',
							fillOpacity: 0.50,
							map: {$id},
							bounds: bounds
						});

						for (var pointToAdd = 1; pointToAdd < data.length - 1; pointToAdd++) {
					    	var point = {latLng: new google.maps.LatLng(data[pointToAdd][0], data[pointToAdd][1])};
					    	addPoint(point);
					    }

						google.maps.event.addListener({$id}, 'click', addPoint);
						google.maps.event.addListener(map_marker, 'dragend', function() {
								for (var i = 0, I = markers.length; i < I && markers[i] != map_marker; ++i);
									path.setAt(i, map_marker.getPosition());
								extractPath();
							}
						);
						google.maps.event.addListener(map_marker, 'drag', function() {
							updateMarkerPosition(map_marker.getPosition());
						});

						$('#{$latitude_input}').blur(function(){
							resetPath();
						});

						$('#{$longitude_input}').blur(function(){
							resetPath();
						});

						$('#{$range_input}').blur(function(){
							resetPath();
						});
					}

					function resetPath() {
						latitude = parseFloat($('#{$latitude_input}').val());
						longitude = parseFloat($('#{$longitude_input}').val());
						range = parseFloat($('#{$range_input}').val()) / 1000;
						var lat_min = latitude - range / 111.319;
						var lat_max = latitude + range / 111.319;
						var lng_min = longitude - range / 111.319;
						var lng_max = longitude + range / 111.319;
						latRange = (lat_min - lat_max)/2;
						lngRange = (lng_min - lng_max)/2;
						for (var i = (markers.length -1); i > 0; i--) {
							markers[i].setMap(null);
							markers.splice(i, 1);
							path.removeAt(i);
						}
						var map_coords = new google.maps.LatLng(latitude , longitude);
						markers[0].setPosition(map_coords);
						path.setAt(i, map_coords);
						{$id}.setCenter(map_coords);
						extractPath();
					}

					function extractPath() {
						var latLng;
						var stringPath = '';
						var minLat, maxLat, minLng, maxLng;
						if (markers.length>1) {
							for (var i = 0; i < markers.length; ++i) {
								latLng = markers[i].getPosition();
								stringPath += latLng.lat() + ' ' + latLng.lng() + ',';
								if ((typeof(minLat) == 'undefined') || (latLng.lat() < minLat)) minLat = latLng.lat();
								if ((typeof(maxLat) == 'undefined') || (latLng.lat() > maxLat)) maxLat = latLng.lat();
								if ((typeof(minLng) == 'undefined') || (latLng.lng() < minLng)) minLng = latLng.lng();
								if ((typeof(maxLng) == 'undefined') || (latLng.lng() > maxLng)) maxLng = latLng.lng();
							}
							latLng = markers[0].getPosition();
							stringPath += latLng.lat() + ' ' + latLng.lng();
							bounds = new google.maps.LatLngBounds(
								new google.maps.LatLng(minLat,minLng),
								new google.maps.LatLng(maxLat,maxLng)
							);
						} else {
							latLng = markers[0].getPosition();
							bounds = new google.maps.LatLngBounds(
								new google.maps.LatLng(latLng.lat()+latRange,latLng.lng()+lngRange),
								new google.maps.LatLng(latLng.lat()-latRange,latLng.lng()-lngRange)
							);
						}
						rectangle.setBounds(bounds);
						rectangle.setVisible((markers.length == 1));
						window.parent.document.getElementById('{$polygon_input}').value  = stringPath;
					}

					function addPoint(event) {
						path.insertAt(path.length, event.latLng);

						var marker = new google.maps.Marker({
							position: event.latLng,
							map: {$id},
							draggable: true
						});
						markers.push(marker);
						marker.setTitle('#' + path.length + ' - Lat:' + event.latLng.lat() + ' Long:' + event.latLng.lng());

						google.maps.event.addListener(marker, 'click', function() {
							marker.setMap(null);
							for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
							markers.splice(i, 1);
							path.removeAt(i);
							extractPath();
						});

						google.maps.event.addListener(marker, 'dragend', function() {
								for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
								path.setAt(i, marker.getPosition());
								marker.setTitle('#' + i + ' - Lat:' + marker.getPosition().lat() + ' Long:' + marker.getPosition().lng());
								extractPath();
							}
						);
						extractPath();
					}

					function updateMarkerPosition(latLng) {	  
						latitude = latLng.lat();
						longitude = latLng.lng()
						$('#{$latitude_input}').val(latitude);
						$('#{$longitude_input}').val(longitude);
						bounds = new google.maps.LatLngBounds(
							new google.maps.LatLng(latLng.lat()+latRange,latLng.lng()+lngRange),
					  		new google.maps.LatLng(latLng.lat()-latRange,latLng.lng()-lngRange)
						);
						rectangle.setBounds(bounds);
					}

					google.maps.event.addDomListener(window, 'load', initialize);
				});
			</script>";
		return $map;
	}
}
?>