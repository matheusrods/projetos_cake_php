<!DOCTYPE html>
<html>
<head>
<title><?php echo isset($titulo)?$titulo:@$marker_title; ?></title>
<style type="text/css">
	body{
		margin: 0 !important;
		padding: 0 !important;
	}
	#canvas_mapa {
		margin: 0 !important;
		padding: 0 !important;
		position: inherit !important;
	}
</style>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
</head>

<body>

<div id="canvas_mapa"></div>

<script type="text/javascript">
	$(function(){
		
		resize();

		$(window).resize(function(){
			resize();
		});

		function resize(){
			$("#canvas_mapa").css({'height':$(window).height()});
		}

		if (typeof(window.google) != 'undefined') {
			var latitude = <?php echo @$latitude; ?>;
			var longitude = <?php echo @$longitude; ?>;
			var marker_title = '<?php echo @$marker_title; ?>';

			var map_coords = new google.maps.LatLng(latitude, longitude);
			var map_config = { zoom: 12, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
			var map = new google.maps.Map(document.getElementById('canvas_mapa'), map_config);
			var map_marker = new google.maps.Marker({ position: map_coords, map: map, title: marker_title });
		} else {
	        var html  = '<div class="alert alert-error">';
	        html += '    <h4>Erro na api do googlemaps</h4>';
	        html += '    <h5>Verifique as susas configurações de proxy, ou se o script da api está carregado corretamente.</h5>';
	        html += '</div>';
	        $("#canvas_mapa").html(html);
	    }
	});
</script>