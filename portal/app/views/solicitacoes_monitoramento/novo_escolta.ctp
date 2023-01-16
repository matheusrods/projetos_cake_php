<table class='table table-striped' data-index="<?php echo $contador ?>">
	<thead>
		<th>
			<div class="row-fluid inline">
				<?php echo $this->Buonny->input_escolta($this, 'Recebsm.RecebsmEscolta','eesc_codigo',$contador,"Empresa Escolta",TRUE) ?>
				<div class="control-group input text">
					<label for="RecebsmDtaFim">&nbsp</label>
					<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-escolta-remove', 'escape' => false)); ?>
				</div>
			</div>
		</th>
	</thead>
	
	<tbody>
		<tr>
			<td>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$contador}.RecebsmEquipes.0.nome", array('class' => 'input-large','label' => false, 'placeholder' => 'Equipe')); ?>
					<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$contador}.RecebsmEquipes.0.telefone", array('class' => 'input-medium telefone','label' => false, 'placeholder' => 'Telefone')); ?>
					<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$contador}.RecebsmEquipes.0.placa", array( 'class' => 'input-small placa-veiculo','label' => false, 'placeholder' => 'Placa')); ?>
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-equipe', 'escape' => false)); ?>
					<div class="btn-group">	
						<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$contador}.RecebsmEquipes.0.TVescViagemEscolta.vesc_armada", array('label' => false, 'class' => 'checkbox inline input-small', 'options' => array(1=>'Armada'), 'multiple' => 'checkbox')) ?>
						<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$contador}.RecebsmEquipes.0.TVescViagemEscolta.vesc_velada", array('label' => false, 'class' => 'checkbox inline input-small', 'options' =>array(1=>'Velada'), 'multiple' => 'checkbox')) ?>
					</div>
				</div>
				<div class='row-fluid inline'>
			        <?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$contador}.RecebsmEquipes.0.TTecnTecnologia.tecn_codigo", array('label' => 'Tecnologia', 'empty' => 'Tecnologia','options' => $tecnologias, 'class' => 'tecn_codigo')) ?>
			        <?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$contador}.RecebsmEquipes.0.TVescViagemEscolta.vesc_vtec_codigo", array('label' => 'Versão', 'empty' => 'Versão da Tecnologia', 'options' => '', 'class' => 'vtec_codigo')) ?>
			        <?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$contador}.RecebsmEquipes.0.TVescViagemEscolta.vesc_numero_terminal", array('label' => 'Número Terminal')) ?>
			    </div>
			</td>
		</tr>
	</tbody>

</table>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		if($("table[data-index='.$contador.']  .tecn_codigo").val() != ""){
			buscar_t_versao("table[data-index='.$contador.'] .tecn_codigo", "table[data-index='.$contador.'] .vtec_codigo");
		}else{
            $("table[data-index='.$contador.'] .vtec_codigo").html("<option value=\"\">Versão da Tecnologia</option>");
        }

        $(document).on("change","table[data-index='.$contador.'] .tecn_codigo",function(){
			var vtec_codigo = $(this).parent().parent().find(".vtec_codigo");
        	if($(this).val() != ""){
            	buscar_t_versao(this, vtec_codigo);
            }else{
                $(vtec_codigo).html("<option value=\"\">Versão da Tecnologia</option>");
            }
        });
	});
');
?>