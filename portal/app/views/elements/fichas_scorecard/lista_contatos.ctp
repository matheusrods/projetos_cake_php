<?php $index = isset($index) ? $index : ''; ?>
<?php $item_nome = $tipo.$index; ?>
<?php $disabled = (isset($disabled) && $disabled == true ? true : false);?>
<h5><?php echo $titulo ?></h5>
<div class="lista-contatos">
	<?php if(!$disabled): ?>
	<div class="actionbar-right">
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-contato-'.$item_nome,'id'=>'btn-'.$item_nome, 'escape' => false)); ?>
	</div>
	<?php endif; ?>
	<table class='table table-striped contato-<?php echo $item_nome; ?>'>
		<thead>
			<th class='input-large'>Nome</th>
			<?php if($tipo != 'retorno'): ?>
				<th class='input-large'>Tipo</th>
			<?php endif; ?>
			<th class='input-large'>Tipo de Referência</th>
			<th class='input-large'></th>
			<th></th>
		</thead>
		<tbody>
		    <?php
		    	$key_fixo = 0;
	            $contadorContato = 0; 
		    	if(isset($tipos_retorno_fixo)):
			    	foreach($tipos_retorno_fixo as $tipo_retorno_fixo):
			            $contadorContato++;
			            echo $this->element('fichas_scorecard/incluir_linha_contato', array('key'=>$key_fixo++, 'tipo'=>$tipo, 'model'=>$model, 'tipo_retorno'=>$tipo_retorno, 'tipo_retorno_fixo'=>$tipo_retorno_fixo, 'disabled'=>$disabled ));
			    	endforeach;
			    	if (count($listaContatos)>0):
				    	foreach ($listaContatos as $key => $contato):
	                		if($key >= $key_fixo){
			                    $contadorContato = max($contadorContato, $key);
		                    	echo $this->element('fichas_scorecard/incluir_linha_contato', array('key'=>$key, 'tipo'=>$tipo, 'model'=>$model, 'tipo_retorno'=>$tipo_retorno, 'tipo_retorno_fixo'=>(!empty($contato['fixo']) ? $contato['codigo_tipo_retorno'] : ''), 'contato'=>$contato, 'disabled'=>$disabled ));
							}
	                	endforeach;
				    endif;
		    	else:
	            	if (empty($listaContatos)):
		                echo $this->element('fichas_scorecard/incluir_linha_contato', array('key'=>$key_fixo++, 'tipo'=>$tipo, 'model'=>$model, 'tipo_retorno'=>$tipo_retorno, 'disabled'=>$disabled));
	            	else:
		                foreach ($listaContatos as $key => $contato):
	                		if($key >= $key_fixo){
			                    $contadorContato = max($contadorContato, $key);
		                    	echo $this->element('fichas_scorecard/incluir_linha_contato', array('key'=>$key, 'tipo'=>$tipo, 'model'=>$model, 'tipo_retorno'=>$tipo_retorno, 'tipo_retorno_fixo'=>(!empty($contato['fixo']) ? $contato['codigo_tipo_retorno'] : ''), 'contato'=>$contato, 'disabled'=>$disabled));
							}
	                	endforeach;
	            	endif;
	            endif;
	        ?>
		</tbody>
	</table>
</div>
<?php echo $this->Javascript->codeBlock('
	var contador_contato_'.$item_nome.' = ' . $contadorContato . ';	
	$(document).ready(function() {
		$("a.novo-contato-'.$item_nome.'").click(function(){
			var conteiner = $(this).parent().parent().find("table.contato-'.$item_nome.' tbody");
			contador_contato_'.$item_nome.'++;
			$.ajax({
				url: baseUrl + "fichas_scorecard/novo_contato_'.$tipo.'/"+ contador_contato_'.$item_nome.' +"/'.$index.'/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.append(data);
				}
			});
		});
		$(document).on("click", "a.remove-contato-'.$tipo.'", function(){
			$(this).parent().parent().remove();
			return false;
		});
	});
');?>