    <style>
    	#map img{
    		max-width: none;
			vertical-align: top;
    	}
	</style>
    <div class="row-fluid" id="divMapaOut">

    	<div class="span8">
            <div class='well' style="width: 920px; text-align: left; vertical-align: bottom; margin-top: 10px;">
                <div class="row-fluid inline">
                    <form name="frmOpcoesMapa" id="frmOpcoesMapa">
                        <?php foreach ($tipos_local as $key => $tipo): ?>
                            <label for="exibirTipo<?=$key?>" style="display: inline-block; width: 220px;">
                                <input type="checkbox" value="<?=$key?>" name="exibirTipo[<?=$key?>]" id="exibirTipo<?=$key?>" class="exibir_tipo" style="vertical-align: top">
                                <span style="vertical-align: bottom;"><?=$tipo['tloc_descricao']?></span>
                            </label>
                        <?php endforeach; ?>
                        <?php foreach ($classes_local as $key => $classe): ?>
                            <label for="exibirClasse<?=$key?>" style="display: inline-block; width: 220px;">
                                <input type="checkbox" value="<?=$key?>" name="exibirClasse[<?=$key?>]" id="exibirClasse<?=$key?>" class="exibir_classe" style="vertical-align: top">
                                <span style="vertical-align: bottom;"><?=ucwords(strtolower(Comum::trata_nome($classe)))?></span>
                            </label>
                        <?php endforeach; ?>                    
                        <label for="exibirSinistro" style="display: inline-block; width: 220px;">
                            <input type="checkbox" value="S" name="exibirSinistro" id="exibirSinistro" class="exibir_sinistro" style="vertical-align: top">
                            <span style="vertical-align: bottom;">Sinistro</span>
                        </label>
                    </form>
                </div>
            </div>
		    <div class="well" style="width: 920px; height: 510px; margin: 10px auto; margin-top: 0px;">
		        <div id="canvas_mapa" style="width: 910px; height: 500px;"></div>
		    </div>
    		<div class="well" style="width: 920px; margin: 10px auto; height: 110px;">
    			<ul style="list-style: none; margin: 0 0 0px 0px;">
                    <div style="width: 170px; display: inline-block;">
                        <li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/truck.png"/>Agendado</li>
                    </div>
                    <div style="width: 170px; display: inline-block;">
                        <li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/truck_6.png"/>Em Trânsito</li>
                    </div>
                    <div style="width: 170px; display: inline-block;">
                        <li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/truck_2.png"/>Entregando</li>
                    </div>
                    <div style="width: 170px; display: inline-block;">
                        <li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/truck_7.png"/>Logístico</li>
                    </div>
                    <div style="width: 170px; display: inline-block;">
                        <li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/truck_5.png"/>Sem Viagem</li>
                    </div>
                    <?php foreach ($tipos_local as $key => $tipo): ?>
                    <div style="width: 170px; display: inline-block;">
                        <li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/<?=$tipo['tloc_imagem']?>"/><?=$tipo['tloc_descricao']?></li>
                    </div>
                    <?php endforeach; ?>
                    <?php foreach ($classes_local as $key => $classe): ?>
                    <div style="width: 170px; display: inline-block;">
                        <li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/<?=$icones_classes[$key]?>"/><?=ucwords(strtolower(Comum::trata_nome($classe)))?></li>
                    </div>
                    <?php endforeach; ?>
                    <div style="width: 170px; display: inline-block;">
                        <li style="line-height: 25px; float:left; margin:0 5px;"><img style="width: 18px;" src="/portal/img/marker/shooting.png"/>Sinistro</li>
                    </div>
    			</ul>
    		</div>
    	</div>
    </div>
    <?php 
        echo $this->Javascript->codeBlock('
            var icones = [];
            var icones_classe = [];
            var pontos = [];
            var pontos_classe = [];
            var raios = [];
            var raios_classe = [];
            var sinistros = [];
        ',false);    
        $arrays_armazenamento = Array();
        foreach ($tipos_local as $key => $tipo):
            //$arrays_armazenamento[] = 'tipo_'.$key;
            echo $this->Javascript->codeBlock('
                icones['.$key.']="'.$tipo['tloc_imagem'].'";
                pontos['.$key.']=[];
                raios['.$key.']=[];
            ', false);
        endforeach;
        foreach ($classes_local as $key => $classe):
            //$arrays_armazenamento[] = 'tipo_'.$key;
            echo $this->Javascript->codeBlock('
                icones_classe['.$key.']="'.$icones_classes[$key].'";
                pontos_classe['.$key.']=[];
                raios_classe['.$key.']=[];
            ', false);
        endforeach;
        $options_mapa = array(
            'id' => 'map',
            'div_id' => 'canvas_mapa',
            'separate_code' => true,
            'draw_div' => false,
            'resizable' => false,
            'zoom' => 10,
        );
        foreach ($veiculos as $veiculo):
            $titulo = (!empty($veiculo[0]['veic_placa']) ? $veiculo[0]['veic_placa'] : $veiculo[0]['veic_chassi']);
            $titulo.= " - ".$veiculo[0]['upos_descricao_sistema'];            
            $options_mapa['marcadores'][] = array(
                'latitude' => $veiculo[0]['upos_latitude'],
                'longitude' => $veiculo[0]['upos_longitude'],
                'titulo' => $titulo,
                'icone' => "/portal/img/marker/".$icones_status[$veiculo[0]['status']].".png",
                'zIndex' => '999998'
            );
        endforeach;
        echo $this->GoogleMap->desenhaMapa($options_mapa);  

        $seta_opcoes = '';
        if (!empty($opcoes_selecionadas['exibirTipo'])) {
            foreach ($opcoes_selecionadas['exibirTipo'] as $opcao) {
                $seta_opcoes.='$("#exibirTipo'.$opcao.'").prop("checked",true);'."\n";
                $seta_opcoes.='$("#exibirTipo'.$opcao.'").change();'."\n";
            }
        }
        if (!empty($opcoes_selecionadas['exibirClasse'])) {
            foreach ($opcoes_selecionadas['exibirClasse'] as $opcao) {
                $seta_opcoes.='$("#exibirClasse'.$opcao.'").prop("checked",true);'."\n";
                $seta_opcoes.='$("#exibirClasse'.$opcao.'").change();'."\n";
            }
        }
        if (!empty($opcoes_selecionadas['exibirSinistro'])) {
            $seta_opcoes.='$("#exibirSinistro").prop("checked",true);'."\n";
            $seta_opcoes.='$("#exibirSinistro").change();'."\n";
        }

    ?>

    <?php echo $this->Javascript->codeBlock('
        function carregar_pontos_mapa(tipo, classe) {
            bloquearDiv($("#divMapaOut"));
            if (tipo==null || tipo==undefined) tipo = "0";

            $.ajax({
                type: "POST",
                url: baseUrl + "veiculos/carregar_pontos_mapa_gr/'.$codigo_cliente.'/" +tipo+"/" +classe+"/N/"+ Math.random(),
                data: '.json_encode(array('viagens' => $viagens)).',
                dataType: "html",
                beforeSend: function() {
                    bloquearDiv($("#divMapaOut"));
                },
                success: function(data) {
                    //console.log(data);
                    var obj = JSON.parse(data);
                    for(i=0;i<obj.length;i++) {
                        var refe = obj[i];
                        var posicao = new google.maps.LatLng(refe.TRefeReferencia.refe_latitude, refe.TRefeReferencia.refe_longitude);
                        if (refe.TRefeReferencia.refe_poligono!="" && refe.TRefeReferencia.refe_poligono!=null) {
                            var path = new google.maps.MVCArray;
                            pontosX = refe.TRefeReferencia.refe_poligono.split(",");
                            for(var seq=0;seq<pontosX.length;seq++) {
                                pontoX = pontosX[seq].split(" ");
                                path.insertAt(seq, new google.maps.LatLng(pontoX[0], pontoX[1]));
                            }
                            var rectangle = new google.maps.Polygon({
                                strokeColor: "#AAAAEE",
                                strokeOpacity: 0.6,
                                strokeWeight: 2,
                                fillColor: "#AAAAFF",
                            });
                            rectangle.setMap(map);
                            rectangle.setPaths(new google.maps.MVCArray([path]));
                        } else {
                            var rectangle = new google.maps.Rectangle({
                                strokeColor: "#AAAAEE",
                                strokeOpacity: 0.6,
                                strokeWeight: 2,
                                fillColor: "#AAAAFF",
                                fillOpacity: 0.50,
                                map: map,
                                bounds: new google.maps.LatLngBounds(
                                    new google.maps.LatLng(refe.TRefeReferencia.refe_latitude_min, refe.TRefeReferencia.refe_longitude_min),
                                    new google.maps.LatLng(refe.TRefeReferencia.refe_latitude_max, refe.TRefeReferencia.refe_longitude_max)
                                )
                            });                                            

                        }
                        var marker_title = refe.TRefeReferencia.refe_descricao;
                        if (tipo==null || tipo==undefined || tipo=="0") {
                            var marker_image = new google.maps.MarkerImage("/portal/img/marker/"+icones_classe[classe], new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(15, 15), new google.maps.Size(30, 30));
                        } else {
                            var marker_image = new google.maps.MarkerImage("/portal/img/marker/"+icones[tipo], new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(15, 15), new google.maps.Size(30, 30));
                        }
                        map_marker = new google.maps.Marker({ position: posicao, map: map, title: marker_title, icon: marker_image, zIndex: 999999 });
                        if (tipo==null || tipo==undefined || tipo=="0") {
                            pontos_classe[classe].push(map_marker);    
                            raios_classe[classe].push(rectangle);
                        } else {
                            //console.log(pontos[tipo]);
                            pontos[tipo].push(map_marker);    
                            raios[tipo].push(rectangle);
                        }
                        
                    }
                },
                complete: function() {
                    $("#divMapaOut").unblock();
                }
            });

        }

        $(".exibir_tipo").change(function() {
            var id_tipo = ($(this).val());
            var checked = ($(this)[0].checked);
            if (checked) {
                carregar_pontos_mapa(id_tipo);
            } else {
                for (var j=pontos[id_tipo].length-1;j>=0;j--) {
                    pontos[id_tipo][j].setVisible(false);
                    pontos[id_tipo].pop();

                    raios[id_tipo][j].setVisible(false);
                    raios[id_tipo].pop();
                }
            }
        });

        $(".exibir_classe").change(function() {
            var id_classe = ($(this).val());
            var checked = ($(this)[0].checked);
            if (checked) {
                carregar_pontos_mapa(null,id_classe);
            } else {
                for (var j=pontos_classe[id_classe].length-1;j>=0;j--) {
                    pontos_classe[id_classe][j].setVisible(false);
                    pontos_classe[id_classe].pop();

                    raios_classe[id_classe][j].setVisible(false);
                    raios_classe[id_classe].pop();
                }
            }
        });   

        $(".exibir_sinistro").change(function() {
            var id_classe = ($(this).val());
            var checked = ($(this)[0].checked);
            if (checked) {
                $.ajax({
                    type: "POST",
                    url: baseUrl + "veiculos/carregar_pontos_mapa_gr/'.$codigo_cliente.'/0/0/S/"+ Math.random(),
                    dataType: "html",
                    beforeSend: function() {
                        bloquearDiv($("#divMapaOut"));
                    },
                    success: function(data) {
                        //console.log(data);
                        var obj = JSON.parse(data);
                        for(i=0;i<obj.length;i++) {
                            var refe = obj[i];
                            var posicao = new google.maps.LatLng(refe.Sinistro.latitude, refe.Sinistro.longitude);
                            var marker_title = "Sinistro SM: "+refe.Sinistro.sm+" em "+refe.Sinistro.data_evento+" - "+refe.Sinistro.modo_de_operacao;
                            var marker_image = new google.maps.MarkerImage("/portal/img/marker/shooting.png", new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(15, 15), new google.maps.Size(30, 30));
                            map_marker = new google.maps.Marker({ position: posicao, map: map, title: marker_title, icon: marker_image, zIndex: 999999 });
                            sinistros.push(map_marker);    
                        }
                    },
                    complete: function() {
                        $("#divMapaOut").unblock();
                    }
                });                
            } else {
                for (var j=sinistros.length-1;j>=0;j--) {
                    sinistros[j].setVisible(false);
                    sinistros.pop();
                }
            }
        });   

        $(document).ready(function(){
            '.$seta_opcoes.'
        });         
    ', false);
    ?>