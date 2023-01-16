<?php $index = isset($index) ? $index : ''; ?>
<h5><?php echo $titulo ?></h5>
<div class="lista-respostas">
	<div class="actionbar-right">
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success nova-resposta','id'=>'btnIncluir', 'escape' => false)); ?>
	</div>
	<table class='table table-striped resposta'>
		<thead>
			<th class='input-large'>Resposta</th>
			<th></th>
		</thead>
		<tbody>
		    <?php
		    	$key_fixo = 0;
	            $contadorResposta = 0; 
		    	if (empty($respostas)):
		        	echo $this->element('proposta_perguntas/incluir_linha_resposta', array('key'=>$key_fixo++, 'model'=>$model));
	            else:
		        	foreach ($respostas as $key => $resposta):
	                	if($key >= $key_fixo){
		                    $contadorResposta = max($contadorResposta, $key);
	                    	echo $this->element('proposta_perguntas/incluir_linha_resposta', array('key'=>$key, 'model'=>$model, 'resposta'=>$resposta));
						}
                	endforeach;
            	endif;
	        ?>
		</tbody>
	</table>
</div>
<?php echo $this->Javascript->codeBlock('
	var contador_contato = ' . $contadorResposta . ';
	
	$(document).ready(function() {
		$(document).on("click","a.nova-resposta", function(){
			var conteiner = $(this).parent().parent().find("table.resposta tbody");
			contador_contato++;

			$.ajax({
				url: baseUrl + "proposta_perguntas/nova_resposta/"+ contador_contato +"/'.$index.'/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.append(data);
				}
			});
		});

		$(document).on("click", "a.remove-resposta", function(){
			$(this).parent().parent().remove();
			return false;
		});
	});
');
?>