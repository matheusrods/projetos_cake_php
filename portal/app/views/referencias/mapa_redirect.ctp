<!DOCTYPE html>
<html>
<head>
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
			function extractPath() {
				var latLng;
				var stringPath = "";
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
				window.parent.document.getElementById('TRefeReferenciaRefePoligono').value  = stringPath;
			}

			function addPoint(event) {
				path.insertAt(path.length, event.latLng);

				var marker = new google.maps.Marker({
					position: event.latLng,
					map: map,
					draggable: true
				});
				markers.push(marker);
				marker.setTitle("#" + path.length);

				google.maps.event.addListener(marker, 'click', function() {
					marker.setMap(null);
						for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
							markers.splice(i, 1);
						path.removeAt(i);
						extractPath();
					}
				);

				google.maps.event.addListener(marker, 'dragend', function() {
						for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
							path.setAt(i, marker.getPosition());
						extractPath();
					}
				);
				extractPath();
			}

			var latitude = <?php echo @$latitude; ?>;
			var longitude = <?php echo @$longitude; ?>;
			var marker_title = '<?php echo @$marker_title; ?>';

			var map_coords = new google.maps.LatLng(latitude, longitude);
			var map_config = { zoom: 12, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
			var map = new google.maps.Map(document.getElementById('canvas_mapa'), map_config);
			var map_marker = new google.maps.Marker({ position: map_coords, map: map, title: marker_title, draggable: true });
			var poly;
			var path = new google.maps.MVCArray;
			var markers = [];

			path.insertAt(path.length, map_marker.position);
			markers.push(map_marker);
			poly = new google.maps.Polygon({
		      strokeWeight: 3,
		      fillColor: '#5555FF'
		    });
		    poly.setMap(map);
		    poly.setPaths(new google.maps.MVCArray([path]));
		    google.maps.event.addListener(map, 'click', addPoint);

			google.maps.event.addListener(map_marker, 'drag', function() {
				updateMarkerPosition(map_marker.getPosition());
			});
			google.maps.event.addListener(map_marker, 'dragend', function() {
					for (var i = 0, I = markers.length; i < I && markers[i] != map_marker; ++i);
						path.setAt(i, map_marker.getPosition());
					extractPath();
				}
			);
			

			var bounds = new google.maps.LatLngBounds(
			  new google.maps.LatLng(<?php echo @$area['refe_latitude_min']; ?>, <?php echo @$area['refe_longitude_min']; ?>),
			  new google.maps.LatLng(<?php echo @$area['refe_latitude_max']; ?>, <?php echo @$area['refe_longitude_max']; ?>)
			);

			var rectangle = new google.maps.Rectangle({
				strokeColor: '#AAAAEE',
				strokeOpacity: 0.6,
				strokeWeight: 2,
				fillColor: '#AAAAFF',
				fillOpacity: 0.50,
				map: map,
				bounds: bounds
			});

			var latRange = <?php echo (@$area['refe_latitude_min'] - @$area['refe_latitude_max'])/2; ?>;
			var lngRange = <?php echo (@$area['refe_longitude_min'] - @$area['refe_longitude_max'])/2; ?>;

			function updateMarkerPosition(latLng) {	  
				window.parent.document.getElementById('TRefeReferenciaRefeLatitude').value  = latLng.lat();
				window.parent.document.getElementById('TRefeReferenciaRefeLongitude').value = latLng.lng();					
				bounds = new google.maps.LatLngBounds(
					new google.maps.LatLng(latLng.lat()+latRange,latLng.lng()+lngRange),
			  		new google.maps.LatLng(latLng.lat()-latRange,latLng.lng()-lngRange)
				);
				rectangle.setBounds(bounds);
			}
		} else {
			var html  = '<div class="alert alert-error">';
			html += '	<h4>Erro na api do googlemaps</h4>';
			html += '	<h5>Verifique as susas configurações de proxy, ou se o script da api está carregado corretamente.</h5>';
			html += '</div>';
			$("#canvas_mapa").html(html);
		}
	});
</script>