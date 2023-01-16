<?php if (isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export'): 
	    header(sprintf('Content-Disposition: attachment; filename="%s"', basename('listagem_veiculos_regiao.csv')));
	    header('Pragma: no-cache');
	    
		echo '"Placa/Chassi";"Transportadora";"Tecnologia";"N/S";"Tipo";"Última Posição";"Data Computador Bordo";"Status";"Alvo";"SM";"Data Entrada Alvo";"Permanência"';
	    foreach($posicoes as $posicao):
	        $posicao = $posicao[0];
	        
	        $now = new DateTime();
            $ref = new DateTime($posicao['vlev_data']);
            $permanencia = $now->diff($ref);
            
    	    $status = $this->Buonny->status_viagem($posicao);
            
            $linha = "";
            $linha .= '"'. (isset($posicao['veic_placa'][0]) && ctype_alpha($posicao['veic_placa'][0]) ? preg_replace('/(\w{3})(\d+)/i', "$1-$2", $posicao['veic_placa']) : $posicao['veic_chassi']) .'";';
            $linha .= '"'. $posicao['pjur_razao_social'] .'";';
            $linha .= '"'. $posicao['tecn_descricao'] . '";';
            $linha .= '"'. $posicao['term_numero_terminal'] . '";';
            $linha .= '"'. $posicao['tvei_descricao'] . '";';
            $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $posicao['upos_descricao_sistema'] ) . '";';
            $linha .= '"'. $posicao['upos_data_comp_bordo'] . '";';
            $linha .= '"'. $status . '";';
            $linha .= '"'. $posicao['refe_descricao'] . '";';
            $linha .= '"'. $posicao['viag_codigo_sm'] . '";';
            $linha .= '"'. $posicao['vlev_data'] . '";';
            $linha .= '"'. (!empty($posicao['vlev_data']) ? "{$permanencia->d} dias, {$permanencia->h} horas, {$permanencia->i} minutos" : '') . '";';
    		echo "\n".$linha;
        endforeach;    
	else:
        if(!empty($posicoes)): ?>
        	<div class="well">
	            <strong>Última atualização:</strong> <?php echo date('d/m/Y H:i:s') ?> 
	        	<span class="pull-right">
	        		<?php echo $html->link('Atualizar', 'javascript:atualizaListaRelatorioSmVeiculosPorRegiao();') ?>
	        		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
	        	</span>
            </div>
        
            <style>
            	#map img{
            		max-width: none;
        			vertical-align: top;
            	}
        	</style>
            <div class="row-fluid">
            	<div class="span2">
            	</div>
            	<div class="span8">
                    <div style="width: 720px; text-align: right; vertical-align: bottom; margin-top: 10px;">
                        <label for="exibirAreasDeRisco">
                            <input type="checkbox" value="S" name="exibir_areas_de_risco" id="exibirAreasDeRisco" style="vertical-align: top">
                            <span style="vertical-align: bottom;">Exibir Áreas de Risco</span>
                        </label>

                    </div>
        		    <div class="well" style="width: 720px; height: 510px; margin: 10px auto; margin-top: 0px;">
        		        <div id="map" style="width: 710px; height: 500px;">
        		        </div>
        		    </div>
            		<div class="well" style="width: 720px; margin: 10px auto">
            			<ul style="list-style: none; margin: 0 0 0px 0px;">
            				<li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/red-pushpin.png"/>Alvo</li>
            				<li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/red.png"/>Cancelado</li>
            				<li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/green.png"/>Agendado</li>
            				<li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/yellow.png"/>Em trânsito</li>
            				<li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/blue.png"/>Entregando</li>
            				<li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/darkseagreen.png"/>Logístico</li>
            				<li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/orange.png"/>Sem viagem</li>
                            <li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/forbidden_yellow.png"/>&nbsp;Área de Risco</li>
            			</ul>
            		</div>
            	</div>
            </div>

            <script type="text/javascript">
        
                function init(left, bottom, right, top) {
                    var map = new MMap2(document.getElementById('map'));
                    map.zoomToExtent(new LBS.Bounds(left, bottom, right, top, 'EPSG:4236'));
                    //map.addControl(new GSmallMapControl());
                    map.addControl(new GLargeMapControl());
                    //map.addControl(new MMapTypeControl());
                    map.disableScrollWheelZoom();
                    return map;
                }

                function createMarker(point, label, marker_type) {
                    var icon = new MIcon();
                    icon.shadow = "";
                    icon.shadowSize = null;
                    icon.image = "/portal/img/marker/" + marker_type + ".png";
                    icon.iconSize = new MSize(25, 27);
                    icon.iconAnchor = new MIconPoint(13, 27);
                    icon.infoWindowAnchor = new MIconPoint(25, 10);
            
                    var marker = new MMarker(point, icon);
                    LBS.Event.addListener(marker, "mouseover", function (e) {
                        marker.openInfoWindowHtml(label);
                        LBS.Event.stop(e);
                    });
                    LBS.Event.addListener(marker, "mouseout", function (e) {
                       marker.closeInfoWindow();
                        LBS.Event.stop(e);
                    });

                    return marker;
                }
    
                function createCircle(point, radius, color) {
                    var options = new MPolylineProperties();
                    options.color = "#"+color;
                    options.weight = 2;
                    options.opacity = 0.8;

                    var circle = new MCircle(point, radius, color);
                    
                    return circle;
                }

                var objAreaDeRisco = new Array();
                var objRaioAreaDeRisco = new Array();
                var map;

                function exibeAreasDeRisco(exibe) {
                    var i
                    for (i = 0; i < objAreaDeRisco.length; i++) {
                        if (exibe) {
                            map.addMarker(objAreaDeRisco[i]);
                            map.addMarker(objRaioAreaDeRisco[i]);
                        } else {
                            map.removeMarker(objAreaDeRisco[i]);
                            map.removeMarker(objRaioAreaDeRisco[i]);
                        }
                    }                    
                }

                jQuery(document).ready(function(){
                    map = init(<?php echo $bounds['left'] ?>, <?php echo $bounds['bottom'] ?>, <?php echo $bounds['right'] ?>, <?php echo $bounds['top'] ?>);
            
                    <?php foreach ($posicoes as $posicao): 
                    	$posicao = current($posicao);
                        $status = $this->Buonny->status_viagem_cor($posicao);
                    ?>
                        var point = new MPoint(<?php echo $posicao['upos_longitude'] ?>, <?php echo $posicao['upos_latitude'] ?>);
                        map.addMarker(createMarker(point, "Placa: <b><?php echo $posicao['veic_placa'] ?></b>", "<?php echo $status ?>"));
                    <?php endforeach; ?>
                    <?php foreach ($areas_de_risco as $key => $area_de_risco): ?>
                        var point = new MPoint(<?php echo $area_de_risco['TRefeReferencia']['refe_longitude'] ?>, <?php echo $area_de_risco['TRefeReferencia']['refe_latitude'] ?>);
                        objAreaDeRisco[<?=$key?>] = createMarker(point, "Area de Risco: <b><?php echo $area_de_risco['TRefeReferencia']['refe_descricao'] ?></b>", "forbidden_yellow");
                        objRaioAreaDeRisco[<?=$key?>] = createCircle(point,<?=$area_de_risco['TRefeReferencia']['refe_raio']?>,'B0002D');
                        //map.addMarker(objAreaDeRisco[<?=$key?>]);
                    <?php endforeach; ?>
                    //console.log('aaaa');
                    //console.log(objAreaDeRisco);
                    <?php if(!empty($alvo)): ?>
        	            var point = new MPoint(<?php echo $alvo['longitude'] ?>, <?php echo $alvo['latitude'] ?>);
        	            map.addMarker(createMarker(point, "<b>Alvo</b>", "red-pushpin"));
        	        <?php endif; ?>

                    $("#exibirAreasDeRisco").click(function() {
                        exibeAreasDeRisco($(this).is(":checked"));
                    })
                });
    
            </script>
    
            <?php echo $this->element('/relatorios_sm/listagem_posicao_veiculos', array('posicoes'=>$posicoes)); ?>

        <?php else: ?>
            <div class="alert">
        		Nenhum registro encontrado.
        	</div>
        <?php endif; ?>
<?php endif; ?>