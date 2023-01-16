<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'embarcadores_transportadores', 'action' => 'consulta_para_incluir')) );?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium cnpj', 'label' => FALSE, 'placeholder' => 'CNPJ', 'maxlength' => 18)); ?>
</div>
<div class="form-actions">
   <?php echo $this->BForm->submit('Avançar', array('div' => false, 'class' => 'btn btn-primary')); ?>
   <?php echo $this->Html->link('Voltar', array( 'action' => 'embarcador_transportador'), array('class' => 'btn')); ?>
</div>	
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeblock("jQuery(document).ready(function() { 
	$('#ClienteCodigoDocumento').blur(function(){        
      cnpj = $('#ClienteCodigoDocumento').val( );
      $('#ClienteCodigoDocumento').val(cnpj.replace(/\D/g,''));
	});
 });"); ?>