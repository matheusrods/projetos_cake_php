<div class='well'>
	<h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		<?php echo $this->BForm->create('TPveiParadaVeiculo', array('url' => array('controller' => 'paradas_veiculos', 'action' => 'mapa'))); ?>
			<div class="row-fluid inline">
				<?php echo $this->Buonny->input_codigo_cliente_base($this); ?>
			    <?php echo $this->Buonny->input_periodo($this, 'TPveiParadaVeiculo', 'data_inicial', 'data_final', false, 7); ?>
			    <?php echo $this->BForm->input('placa', array('class' => 'placa-veiculo input-small', 'label' => false, 'placeholder' => 'Placa')); ?>
			    <?php echo $this->BForm->input('minutos_parado', array('class' => 'input-mini just-number', 'label' => false, 'placeholder' => 'Minutos')); ?>
			    <?php echo $this->BForm->input('status_alvo', array('class' => 'input-small', 'label' => false, 'options' => array(TPveiParadaVeiculo::STATUS_ALVO_DENTRO => 'No Alvo', TPveiParadaVeiculo::STATUS_ALVO_FORA => 'Fora do Alvo'), 'empty' => 'Alvos')); ?>
			</div>
		    <div id="div-tipo-alvo" class="row-fluid inline">
				<?php echo $this->element('/filtros/alvos_origem', array('model'=>'TPveiParadaVeiculo')); ?>
			</div>
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $this->BForm->end() ?>
	</div>
	<?php $cds = array(); ?>
	<?php if (isset($this->data['TPveiParadaVeiculo']['cd_id'])): ?>
		<?php $cds=implode(',',$this->data['TPveiParadaVeiculo']['cd_id']) ?>
	<?php endif ?>
	<?php echo $this->Javascript->codeBlock('
	    jQuery(document).ready(function(){
	        init_combo_event_alvos_origem("TPveiParadaVeiculo", "#div-tipo-alvo", "#TPveiParadaVeiculoCodigoCliente","'.$cds.'");
	        setup_datepicker();
	        setup_mascaras();
	        jQuery("#TPveiParadaVeiculoCodigoCliente").blur();
	        jQuery("a#filtros").click(function(){
	            jQuery("div#filtros").slideToggle("slow");
	        });
	    });', false);
	?>
	<?php if (!empty($filtrado)): ?>
	    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
	 <?php endif; ?>
</div>
<?php if (isset($dados)): ?>
	<div id="canvas_mapa"></div>
	<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
		$(function(){
			$('.alert').delay(4000).animate({opacity:0,height:0,margin:0},function(){jQuery(this).slideUp()});
			
			resize();

			$(window).resize(function(){
				resize();
			});

			function resize(){
				$("#canvas_mapa").css({'height':$(window).height()});
			}

			if (typeof(window.google) != 'undefined') {
				<?php if (count($referencias) == 1): ?>
					var map_coords = new google.maps.LatLng(<?= $referencias[0]['TRefeReferencia']['refe_latitude']; ?>, <?= $referencias[0]['TRefeReferencia']['refe_longitude']; ?>);
					var map_config = { zoom: 12, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
				<?php else: ?>
					var map_coords = new google.maps.LatLng(-22.070647,-48.4337);
					var map_config = { zoom: 4, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
				<?php endif; ?>
				var map = new google.maps.Map(document.getElementById('canvas_mapa'), map_config);
				<?php if (!empty($dados)): ?>
					<?php foreach($dados as $dado): ?>
						dado = new google.maps.LatLng(<?php echo @$dado['0']['pvei_latitude']; ?>, <?php echo @$dado['0']['pvei_longitude']; ?>);
						var marker_title = '<?php echo @preg_replace("/(\w{3})(\d{4})/", "$1-$2",$dado['0']['veic_placa'])." : ".@$dado['0']['pvei_latitude'].",".@$dado['0']['pvei_longitude'].'\n'.@$dado['0']['pvei_descricao'].'\n'.$dado['0']['pvei_data_inicial']."-".$dado['0']['pvei_data_final']." (".Comum::convertToHoursMins($dado['0']['minutos_parado']).")"; ?>';
						<?php if ($dado['0']['refe_descricao_alvo']): ?>
							var marker_image = new google.maps.MarkerImage("/portal/img/marker/bullet-green.png", new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(15, 15));
						<?php else: ?>
							var marker_image = new google.maps.MarkerImage("/portal/img/marker/bullet-red.png", new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(15, 15));
						<?php endif; ?>
						map_marker = new google.maps.Marker({ position: dado, map: map, title: marker_title, icon: marker_image });
					<?php endforeach ?>
				<?php endif ?>
				<?php foreach($referencias as $referencia): ?>
					dado = new google.maps.LatLng(<?php echo @$referencia['TRefeReferencia']['refe_latitude']; ?>, <?php echo @$referencia['TRefeReferencia']['refe_longitude']; ?>);
					var marker_title = '<?php echo @$referencia['TRefeReferencia']['refe_descricao_alvo']." : ".@$referencia['TRefeReferencia']['refe_latitude'].",".@$referencia['TRefeReferencia']['refe_longitude']; ?>';
					var marker_image = new google.maps.MarkerImage("/portal/img/marker/red-dot.png", new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(15, 15));
					map_marker = new google.maps.Marker({ position: dado, map: map, title: marker_title, icon: marker_image });
				<?php endforeach; ?>
			} else {
		        var html  = '<div class="alert alert-error">';
		        html += '    <h4>Erro na api do googlemaps</h4>';
		        html += '    <h5>Verifique as susas configurações de proxy, ou se o script da api está carregado corretamente.</h5>';
		        html += '</div>';
		        $("#canvas_mapa").html(html);
		    }
		});
	</script>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		<span class="pull-right">
			<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', 'javascript:exportar()', array('escape' => false, 'title' =>'Exportar para Excel'));?>
		</span>
	</div>
	<table class='table table-striped'>
		<thead>
			<th>Placa</th>
			<th>Posição</th>
			<th>Data Inicial</th>
			<th>Data Final</th>
			<th>Alvo</th>
			<th>Tempo</th>
			<th>SM</th>
			<th>CD Origem</th>
			<th>Transportadora</th>
			<th>Motorista</th>
		</thead>
		<?php foreach ($dados as $dado): ?>
			<tr>
				<td><?= $this->Buonny->placa($dado['0']['veic_placa'], $this->data['TPveiParadaVeiculo']['data_inicial'], $this->data['TPveiParadaVeiculo']['data_inicial'], $this->data['TPveiParadaVeiculo']['codigo_cliente']) ?></td>
				<td><?= $this->Buonny->posicao_geografica($dado['0']['pvei_descricao'], $dado['0']['pvei_latitude'], $dado['0']['pvei_longitude']) ?></td>
				<td><?= $dado['0']['pvei_data_inicial'] ?></td>
				<td><?= $dado['0']['pvei_data_final'] ?></td>
				<td title="<?= $dado['0']['refe_descricao_alvo'] ?>"><img src="/portal/img/marker/bullet-<?= ($dado['0']['refe_descricao_alvo'] ? 'green' : 'red') ?>.png"/></td>
				<td><?= Comum::convertToHoursMins($dado['0']['minutos_parado']) ?></td>
				<td><?= $this->Buonny->codigo_sm($dado['0']['viag_codigo_sm']) ?></td>
				<td><?= $this->Buonny->posicao_geografica($dado['0']['refe_descricao_origem'], $dado['0']['refe_latitude_origem'], $dado['0']['refe_longitude_origem']) ?></td>
				<td><?= $dado['0']['tran_pjur_razao_social'] ?></td>
				<td><?= $dado['0']['moto_pess_nome'] ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php echo $this->Javascript->codeBlock("function exportar() {
	$('form').attr('action', '/portal/paradas_veiculos/mapa/export').submit();
}") ?>