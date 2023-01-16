<div class='well'>
	<div id="filtros">
		<?php echo $this->Bajax->form('Prestador', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Prestador', 'element_name' => 'mapa_prestadores', 'searcher' => $input_id), 'divupdate' => '.form-procurar')) ?>
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
			jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:Prestador/element_name:mapa_prestadores/searcher:".$input_id."/'+ Math.random())
		});
	
		$('#PrestadorEndereco').blur(function(){
			$('#PrestadorEndereco').parent().removeClass('error').find('.help-block').remove();
			endereco = $(this).val().trim();
			if(endereco != ''){
				bloquearDiv($('div.endereco'));
	            $.post(baseUrl + 'prestadores/carregar_lat_lgn_por_endereco/'+endereco+'/'+Math.random(),function(data){
	                data = $.parseJSON(data);
	                if(data){
	                    $('#PrestadorLatitude').val(data['latitude']);
	                    $('#PrestadorLongitude').val(data['longitude']);
	                }else{
	                	$('#PrestadorLatitude').val('');
	                    $('#PrestadorLongitude').val('');
	                    $('#PrestadorEndereco').parent().addClass('error').append('<div class=\"help-block error-message\">Endereço não encontrado</div>');
	                }
	                $('div.endereco').unblock();
	            });
			}
		});
		atualizaListaPrestadoresVisualizar('".$input_id."');
        function atualizaListaPrestadoresVisualizar(input_id) {    
            var div = jQuery('div.lista-prestadores');
            bloquearDiv(div);
            if (input_id == '')
                div.load(baseUrl + 'prestadores/mapa_prestadores_listagem/0/'+ Math.random());
            else
                div.load(baseUrl + 'prestadores/mapa_prestadores_listagem/0/prestadores_buscar_codigo/searcher:' + input_id + '/' + Math.random());
        }
	})
")  ?>