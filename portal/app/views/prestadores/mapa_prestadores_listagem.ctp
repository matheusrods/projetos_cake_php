<?php if(isset($prestadores) && count($prestadores) == 0 ){ ?>
<div class='alert alert-warning'><strong>Não há registros encontrados para os critérios pesquisados.</strong></div>
<?php }else if(isset($prestadores) && count($prestadores) > 0 ){ ?>
	<?php echo $this->Paginator->options(array('update' => 'div.lista-prestadores'));  ?>
	<div class="row-fluid inline">
		<img src='/portal/img/marker/blue.png'> Prestadores
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
				var marker_prestador_image = new google.maps.MarkerImage("/portal/img/marker/blue.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
				var marker_filtro_image = new google.maps.MarkerImage("/portal/img/marker/red-pushpin.png", new google.maps.Size(32, 32), new google.maps.Point(0, 0), new google.maps.Point(16, 32));
				dado = new google.maps.LatLng('<?php echo $latitude; ?>', '<?php echo $longitude; ?>');
				map_marker = new google.maps.Marker({ position: dado, map: map, icon: marker_filtro_image,});
				map.setCenter(dado);
				map.setZoom(10);
				<?php foreach($prestadores_mapa as $key => $dado ):
					$prestador = isset($dado['Prestador']['nome']) 			 ? addslashes($dado['Prestador']['nome'])  : NULL;
					$latitude  = isset($dado['PrestadorEndereco']['latitude'])  ? $dado['PrestadorEndereco']['latitude']  : NULL;
					$longitude = isset($dado['PrestadorEndereco']['longitude']) ? $dado['PrestadorEndereco']['longitude'] : NULL;
					?>
					dado = new google.maps.LatLng('<?php echo $latitude; ?>', '<?php echo $longitude; ?>');
					var marker_title = '<?php echo "Prestador: ". $prestador ." - Lat: ". $latitude ." Long: ". $longitude ?>';
					map_marker = new google.maps.Marker({ position: dado, map: map, title: marker_title, icon: marker_prestador_image,});
				<?php endforeach; ?>
			} else {
				console.log( typeof(window.google) );
				var html  = '<div class="alert alert-error">';
				html += '    <h4>Erro na api do googlemaps</h4>';
				html += '    <h5>Verifique as suas configurações de proxy, ou se o script da api está carregado corretamente.</h5>';
				html += '    </div>';
				$("#canvas_mapa").html(html);
			}
		});
	</script>
	<br />
	<!--
	<h4>Prestadores</h4>
	<span class="pull-right">
	<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', 
		array( 'controller' => $this->name, 'action' 	 => 'mapa_prestadores_listagem', TRUE ), 
		array( 'escape' => false, 'title'  => 'Exportar para Excel')
		); 
	?>  
	</span>
	-->
	<br />
	<table class='table table-striped'>
		<thead>
			<tr>
				<th><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
				<th><?php echo $this->Paginator->sort('Prestador', 'nome') ?></th>
				<th class='input-medium'><?php echo $this->Paginator->sort('CPF/CNPJ', 'codigo_documento') ?></th>
				<th><?php echo $this->Paginator->sort('Contato', 'contato') ?></th>
				<th><?php echo $this->Paginator->sort('Endereço', 'endereco') ?></th>
				<th><?php echo $this->Paginator->sort('Bairro', 'bairro') ?></th>
				<th><?php echo $this->Paginator->sort('Cidade', 'cidade') ?></th>
				<th><?php echo $this->Paginator->sort('CEP', 'cep') ?></th>
				<?php if(isset($destino) && $destino=='prestadores_buscar_codigo'){ ?>
					<th><?php echo $this->Paginator->sort('Distância', 'distancia') ?></th>
					<th></th>
				<?php } else { ?>
				<th class="numeric"><?php echo $this->Paginator->sort('Latitude', 'latitude') ?></th>
				<th class="numeric"><?php echo $this->Paginator->sort('Longitude', 'longitude') ?></th>
				<?php } ?>			
			</tr>
		</thead>
		<tbody id="lista_prestadores">
			<?php foreach ($prestadores as $key => $value): ?>
				<tr lat="<?php echo $value['Prestador']['latitude'] ?>" lgn="<?php echo $value['Prestador']['longitude'] ?>" codigo="<?php echo $value['Prestador']['codigo'] ?>" 
					nome="<?php echo $value['Prestador']['nome'] ?>">
					<td><?= $value['Prestador']['codigo'] ?></td>
					<td><?= $value['Prestador']['nome'] ?></td>
					<td><?= comum::formatarDocumento($value['Prestador']['codigo_documento']) ?></td>
					<td><?= str_replace('|', "</BR>", $value['Prestador']['contato']) ?></td>
					<td><?= (trim($value['Prestador']['numero']) != '' ? $value['Prestador']['endereco'].','.$value['Prestador']['numero'] : $value['Prestador']['endereco']); ?></td>
					<td><?= $value['Prestador']['bairro'] ?></td>
					<td><?= (trim($value['Prestador']['estado']) != '' ? $value['Prestador']['cidade'].'-'.$value['Prestador']['estado'] : $value['Prestador']['cidade']);?></td>
					<td><?= $value['Prestador']['cep'] ?></td>
					<?php if(isset($destino) && $destino=='prestadores_buscar_codigo'){ ?>
						<td class="numeric"><?= number_format($value['Prestador']['distancia'],2,',','.').' KM' ?></td>						
					<?php }else{ ?>
						<td class="numeric"><?= $value['Prestador']['latitude'] ?></td>
						<td class="numeric"><?= $value['Prestador']['longitude'] ?></td>
					<?php } ?>

					
					
				</tr>
			<?php endforeach ?>
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
	<?/*php echo $this->Javascript->codeBlock("
		$(document).ready(function(){
			$('#lista_prestadores tr').click(function(){
				map.setCenter(new google.maps.LatLng(
					$(this).attr('lat'),
					$(this).attr('lgn')
				));
				map.setZoom(14);
				$(window).scrollTop($('.lista').offset().top-100);
			});
		});
	");*/
	if(isset($destino) && $destino=='prestadores_buscar_codigo'){ 
		echo $this->Javascript->codeBlock("
		$(document).ready(function(){
			$('a#destino, #lista_prestadores tr').css('cursor','pointer');
			$('a#destino, #lista_prestadores tr').click(function() {			
				var codigo = $(this).attr('codigo');
				// if($('#nome_prestador').length)
				// 	$('#nome_prestador').val($(this).attr('nome'));

				var input = $('#{$input_id}');
				input.val(codigo).change().blur();
				close_dialog();
			})
		})");
	}
} ?>
