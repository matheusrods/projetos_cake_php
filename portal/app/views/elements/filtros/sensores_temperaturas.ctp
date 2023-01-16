<div>	
	<div class='well'>
		<div id="filtros">
			<?php echo $this->Bajax->form('TStemSensoresTemperatura', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TStemSensoresTemperatura', 'element_name' => 'sensores_temperaturas'), 'divupdate' => '.form-procurar')) ?>
			<div class="row-fluid inline">				
				<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'TStemSensoresTemperatura') ?>
				<?php echo $this->BForm->input('veic_placa', array('label' => 'Placa','type' => 'text','class' => 'placa-veiculo input-small')) ?>
				<?php echo $this->Buonny->input_periodo($this, 'TStemSensoresTemperatura', 'data_inicial', 'data_final', true) ?>
			</div>	
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
			<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
			<?php echo $this->BForm->end();?>
		</div>
	</div>
	<?php
		echo $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				setup_mascaras();
				setup_datepicker();
				var div = jQuery('div.lista');
				bloquearDiv( div );
				div.load(baseUrl + 'sensores_temperaturas/listagem/');
				$('#limpar-filtro').click(function(){
					bloquearDiv($('.form-procurar'));
					$('.form-procurar').load(baseUrl + '/filtros/limpar/model:TStemSensoresTemperatura/element_name:sensores_temperaturas/' + Math.random())
				});
			});
		");
	?>	
</div>