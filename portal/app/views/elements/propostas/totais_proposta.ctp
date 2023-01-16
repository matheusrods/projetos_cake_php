<h5>Valores da Proposta</h5>
<div class='well'>
  	<div class="row-fluid inline">
      	<?php echo $this->BForm->input('valor_total_itens', array('class' => 'input-medium','type'=>'text', 'label' => 'Vl. Total Serviços', 'readonly'=>true)); ?>
      	<?php echo $this->BForm->input('perc_desconto_proposta', array('class' => 'input-mini','type'=>'text', 'label' => '% Desconto', 'readonly'=>true)); ?>
      	<?php echo $this->BForm->input('valor_total_desconto', array('class' => 'input-medium','type'=>'text', 'label' => 'Vl. Desconto', 'readonly'=>true)); ?>
      	<?php echo $this->BForm->input('valor_total_proposta', array('class' => 'input-medium','type'=>'text', 'label' => 'Vl. Proposta', 'readonly'=>true)); ?>
  	</div>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		//
		
	});
');
?>