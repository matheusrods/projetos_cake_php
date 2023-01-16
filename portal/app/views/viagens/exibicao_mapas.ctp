<script type="text/javascript" src="http://services.maplink.com.br/maplinkapi2/api.ashx?v=4&key=ewFCaw3OSKzIGGLXeudvRJvgvwABbG3qGKdPdYF1uJzQSDKCGBHTQR=="></script>

<div class="well" style="width: 720px; height: 510px; margin: 10px auto; margin-top: 0px;">
    <div id="map" style="width: 710px; height: 500px;">
    </div>
</div>


<script type="text/javascript">

	function processarMultiRotas() {
	    var mapa = new MMap2(document.getElementById('map'));
	    var multiRouteManager = new MMultiRouteManager(mapa);

	    var opcoesRota = obterDefinicoesDaRota();
	    var requisicoesMultiRotas = obterRequisicoesMultiRotas1();
	    
	    multiRouteManager.createMultiRoute(requisicoesMultiRotas, opcoesRota, 
	        function (retornoMultiRotas) {
	            if (typeof retornoMultiRotas == "object") {
	                var retornoMultiRotas = formatarResultadoMultiRotas(retornoMultiRotas.singleRouteTotals);
	                document.getElementById("map").style.display = "block";
	            }/*
	            else if (typeof retornoMultiRotas == "string") {
	                alert(retornoMultiRotas);    
	            }
	            else {
	                alert("Retorno inválido");
	            }*/
	        });

	    //var mapa = new MMap2(document.getElementById('map2'));
var originStop = new MRouteStop();
originStop.point = new MPoint(<?=$array_viagens_realizadas[0]['longitude_origem']?>,<?=$array_viagens_realizadas[0]['latitude_origem']?>);

var stopPoints = Array();
<? $i = 0 ?>
<? foreach ($array_viagens_realizadas as $key => $rota_prevista) { ?>
	<? if (($rota_prevista['longitude_destino'] >= $rota_prevista['longitude_origem']-0.02 && $rota_prevista['longitude_destino'] <= $rota_prevista['longitude_origem']+0.02) && 
		($rota_prevista['latitude_destino'] >= $rota_prevista['latitude_origem']-0.02 && $rota_prevista['latitude_destino'] <= $rota_prevista['latitude_origem']+0.02))
	 		continue; ?>
	<? //if ($i>20) break; ?>	
	stopPoint = new MRouteStop();
	stopPoint.point = new MPoint(<?=$rota_prevista['longitude_origem']?>, <?=$rota_prevista['latitude_origem']?>);
	stopPoints.push(stopPoint);
	<? $i ++ ?>

<? } ?>
var destinationStop = new MRouteStop();
destinationStop.point = new MPoint(<?=$rota_prevista['longitude_destino']?>, <?=$rota_prevista['latitude_destino']?>);

var routeStops = new Array;

var routePointAux;
routePointAux = new MRoutePoint();
routePointAux.routeStop = originStop;
routeStops.push(routePointAux);

for (i=0;i<stopPoints.length;i++) {
	routePointAux = new MRoutePoint();
	routePointAux.routeStop = stopPoints[i];
	routeStops.push(routePointAux);
}

routePointAux = new MRoutePoint();
routePointAux.routeStop = destinationStop;
routeStops.push(routePointAux);

var routeOptions = new MRouteOptions();

var routeDetails = new MRouteDetails();
routeDetails.optimizeRoute = true;
routeDetails.descriptionType = 0;
routeDetails.routeType = 1; 

var vehicle = new MVehicle();
vehicle.tankCapacity = 20;
vehicle.averageConsumption = 9;
vehicle.fuelPrice = 3;
vehicle.averageSpeed = 60;
vehicle.tollFeeCat = 2;

routeOptions.language = "portugues";
routeOptions.routeDetails = routeDetails;
routeOptions.vehicle = vehicle;

var routeManager = new MRouteMannager(mapa);
routeManager.createRoute(routeStops, routeOptions);	    
	    /*
	    var multiRouteManager = new MMultiRouteManager(mapa);

	    var opcoesRota = obterDefinicoesDaRota();
	    var requisicoesMultiRotas = obterRequisicoesMultiRotas2();
	    
	    multiRouteManager.createMultiRoute(requisicoesMultiRotas, opcoesRota, 
	        function (retornoMultiRotas) {
	            if (typeof retornoMultiRotas == "object") {
	                var retornoMultiRotas = formatarResultadoMultiRotas(retornoMultiRotas.singleRouteTotals);
	                document.getElementById("map").style.display = "block";
	            }
	        });
		*/
	}

	function obterRequisicoesMultiRotas1() {
	    var requisicaoMultiRotas = new Array();
	    var corLinhaRota = "EF0505";

	    <? foreach ($array_viagens_previstas as $key => $rota_prevista) { ?>
	    	<? if ($rota_prevista['longitude_origem']==$rota_prevista['longitude_destino'] && $rota_prevista['latitude_origem'] == $rota_prevista['latitude_destino']) continue; ?>
		    //console.log("<?=str_replace("\n","",var_export($rota_prevista,true));?>");
		    var origem = new MRouteStop();
		    //origem.description = "Ponto de Origem 1 - R. Joao Vieira Prioste, 1007-1049 - Carrao, Sao Paulo, 03429-000";
		    origem.point = new MPoint();
		    origem.point.x = <?=$rota_prevista['longitude_origem']; ?>;
		    origem.point.y = <?=$rota_prevista['latitude_origem']; ?>;

		    var destino = new MRouteStop();
		    //destino.description = "Ponto de Destino 1 - Av Pres Wilson, Vila Prudente, Sao Paulo, 04220-000";
		    destino.point = new MPoint();
		    destino.point.x = <?=$rota_prevista['longitude_destino']; ?>;
		    destino.point.y = <?=$rota_prevista['latitude_destino']; ?>;

		    requisicaoMultiRotas[requisicaoMultiRotas.length] = new MMultiRouteRequest(origem, destino, corLinhaRota);
	    <? } ?>
	    return requisicaoMultiRotas;
	}

	function obterRequisicoesMultiRotas2() {
	    var requisicaoMultiRotas = new Array();
	    corLinhaRota = "083ADB";
	    <? foreach ($array_viagens_realizadas as $key => $rota_prevista) { ?>
	    	<? if ($rota_prevista['longitude_origem']==$rota_prevista['longitude_destino'] && $rota_prevista['latitude_origem'] == $rota_prevista['latitude_destino']) continue; ?>
	    	<? if (($rota_prevista['longitude_destino'] >= $rota_prevista['longitude_origem']-0.02 && $rota_prevista['longitude_destino'] <= $rota_prevista['longitude_origem']+0.02) && 
	    		($rota_prevista['latitude_destino'] >= $rota_prevista['latitude_origem']-0.02 && $rota_prevista['latitude_destino'] <= $rota_prevista['latitude_origem']+0.02))
	    	 		continue; ?>
	    	<? if ($key>150) break; ?>
		    //console.log("<?=str_replace("\n","",var_export($rota_prevista,true));?>");
		    var origem = new MRouteStop();
		    //origem.description = "Ponto de Origem 1 - R. Joao Vieira Prioste, 1007-1049 - Carrao, Sao Paulo, 03429-000";
		    origem.point = new MPoint();
		    origem.point.x = <?=$rota_prevista['longitude_origem']; ?>;
		    origem.point.y = <?=$rota_prevista['latitude_origem']; ?>;

		    var destino = new MRouteStop();
		    //destino.description = "Ponto de Destino 1 - Av Pres Wilson, Vila Prudente, Sao Paulo, 04220-000";
		    destino.point = new MPoint();
		    destino.point.x = <?=$rota_prevista['longitude_destino']; ?>;
		    destino.point.y = <?=$rota_prevista['latitude_destino']; ?>;

		    requisicaoMultiRotas[requisicaoMultiRotas.length] = new MMultiRouteRequest(origem, destino, corLinhaRota);
	    <? } ?>
	    return requisicaoMultiRotas;
	}

	function obterDefinicoesDaRota() {
	    var routeOptions = new MRouteOptions();
	    routeOptions.language = "portugues";
	    routeOptions.vehicle = obterDadosDeVeiculo();
	    routeOptions.routeDetails = obterDetalhesDaRota();
	    return routeOptions;
	}

	function obterDadosDeVeiculo() {
	    var vehicle = new MVehicle();
	    vehicle.tankCapacity = 70;
	    vehicle.averageConsumption = 9;
	    vehicle.fuelPrice = 2.60;
	    vehicle.averageSpeed = 65;
	    vehicle.tollFeeCat = 2;
	    return vehicle;
	}

	function obterDetalhesDaRota() {
	    var detalhesDaRota = new MRouteDetails();
	    detalhesDaRota.optimizeRoute = false;
	    detalhesDaRota.routeType = 0;
	    detalhesDaRota.descriptionType = 0;
	    return detalhesDaRota;
	}

	function formatarResultadoMultiRotas(multiRoute) {
	    var resultado = "";
	    numeroRotasRetornadas = multiRoute.length;
	    
	    for (var i = 0; i < numeroRotasRetornadas; i++) {
	        resultado += "<br><br>->>> Rota [" + (i + 1) + "]<br>";
	        resultado += "[Origem]<br>";
	        resultado += formatarTextoPontoDeParada(multiRoute[i].origin);
	        resultado += "<br>[Destino]<br>";
	        resultado += formatarTextoPontoDeParada(multiRoute[i].destin);
	        resultado += "<br>[RouteID]<br>"
	        resultado += multiRoute[i].logRouteId;
	        resultado += "<br>[Dados sumarizados]";
	        resultado += formatarTextoDadosSumarizados(multiRoute[i].routeTotals);
	    }

	    return resultado;
	}

	function formatarTextoDadosSumarizados(routeTotals) {
	    var textoDadosSumarizados = "<br>totalDistance: " + routeTotals.totalDistance;
	    textoDadosSumarizados += "<br>totalTime: " + routeTotals.totalTime;
	    textoDadosSumarizados += "<br>totalFuelUsed: " + routeTotals.totalFuelUsed;
	    textoDadosSumarizados += "<br>totaltollFeeCost: " + routeTotals.totaltollFeeCost;
	    textoDadosSumarizados += "<br>totalFuelUsed: " + routeTotals.totalFuelUsed;
	    textoDadosSumarizados += "<br>totalCost: " + routeTotals.totalCost;
	    textoDadosSumarizados += "<br>taxiFare1: " + routeTotals.taxiFare1;
	    textoDadosSumarizados += "<br>taxiFare2: " + routeTotals.taxiFare2;
	    return textoDadosSumarizados;
	}

	function formatarTextoPontoDeParada(pontoParada) {
	    var textoPontoDeParada = "Descricao: " + pontoParada.description;
	    textoPontoDeParada += "<br>Ponto (x, y): " + pontoParada.point.x + ", " + pontoParada.point.y
	    return textoPontoDeParada;
	}

    jQuery(document).ready(function(){
		processarMultiRotas();

    });

</script>
