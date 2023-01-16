<?php

class GeoPortalMapHelper extends Helper{
	
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

	public function setup($options = NULL){
		if($options != null){
			extract($options);
		}

		if(!isset($id))
			$id = $this->defaultId;
		if(!isset($div_id))
			$div_id = $id;
		if(!isset($width))
			$width = $this->defaultWidth;
		if(!isset($height))
			$height = $this->defaultHeight;
		if(!isset($style))
			$style = $this->defaultStyle;
		if(!isset($separate_code))
			$separate_code = false;
		if(!isset($draw_div))
			$draw_div = true;

		$bloco = "";
		// $bloco = "<script type='text/javascript' src='http://www.geoportal.com.br/Api_Js_v3/v3.js'></script>";
		$bloco .= "<div id='".$id."' style='width:".$width."; height:".$height.";  ".$style." ' ></div>";

		$bloco .= $this->iniciaBloco($options);
		
	    return $bloco;
	}
    public function iniciaBloco($options = null){
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

		if(empty($id)) $id = $this->defaultId;

		$key = Ambiente::getGeoPortalKey(0);
		$key = (!empty($key) ? $key : "");

		$ret = "
			<script type='text/javascript'>
				var Mapa;
				function inicio(){
					Mapa = new multispectral(".$longitude_center.", ".$latitude_center.", ".$zoom.", '".$id."', '".$key."', true, 'callbackClient'); 
				}

				function load(){}

				inicio();

		";

		return $ret;
    }
    public function encerraBloco($options = null){
    	$bloco = "</script>";

		return $bloco;
    }
    public function carregaArraysArmazenamento($arrays){

    }
    public function map(){

    }
    public function criaRota($options = null, $seq_rota = null){

    }
    public function criaRotaComPosicoes($options = null, $seq_rota = null, $posicoes = null){

    }
    public function carregaRotasGeradas($qtd_rotas){

    }
    public function carregaSteps($options = null){

    }
    public function criaMarcadoresPosicoesRota($posRota, $posicoes, $userCodigoPerfil = NULL){

    }
    public function criaArrayPosicoesRota($tipo = 1, $posRota = array()){

    }
	public function criaMarcadores($options = null){

	}
	public function criaRetangulos($options = null){

	}
	public function criaLinhas($options = null){

	}
	public function desenhaMapa($options){
		$ret = '';

		$ret .= $this->setup($options);

		$ret .= $this->encerraBloco($options);
		return $ret;
	}
	public function mapaEdicaoAlvo($options){

	}
	public function mapaEdicao($options){

	}

	/**
	 * [mapaFornecedores description]
	 * 
	 * mapa para desenhar o pin do fornecedor
	 * 
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
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

		$icon_azul = 'https://portal.rhhealth.com.br/portal/img/marker/blue.png';

		$map = $this->setup($options);
		$map .="
			function load() {
				Mapa.Client.addMarkerBallon('".$longitude_center."', '".$latitude_center."', '".$icon_azul."', 'PIN_ENDERECO', undefined, undefined, undefined, undefined, undefined, undefined, undefined, undefined, 'ErroCallback', true, undefined, undefined);

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

				Mapa.Client.removeMarker('', 'ErroCallback');

				Mapa.Client.addMarkerBallon(longitude, latitude, '".$icon_azul."', 'Endereco', undefined, undefined, undefined, undefined, undefined, undefined, undefined, undefined, 'ErroCallback', true, undefined, undefined);

				Mapa.Client.setCenter(longitude, latitude,'',".$zoom.");
			}";

		$map .= $this->encerraBloco($options);

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

		$icon_azul = 'https://portal.rhhealth.com.br/portal/img/marker/blue.png';

		$map = $this->setup($options);

		$map .= "
			function load() {
				Mapa.Client.addMarkerBallon('".$longitude_center."', '".$latitude_center."', '".$icon_azul."', 'PIN_ENDERECO', undefined, undefined, undefined, undefined, undefined, undefined, undefined, undefined, 'ErroCallback', true, undefined, undefined);

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

				Mapa.Client.removeMarker('', 'ErroCallback');

				Mapa.Client.addMarkerBallon(longitude, latitude, '".$icon_azul."', 'Endereco', undefined, undefined, undefined, undefined, undefined, undefined, undefined, undefined, 'ErroCallback', true, undefined, undefined);

				Mapa.Client.setCenter(longitude, latitude,'',".$zoom.");
			}
		";

		$map .= $this->encerraBloco($options);

		return $map;
	}


	/**
	 * [mapaFornecedores description]
	 * 
	 * localiza credenciado
	 * 
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public function localizaCredenciado($options) {
		
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

		$icon_vermelho	= 'https://portal.rhhealth.com.br/portal/img/marker/red-pushpin.png';
	    $icon_azul = 'https://portal.rhhealth.com.br/portal/img/marker/blue.png';
	    $radius = $raio * 1000;

	    // debug($options);

		$map = $this->setup($options);

		$map .="
			function load() {
				Mapa.Client.addMarkerCircle('CIRCLE', '".$longitude_center."', '".$latitude_center."', ".$radius.", '".$icon_vermelho."', undefined, undefined, '#E0E9FF', undefined, 'ErroCallback', false);

			";

		if(!empty($options['fornecedores'])) {

			//pega a localização dos fornecedores
			foreach($options['fornecedores'] as $key => $dado) {

				$marker_latitude = '';
				$marker_longitude = '';
				$marker_title = '';

				if($dado['FornecedorEndereco']['latitude'] && $dado['FornecedorEndereco']['longitude']) {
					$marker_latitude = $dado['FornecedorEndereco']['latitude'];
					$marker_longitude = $dado['FornecedorEndereco']['longitude'];
					
					$marker_title = 'Fornecedor: '. $dado['Fornecedor']['nome'] . "<br>";
					
					if( isset($dado['FornecedorContato']) ){
						foreach ($dado['FornecedorContato'] as $key_2 => $telefone ) {
							$marker_title .= "Telefone: ( ". substr($telefone, 0, 2) . "-" . substr($telefone, 2, 4) . "." . substr($telefone, 6, strlen($telefone)) ." )";
						}
					}
					
					//adiciona ao mapa
					$map .= "Mapa.Client.addMarkerHTMLBallon('".$marker_longitude."', '".$marker_latitude."', undefined, undefined, '".$icon_azul."', 'PIN_ENDERECO_AZUL_".$key."', '".$marker_title."', undefined, undefined, undefined, undefined, false, 'ErroCallback', false, undefined, false, 1, undefined);\n";


				}//fim if latitude

			}//fim foreach
		}

		$map .= "}";

		$map .= $this->encerraBloco($options);

		return $map;
	}//fim localiza_credenciado
}
?>