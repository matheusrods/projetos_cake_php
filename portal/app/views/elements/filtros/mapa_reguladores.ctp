<div class='well'>
	<div id="filtros">
		<?php echo $this->Bajax->form('Regulador', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Regulador', 'element_name' => 'mapa_reguladores'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline endereco">
			<?php echo $this->BForm->input('endereco', array('label' => 'Endereço', 'placeholder' => 'Endereço', 'class' => 'input-xlarge')); ?>
			<?php echo $this->BForm->input('latitude', array('label' => 'Latitude', 'placeholder' => 'Latitude', 'class' => 'input-medium numeric')); ?>
			<?php echo $this->BForm->input('longitude', array('label' => 'Longitude', 'placeholder' => 'Longitude', 'class' => 'input-medium numeric')); ?>
			<?php echo $this->BForm->input('raio', array('label' => 'Raio (KM)', 'placeholder' => 'Raio (KM)', 'class' => 'input-small numeric')); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>
<?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function() {
		setup_mascaras();
		jQuery('#limpar-filtro').click(function(){
			bloquearDiv(jQuery('.form-procurar'));
			jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:Regulador/element_name:mapa_reguladores/'+ Math.random())
		});		
		$('#ReguladorEndereco').blur(function(){
			$('#ReguladorEndereco').parent().removeClass('error').find('.help-block').remove();
			endereco = $(this).val().trim();
			if(endereco != ''){
				bloquearDiv($('div.endereco'));
	            $.post(baseUrl + 'enderecos/carregar_lat_lgn_por_endereco/'+endereco+'/'+Math.random(),function(data){
	                data = $.parseJSON(data);
	                if(data){
	                    $('#ReguladorLatitude').val(data['latitude']);
	                    $('#ReguladorLongitude').val(data['longitude']);
	                }else{
	                	$('#ReguladorLatitude').val('');
	                    $('#ReguladorLongitude').val('');
	                    $('#ReguladorEndereco').parent().addClass('error').append('<div class=\"help-block error-message\">Endereço não encontrado</div>');
	                }
	                $('div.endereco').unblock();
	            });
			}
		});		
        var div = jQuery('div.lista-reguladores');
        bloquearDiv(div);
		div.load(baseUrl + 'reguladores/mapa_reguladores_listagem/' + Math.random());
	})
")  ?>