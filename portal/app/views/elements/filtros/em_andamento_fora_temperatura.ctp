<?php if(empty($authUsuario['Usuario']['codigo_cliente'])):?>
    <div class='well'>
		<div id="filtros">
			<?php echo $this->Bajax->form('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TViagViagem', 'element_name' => 'em_andamento_fora_temperatura'), 'divupdate' => '.form-procurar')) ?>
			<div class="row-fluid inline">				
				<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'TViagViagem') ?>	        
			</div>	
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
			<?php echo $this->BForm->end();?>
		</div>
	</div>
<?php endif; ?>
<?php
	echo $this->Javascript->codeBlock("
		jQuery(document).ready(function(){
			setup_mascaras();
			bloquearDiv($('div.lista'));
			atualizaListaViagensForaTemperatura();

			$('#limpar-filtro').click(function(){
				bloquearDiv($('.form-procurar'));
				$('.form-procurar').load(baseUrl + '/filtros/limpar/model:TViagViagem/element_name:em_andamento_fora_temperatura/' + Math.random())
			});
		});
	");
?>