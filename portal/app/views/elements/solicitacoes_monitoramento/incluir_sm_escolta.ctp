<h4>Escolta</h4>

<div class="actionbar-right">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-escolta', 'escape' => false)); ?>
</div>

<div class='row-fluid inline escolta'>

<?php foreach($this->data['Recebsm']['RecebsmEscolta'] as $key => $escolta): ?>
<table class='table table-striped' data-index="<?php echo $key ?>">
	<thead>
		<th>
			
			<div class="row-fluid inline">

				<?php echo $this->Buonny->input_escolta($this, 'Recebsm.RecebsmEscolta','eesc_codigo',$key,"Empresa Escolta",TRUE) ?>
				<?php if($key): ?>
					<div class="control-group input text">
						<label for="RecebsmDtaFim">&nbsp</label>
						<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-escolta-remove', 'escape' => false)); ?>
					</div>
				<?php endif; ?>
			</div>
		</th>
	</thead>
	
	<tbody>
		<?php foreach($this->data['Recebsm']['RecebsmEscolta'][$key]['RecebsmEquipes'] as $key2 => $equipe): ?>
		<tr>
			<td>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$key}.RecebsmEquipes.{$key2}.nome", array('class' => 'input-large','label' => false, 'placeholder' => 'Equipe')); ?>
					<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$key}.RecebsmEquipes.{$key2}.telefone", array('class' => 'input-medium telefone','label' => false, 'placeholder' => 'Telefone')); ?>
					<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$key}.RecebsmEquipes.{$key2}.placa", array( 'class' => 'input-small placa-veiculo','label' => false, 'placeholder' => 'Placa')); ?>

					<?php if(!$key2): ?>
						<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-equipe', 'escape' => false)); ?>
					<?php else: ?>
						<?php echo $this->Html->link('<i class="icon-minus icon-black"></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-equipe-remove', 'escape' => false)); ?>
					<?php endif; ?>
					<div class="btn-group">								
						<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$key}.RecebsmEquipes.{$key2}.TVescViagemEscolta.vesc_armada", array('label' => false, 'class' => 'checkbox inline input-small', 'options' => array(1=>'Armada'), 'multiple' => 'checkbox')) ?>
						<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$key}.RecebsmEquipes.{$key2}.TVescViagemEscolta.vesc_velada", array('label' => false, 'class' => 'checkbox inline input-small', 'options' =>array(1=>'Velada'), 'multiple' => 'checkbox')) ?>
					</div>	
				</div>	
				<div class='row-fluid inline'>
			        <?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$key}.RecebsmEquipes.{$key2}.TTecnTecnologia.tecn_codigo", array('label' => 'Tecnologia', 'empty' => 'Tecnologia','options' => $tecnologias_lista, 'class' => 'tecn_codigo')) ?>
			        <?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$key}.RecebsmEquipes.{$key2}.TVescViagemEscolta.vesc_vtec_codigo", array('label' => 'Versão', 'empty' => 'Versão da Tecnologia', 'options' => $versoes[$key][$key2], 'class' => 'vtec_codigo')) ?>
			        <?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$key}.RecebsmEquipes.{$key2}.TVescViagemEscolta.vesc_numero_terminal", array('label' => 'Número Terminal')) ?>
			    </div>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>
<?php endforeach; ?>

</div>

<?php 
if(empty($versoes)){
    echo $this->Javascript->codeBlock('
        jQuery(document).ready(function(){
        	if($(".tecn_codigo").val() != ""){
				buscar_t_versao(".tecn_codigo", ".vtec_codigo");
			}else{
                $(".vtec_codigo").html("<option value=\"\">Versão da Tecnologia</option>");
            }
        });', false);
}
echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		var contador_equipe = $("div.escolta table").length;

		$(document).on("change",".tecn_codigo",function(){
			var vtec_codigo = $(this).parent().parent().find(".vtec_codigo");
        	if($(this).val() != ""){
            	buscar_t_versao(this, vtec_codigo);
            }else{
                $(vtec_codigo).html("<option value=\"\">Versão da Tecnologia</option>");
            }
        });

		$(document).on("click", "a.novo-escolta",function(){
			var conteiner = $("div.escolta");
			$.ajax({
				url: baseUrl + "solicitacoes_monitoramento/novo_escolta/"+ contador_equipe +"/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.append(data);
					setup_mascaras();
					autocomplete_escolta("RecebsmEscolta");
					contador_equipe++;
				},
				complete: function(){
					$.placeholder.shim();
				}
			});

			return false;
		});

		$(document).on("click", "a.novo-equipe",function(){
			var conteiner = $(this).parent().parent().parent().parent();
			var table = $(this).parents("table:first");

			$.ajax({
				url: baseUrl + "solicitacoes_monitoramento/novo_equipe/"+ table.attr("data-index") +"/"+ conteiner.children("tr").length +"/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.append(data);
					setup_mascaras();
				},
				complete: function(){
					$.placeholder.shim();
				}
			});
			
			return false;
		});

		$(document).on("click","a.novo-escolta-remove",function(){
			$(this).parents("table:eq(0)").remove();
			return false;
		});

		$(document).on("click","a.novo-equipe-remove",function(){
			$(this).parents("tr:eq(0)").remove();
			return false;
		});

		
	});
	
');
?>