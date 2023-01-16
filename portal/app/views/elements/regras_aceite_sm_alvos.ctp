<?php $index = isset($index) ? $index : ''; ?>
<div class="lista-contatos">
	<div class="actionbar-right">
				<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-contato','id'=>'btnIncluirContato', 'escape' => false)); ?>
	</div>
	<table class='table table-striped contato'>
		<thead>
			<th class='input-xxxlarge'>Alvos</th>
			<th></th>
		</thead>
		<tbody>
		<?php 
			$key_fixo = 0;
			$contadorContato = 0; 
		 ?>
		    <?php		    	
            	if (empty($listaContatos)):
            		echo $this->element('incluir_linhas_alvos', array('key'=>$key_fixo++, 'model'=>$model));
            	else:
	                foreach ($listaContatos as $key => $contato):
	                	if (!is_numeric($key)) continue;
                		if($key >= $key_fixo){
		                    $contadorContato = max($contadorContato, $key);
	                    	echo $this->element('incluir_linhas_alvos', array('key'=>$key, 'model'=>$model) );
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
				url: baseUrl + "regras_aceite_sm/nova_linha/"+ contador_contato +"/'.$index.'/"+ Math.random(),
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