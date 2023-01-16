<?php if(isset($reguladores) && count($reguladores) == 0 ): ?>
<div class='alert alert-warning'><strong>Não há registros encontrados para os critérios pesquisados.</strong></div>
<?php elseif(isset($reguladores) && count($reguladores) > 0 ) : ?>
	<?php echo $this->Paginator->options(array('update' => 'div.lista-reguladores'));  ?>
	<div class="row-fluid inline">
		<img src='/portal/img/marker/blue.png'> Reguladores
		<img src='/portal/img/marker/red-pushpin.png'> Ponto Pesquisado
	</div>
	<div style="width: 100%; height: 400px; background: none repeat scroll 0% 0% rgb(229, 227, 223); position: relative;" id="canvas_mapa"> </div>
	<script type="text/javascript">
		$(function(){
			$('.alert').delay(4000).animate({opacity:0,height:0,margin:0},function(){jQuery(this).slideUp()});
			if (typeof(window.google) != 'undefined') {
				var map_coords = new google.maps.LatLng(-22.070647,-48.4337);
				var map_config = { zoom: 4, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
				map = new google.maps.Map(document.getElementById('canvas_mapa'), map_config);
				var marker_regulador_image = new google.maps.MarkerImage("/portal/img/marker/blue.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
				var marker_filtro_image = new google.maps.MarkerImage("/portal/img/marker/red-pushpin.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
				dado = new google.maps.LatLng('<?php echo $latitude; ?>', '<?php echo $longitude; ?>');
				map_marker = new google.maps.Marker({ position: dado, map: map, icon: marker_filtro_image,});
				map.setCenter(dado);
				map.setZoom(10);
				<?php foreach($reguladores_mapa as $key => $dado ):
					$regulador  = isset($dado['Regulador']['nome']) 			  ? addslashes($dado['Regulador']['nome'])  : NULL;
					$latitude   = isset($dado['ReguladorRegiao']['latitude'])  ? $dado['ReguladorRegiao']['latitude']  : NULL;
					$longitude  = isset($dado['ReguladorRegiao']['longitude']) ? $dado['ReguladorRegiao']['longitude'] : NULL;
					$prioridade = isset($dado['ReguladorRegiao']['prioridade']) ? $dado['ReguladorRegiao']['prioridade'] : NULL;
					$raio       = isset($dado['ReguladorRegiao']['raio']) ? $dado['ReguladorRegiao']['raio'] : 1;
					?>
					dado = new google.maps.LatLng('<?php echo $latitude; ?>', '<?php echo $longitude; ?>');
					var marker_title = "<?php echo 'Regulador: '. $regulador;?>\n";
					marker_title += "<?php echo 'Prioridade: '. $prioridade;?>\n";
					<?php
					if( isset($dado['ReguladorContato']) ){
						foreach ($dado['ReguladorContato'] as $cv => $dados_contato ) {?>
							marker_title += "Nome: <?php echo $dados_contato['nome'];?> ( <?php echo $dados_contato['descricao'];?> )\n";
						<?}
					}?>				
					var raio = calcula_raio(<?php echo $raio;?>);
					rectangle = new google.maps.Rectangle({
						strokeColor: "#AAAAEE",
						strokeOpacity: 0.6,
						strokeWeight: 2,
						fillColor: "#AAAAFF",
						fillOpacity: 0.50,
						map: map,
						bounds: new google.maps.LatLngBounds(
						new google.maps.LatLng(<?php echo ($latitude)?>-raio, <?php echo ($longitude)?>-raio),
						new google.maps.LatLng(<?php echo ($latitude)?>+raio, <?php echo ($longitude)?>+raio)
						)
					});					
					map_marker = new google.maps.Marker({ position: dado, map: map, title: marker_title, icon: marker_regulador_image});
				<?php endforeach; ?>
			} else {
				var html  = '<div class="alert alert-error">';
				html += '    <h4>Erro na api do googlemaps</h4>';
				html += '    <h5>Verifique as suas configurações de proxy, ou se o script da api está carregado corretamente.</h5>';
				html += '    </div>';
				$("#canvas_mapa").html(html);
			}
		});
		function calcula_raio(raio_metros){
			var raio_latLgn = (raio_metros / 1000) / 111.319;
			return raio_latLgn;
		}
	</script>
	<br />
	<br />
	<table class='table table-striped horizontal-scroll' style='width:2000px;max-width:none;'>
		<thead>
			<tr>
				<th><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
				<th><?php echo $this->Paginator->sort('Regulador', 'nome') ?></th>
				<th class='input-medium'><?php echo $this->Paginator->sort('CPF/CNPJ', 'codigo_documento') ?></th>
				<th><?php echo $this->Paginator->sort('Contato', 'contato') ?></th>
				<th><?php echo $this->Paginator->sort('Endereço', 'endereco') ?></th>
				<th><?php echo $this->Paginator->sort('Bairro', 'bairro') ?></th>
				<th><?php echo $this->Paginator->sort('Cidade', 'cidade') ?></th>
				<th><?php echo $this->Paginator->sort('CEP', 'cep') ?></th>
				<th><?php echo $this->Paginator->sort('Latitude', 'latitude') ?></th>
				<th><?php echo $this->Paginator->sort('Longitude', 'longitude') ?></th>
			</tr>
		</thead>
		<tbody id="lista_reguladores">
            <?php foreach ($reguladores as $regulador): ?>
            <tr class="reguladores-tr" codigo="<?php echo $regulador['Regulador']['codigo'] ?>">
            	<td><?= $regulador['Regulador']['codigo'] ?></td>
                <td><?= $regulador['Regulador']['nome'] ?></td>
                <td><?= comum::formatarDocumento($regulador['Regulador']['codigo_documento']) ?></td>
                <td><?= $regulador[0]['contato']?></td>
                <td><?= (trim($regulador['ReguladorEndereco']['numero']) != '') ? $regulador['Endereco']['descricao'].','.$regulador['ReguladorEndereco']['numero'] : $regulador['Endereco']['descricao']; ?></td>
                <td><?= $regulador['EnderecoBairro']['descricao'] ?></td>
                <td><?= (trim($regulador['EnderecoEstado']['descricao']) != '' ? $regulador['EnderecoCidade']['descricao'].' - '.$regulador['EnderecoEstado']['descricao'] : $regulador['EnderecoCidade']['descricao']);?></td>
                <td><?= comum::formataCEP($regulador['EnderecoCep']['cep']) ?></td>            
				<td><?= $regulador['ReguladorRegiao']['latitude']?></td>
                <td><?= $regulador['ReguladorRegiao']['longitude']?></td>
            </tr>
            <?php endforeach; ?>
		</tbody>
	</table>
	<div class='row-fluid'>
		<div class='numbers span6'>
			<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
			<?php echo $this->Paginator->numbers(); ?>
			<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
		</div>
		<div class='counter span6'>
			<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		</div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
	
<?php endif;?>