<tr>
	<td>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$tabela}.RecebsmEquipes.{$index}.nome", array('class' => 'input-large','label' => false, 'placeholder' => 'Equipe')); ?>
			<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$tabela}.RecebsmEquipes.{$index}.telefone", array('class' => 'input-medium telefone','label' => false, 'placeholder' => 'Telefone')); ?>
			<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$tabela}.RecebsmEquipes.{$index}.placa", array( 'class' => 'input-small placa-veiculo','label' => false, 'placeholder' => 'Placa')); ?>
			<?php echo $this->Html->link('<i class="icon-minus icon-black"></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-equipe-remove', 'escape' => false)); ?>
			<div class="btn-group">	
				<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$tabela}.RecebsmEquipes.{$index}.TVescViagemEscolta.vesc_armada", array('label' => false, 'class' => 'checkbox inline input-small', 'options' => array(1=>'Armada'), 'multiple' => 'checkbox')) ?>
				<?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$tabela}.RecebsmEquipes.{$index}.TVescViagemEscolta.vesc_velada", array('label' => false, 'class' => 'checkbox inline input-small', 'options' =>array(1=>'Velada'), 'multiple' => 'checkbox')) ?>
			</div>
		</div>
		<div class='row-fluid inline'>
		    <?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$tabela}.RecebsmEquipes.{$index}.TTecnTecnologia.tecn_codigo", array('label' => 'Tecnologia', 'empty' => 'Tecnologia','options' => $tecnologias, 'class' => 'tecn_codigo')) ?>
	        <?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$tabela}.RecebsmEquipes.{$index}.TVescViagemEscolta.vesc_vtec_codigo", array('label' => 'Versão', 'empty' => 'Versão da Tecnologia', 'options' => '', 'class' => 'vtec_codigo')) ?>
	        <?php echo $this->BForm->input("Recebsm.RecebsmEscolta.{$tabela}.RecebsmEquipes.{$index}.TVescViagemEscolta.vesc_numero_terminal", array('label' => 'Número Terminal')) ?>
	    </div>
	</td>
</tr>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		buscar_t_versao("#RecebsmRecebsmEscolta'.$tabela.'RecebsmEquipes'.$index.'TTecnTecnologiaTecnCodigo", "#RecebsmRecebsmEscolta'.$tabela.'RecebsmEquipes'.$index.'TVescViagemEscoltaVescVtecCodigo");
	});
');
?>