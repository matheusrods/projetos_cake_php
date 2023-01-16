<?php if(isset($listagem) && count($listagem)) : ?>
	
	<div class="row-fluid inline">
	    <?php echo $this->BForm->create('Funcionario', array('url' => array('controller' => 'funcionarios','action' => 'set_func_lib_trab'))); ?>

	        <table class="table table-striped">
	            <thead>
	                <tr>
	                	<th >
	                		<?php echo $this->BForm->input('FuncionarioSetorCargo.todos', array('type' => 'checkbox', 'label' => false, 'value' => 'todos', 'multiple', 'hiddenField' => false)); ?>Todos
	                	</th>
	                	<th >Código Unidade</th>
	                    <th >Razão Social</th>
	                    <th >Unidade</th>
	                    <th >Setor</th>
	                    <th >Cargo</th>
	                    <th >Matrícula</th>
	                    <th >CPF</th>
	                    <th >Funcionário</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php foreach ($listagem as $key => $linha): ?>
	                    <tr>
	                    	<td>
	                    		<?php 
	                    			echo $this->BForm->input('FuncionarioSetorCargo.'.$key.'.codigo',
	                    				array('type' => 'hidden',
	                    					'value' => $linha['FuncionarioSetorCargo']['codigo']
	                    			));
	                    		?>
	                    		<?php echo $this->BForm->input('FuncionarioSetorCargo.'.$key.'.codigo_check', 
	                    			array('type' => 'checkbox', 
	                    				'label' => false, 
	                    				'value' => $linha['FuncionarioSetorCargo']['codigo'], 
	                    				'multiple', 
	                    				'hiddenField' => false,
	                    				'checked' => ((!empty($linha['FLT']['codigo'])) ? 'checked' : ''),
	                    				'class' => 'checkAll',  
	                    				// "onclick"=>"libera_funcionario(this);"
	                    			)); ?>
	                    		
	                    	</td>
	                        <td class="input-mini"><?= $linha['Cliente']['codigo']; ?></td>
	                        <td><?= $linha['Cliente']['razao_social']; ?></td>
	                        <td><?= $linha['Cliente']['nome_fantasia']; ?></td>
	                        <td><?php echo $linha['Setor']['descricao']; ?></td>
	                        <td><?php echo $linha['Cargo']['descricao']; ?></td>
	                        <td><?php echo $linha['ClienteFuncionario']['matricula']; ?></td>
	                        <td><?php echo $linha['Funcionario']['cpf']; ?></td>
	                        <td><?php echo $linha['Funcionario']['nome']; ?></td>
	                    </tr>
	                <?php endforeach; ?>        
	            </tbody>
	        	
		    </table>
	    <?php     
	    echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary pull-left', 'id' => 'salvar')); 
	    echo "<div id='salvando' style='width:30px; height:30px; float: left; margin: 0 10px 20px;'></div>";    
	    echo $this->BForm->end();
	    ?> 
    </div>

<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('
	function mostra_botao(element) {
		if($(element).val()) {
			$("#botao").show();
		} else {
			$("#botao").hide();
		}
	}

	jQuery(document).ready(function(){

		$("#FuncionarioSetorCargoTodos").click(function(){
			if($(this).prop("checked")) {
				selecionarTodos(true);
			}
			else {
				selecionarTodos(false);
			}
		});

		libera_funcionario = function(elem) {

			var codigos = "";
			codigos += $(elem).val()+"|";

			if($(elem).prop("checked")) {
				envia_codigos(codigos,true);
			}
			else {
				envia_codigos(codigos,false);
			}
		}

		selecionarTodos = function(param) {
			var id = "";
			var codigos = "";
			$(".checkAll").each(function(){
				id = $(this).attr("id");

				if(param) {
					$("#"+id).prop("checked", true);
				}
				else {
					$("#"+id).prop("checked", false);	
				}

				// codigos += $(this).val()+"|";
			});

			// envia_codigos(codigos,param);

		}// fim selecionar todos


		envia_codigos = function(codigos,param) {

			param = (param) ? "ok" : "del";

			$.ajax({
		        type: "POST",
		        url: "/portal/funcionarios/set_func_lib_trab",
		        data: "controle="+param+"&dado="+codigos,
		        beforeSend: function() {
					$(this).parent("div").html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> <b>Aguarde! Gravando as informações!</b>");
				},
		        success: function(json) {
			
					if(json) {
						div.load(baseUrl + "funcionarios/listagem_funcionario_liberacao/" + Math.random());
					}
				},
		        complete: function() {

				}
		    });//fim ajax
		}

	});



'); ?>
