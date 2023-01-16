<div>	
	
	<div class='row-fluid inline'>
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Adicionar Problema', 'javascript:void(0)', array('class' => 'btn btn-success', 'escape' => false, 'id' => 'AdicionaProblema' )); ?>
		<table class='table table-striped' id="problemas">
			<thead>
				<th class='input-large'>
					<?php echo $this->BForm->input('problema_tipo', array('class' => 'input-large', 'label' => 'Tipo de Problema','name' => 'data[problema_tipo]' ,'empty' => 'Selecione um Tipo', 'options' => $problema_tipo )) ?>
				</th>
				<th class='input-xlarge'>
					<?php echo $this->BForm->input('problema_codigo', array('class' => 'input-xxlarge', 'label' => 'Problema Encontrado','name' => 'data[problema_codigo]' , 'empty' => 'Selecione um Problema', 'options' => $problema_codigo )) ?>
				</th>
				
				<th></th>
			</thead>
			<tbody>
				<?php
					if(isset($this->data['Recebsm']['problemas'])){
						foreach ($this->data['Recebsm']['problemas'] as $key => $value) {
							echo '<tr>';
							echo '<td colspan="2">';
							echo $value['TipoProblema']['descricao'].' - '.$value['ViagemProblema']['descricao'];
							echo $this->BForm->input('problema_codigo', array('name' => 'data[Recebsm][problema_tipo][]', 'type' => 'hidden', 'value' => $value['ViagemProblema']['codigo']));
							echo '</td>';
							echo '<td>';
							echo '<a href="javascript:void(0)"" class="btn remove" > <i class="icon-minus icon-bleck"></i> </a>';
							echo '</td>';
							echo '</tr>';
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		$("#AdicionaProblema").click(function(){
			var tbody = $("#problemas tbody");
			var selected_tipo = $("#RecebsmProblemaTipo option:selected");
			var selected_prob = $("#RecebsmProblemaCodigo option:selected");

			var new_row = "<tr>";
			new_row += "<td colspan=\'2\'>";
			new_row += selected_tipo.html()+" - "+selected_prob.html();
			new_row += "<input name=\'data[Recebsm][problema_tipo][]\' value=\'"+selected_prob.val()+"\' type=\'hidden\' >";
			new_row += "</td>";

			new_row += "<td>";
			new_row += "<a href=\'javascript:void(0)\' class=\'btn remove\' > <i class=\'icon-minus icon-bleck\'></i> </a>";
			new_row += "</td>";
			new_row += "</tr>";

			if(selected_tipo.val() && selected_prob.val()){
				tbody.append(new_row);	
			}			

			return false;

		});	

		$(document).on("click",".remove",function(){
			$(this).parents("tr:eq(0)").remove();
			return false;
		});

		$("#RecebsmProblemaTipo").change(function(){
			var viagem_problema = $("#RecebsmProblemaCodigo");
			var viagem_tipo 	= $("#RecebsmProblemaTipo");

			if(viagem_tipo.val()){
				$.ajax({
					url: baseUrl + "Problemas/lista_problemas/" + viagem_tipo.val() + "/" + Math.random(),
					dataType: "html",
					beforeSend: function(){
						viagem_problema.html("<option value=\"\">Aguarde...</option>");
					},
					success: function(data){
						viagem_problema.html(data);	
					},
					error: function(obj,msg,erro){
						viagem_problema.html("<option value=\"\">Selecione um Problema</option>");
					}

				});

			} else {
				viagem_problema.html("<option value=\"\">Selecione um Problema</option>");
			}

			return false;
		});

		
	});', false);
?>