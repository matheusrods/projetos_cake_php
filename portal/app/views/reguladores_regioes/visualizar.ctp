<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<?php echo $this->BForm->create('ReguladorRegiao', array('url' => array('controller' => 'reguladores_regioes','action' => 'editar', $this->passedArgs[0])));?>
<div class="row-fluid inline">
  <?php echo $this->BForm->hidden('codigo') ?>
  <?php echo $this->BForm->hidden('codigo_regulador',array('value'=>$this->data['ReguladorRegiao']['codigo_regulador'])) ?>
  <div style="width:500px;float:left">
      <div class="row-fluid inline">
        <?php echo $this->BForm->input('latitude', array('label' => 'Latitude', 'type' => 'text','class' => 'input-medium','disabled' => true)) ?>
        <?php echo $this->BForm->input('longitude', array('label' => 'Longitude', 'type' => 'text','class' => 'input-medium','disabled' => true)) ?>
        <?php echo $this->BForm->input('raio', array('label' => 'Raio (m)','class' => 'input-small just-number numeric','disabled' => true)) ?>
      </div>
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('cidade', array('label' => 'Cidade', 'class' => 'text-medium','disabled' => true)) ?>
      <?php echo $this->BForm->input('prioridade', array('label' => 'Prioridade','class' => 'input-small numeric','disabled' => true)) ?>
    </div>
  </div>
<div class='actionbar-right'>   
  <div id="map" style="background-color:'#E5E3DF';width:600px;height:510px;float:right"></div>
</div>
</div>
<div class="form-actions">
  <?= $html->link('Voltar', 'javascript:window.close()', array('class' => 'btn')); ?>
</div> 
<?php echo $this->BForm->end(); ?>
<? echo $javascript->codeBlock("setup_mascaras();setup_datepicker();");?>
<?= $this->Javascript->codeBlock('
$(document).ready(function(){   
    setup_time(); 
    var myLatLng = {lat: -9.000000, lng: -54.000000};
    raio_metros = retorna_metros_raio();

    $("#ReguladorRegiaoRaio").blur(function(){        
        renderiza_marcador_mapa($("#ReguladorRegiaoLatitude").val(),$("#ReguladorRegiaoLongitude").val(),calcula_raio($("#ReguladorRegiaoRaio").val()));
    });

    var raio = calcula_raio(raio_metros);
    initMap(myLatLng,raio);
    renderiza_marcador_mapa($( "#ReguladorRegiaoLatitude" ).val(),$( "#ReguladorRegiaoLongitude" ).val(),raio);

    $("#SinistroLatitude").blur(function(){
        renderiza_marcador_mapa($("#ReguladorRegiaoLatitude").val(),$("#ReguladorRegiaoLongitude").val(),calcula_raio($("#ReguladorRegiaoRaio").val()));
    });

    $("#SinistroLongitude").blur(function(){
        renderiza_marcador_mapa($("#ReguladorRegiaoLatitude").val(),$("#ReguladorRegiaoLongitude").val(),calcula_raio($("#ReguladorRegiaoRaio").val()));
    });

    function renderiza_marcador_mapa(latitude,longitude,raio){
        if(latitude != "" && longitude != ""){
            var myLatLng = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
            initMap(myLatLng,raio);
        }
    }
        
    function initMap(myLatLng,raio) {  
        var map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: {lat: myLatLng.lat, lng: myLatLng.lng}
        });        

        marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
        });

        rectangle = new google.maps.Rectangle({
            strokeColor: "#AAAAEE",
            strokeOpacity: 0.6,
            strokeWeight: 2,
            fillColor: "#AAAAFF",
            fillOpacity: 0.50,
            map: map,
            bounds: new google.maps.LatLngBounds(
                new google.maps.LatLng(myLatLng.lat-raio, myLatLng.lng-raio),
                new google.maps.LatLng(myLatLng.lat+raio, myLatLng.lng+raio)
            )
        });

        var geocoder = new google.maps.Geocoder();
        var infowindow = new google.maps.InfoWindow;

    }

    function atualiza_raio(latLng){        
        var raio_m = calcula_raio($("#ReguladorRegiaoRaio").val());
        bounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(latLng.lat()-raio_m, latLng.lng()-raio_m),
            new google.maps.LatLng(latLng.lat()+raio_m, latLng.lng()+raio_m)
        );
        rectangle.setBounds(bounds);
        return true;
    }

    function calcula_raio(raio_metros){
        var raio_latLgn = (raio_metros / 1000) / 111.319;
        return raio_latLgn;
    }

    function atualizar_campos_latLgn(latLng){
        $("#ReguladorRegiaoLatitude").val(latLng.lat());
        $("#ReguladorRegiaoLongitude").val(latLng.lng());
    }

    function geocodeAddress(geocoder, resultsMap) {
        var address = document.getElementById("ReguladorRegiaoLocalSinistro").value;
        geocoder.geocode({"address": address}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                resultsMap.setCenter(results[0].geometry.location);
                $("#ReguladorRegiaoCidade").val(results[0].address_components[4].long_name);
                $("#ReguladorRegiaoLatitude").val(results[0].geometry.location.lat());
                $("#ReguladorRegiaoLongitude").val(results[0].geometry.location.lng());
                var myLatLng = {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()};
                initMap(myLatLng,calcula_raio($("#ReguladorRegiaoRaio").val()));
            }
        });
    }


    function geocodeLatLng(geocoder, map, infowindow) {        
        var latlng = {lat: parseFloat($("#ReguladorRegiaoLatitude").val()), lng: parseFloat($("#ReguladorRegiaoLongitude").val())};      
        geocoder.geocode({"location": latlng}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                if (results[1]) {             
                    $("#ReguladorRegiaoLocalSinistro").val(results[0].formatted_address); 
                }
            }
        });
    }  


    function retorna_metros_raio(){
        if($("#ReguladorRegiaoRaio").val() != ""){
            raio_metros = $("#ReguladorRegiaoRaio").val(); 
        }else{
            raio_metros = 1500; 
        }
        return raio_metros;
    }


    
  
});'); 
?>
