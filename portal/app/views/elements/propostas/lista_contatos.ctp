<?php $index = isset($index) ? $index : ''; ?>
<h5><?php echo $titulo ?></h5>
<div class="lista-contatos">
	<div style="float: left">
	    <?=$this->BForm->input("PropostaContato.emails",Array('style'=>'display: none','label'=>false))?>
	</div>
	<div class="actionbar-right">
		<?php if(!$readonly): ?>
			<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-contato','id'=>'btnIncluirContato', 'escape' => false)); ?>
		<?php endif; ?>
	</div>
	<table class='table table-striped contato'>
		<thead>
			<th class='input-large'>Nome</th>
			<th class='input-large'>Tipo Contato</th>
			<th class='input-large'>Tipo Retorno</th>
			<th class='input-large'></th>
			<th></th>
		</thead>
		<tbody>
		    <?php
		    	$key_fixo = 0;
	            $contadorContato = 0; 
	            if (!isset($codigo_tipo_retorno_fixo)) $codigo_tipo_retorno_fixo = null;
            	if (empty($listaContatos)):
	                echo $this->element('propostas/incluir_linha_contato', array('key'=>$key_fixo++, 'model'=>$model, 'codigo_tipo_retorno_fixo'=>TipoRetorno::TIPO_RETORNO_EMAIL));
            	else:
	                foreach ($listaContatos as $key => $contato):
	                	if (!is_numeric($key)) continue;
                		if($key >= $key_fixo){
		                    $contadorContato = max($contadorContato, $key);
	                    	echo $this->element('propostas/incluir_linha_contato', array('key'=>$key, 'model'=>$model, 'contato'=>$contato, 'codigo_tipo_retorno_fixo'=>($key==0 && !$readonly? TipoRetorno::TIPO_RETORNO_EMAIL :$codigo_tipo_retorno_fixo) ));
						}
                	endforeach;
            	endif;
	        ?>
		</tbody>
	</table>
</div>
<?php echo $this->Javascript->codeBlock('
	var contador_contato = ' . $contadorContato . ';
	
	$(document).ready(function() {
		
		$(document).on("click","a.novo-contato", function(e){

			var conteiner = $(this).parent().parent().find("table.contato tbody");
			contador_contato++;
			$.ajax({
				url: baseUrl + "propostas/novo_contato/"+ contador_contato +"/'.$index.'/'.$codigo_tipo_retorno_fixo.'/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.append(data);
				}
			});
			e.stopImmediatePropagation();
		});

		$(document).on("click", "a.remove-contato", function(){
			$(this).parent().parent().remove();
			return false;
		});
	});
');
?>