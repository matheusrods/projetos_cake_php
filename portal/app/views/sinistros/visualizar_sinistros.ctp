<div class="row-fluid inline">	
	<div style="float:left;">
		<?php echo $this->BForm->hidden('codigo');?>
		<?php echo $this->BForm->input('Sinistro.sm', array('class' => 'input-small', 'label' => 'SM','readonly'=>'readonly')); ?>	<span id='load'></span>
	</div>
	<div id="dt-sm-1" style="display:none;">
		<?php echo $this->BForm->input('Sinistro.embarcador', array('class' => 'input-xlarge', 'readonly'=>'readonly', 'label' => 'Embarcador')); ?>	
		<?php echo $this->BForm->input('Sinistro.transportador', array('class' => 'input-xlarge', 'readonly'=>'readonly', 'label' => 'Transportador')); ?>	
		<?php echo $this->BForm->input('Sinistro.motorista', array('class' => 'input-xlarge', 'readonly'=>'readonly', 'label' => 'Motorista')); ?>			
	</div>
	<div id="dt-sm-2" style="margin-left:110px; display:none;">
		<?php echo $this->BForm->input('Sinistro.cpf', array('class' => 'input-medium', 'readonly'=>'readonly', 'label' => 'CPF')); ?>
		<?php echo $this->BForm->input('Sinistro.seguradora', array('class' => 'input-xlarge', 'readonly'=>'readonly', 'label' => 'Seguradora')); ?>
		<?php echo $this->BForm->input('Sinistro.corretora', array('class' => 'input-xlarge', 'readonly'=>'readonly', 'label' => 'Corretora')); ?>
		<?php echo $this->BForm->input('Sinistro.veiculo', array('class' => 'input-small', 'readonly'=>'readonly', 'label' => 'Veículo')); ?>
	</div>
</div>	   

<div class="row-fluid inline">
	
	<?php echo $this->BForm->input('Sinistro.data_evento', array('class' => 'input-small data', 'type'=>'text', 'label' => 'Data Evento','readonly'=>'readonly')); ?>
	<?php echo $this->BForm->input('Sinistro.hora', array('class' => 'input-small hora', 'label' => 'Hora Evento','readonly'=>'readonly')); ?>		        
	<?php echo $this->BForm->input('Sinistro.natureza', array('class' => 'input-medium', 'label'=>'Natureza', 'options'=>$natureza,'empty'=>'Escolha a natureza','disabled'=>'disabled')); ?>


	<?php echo $this->BForm->input('Sinistro.status_veiculo', array('class' => 'input-medium', 'label'=>'Status Veículo', 'options'=>$status_veiculo, 'empty'=>'Escolha o status do veíclo','disabled'=>'disabled')); ?>
</div>	

<div class="row-fluid inline">
	<?php echo $this->BForm->input('Sinistro.valor_carga', array('class' => 'input-small moeda', 'label' => 'Valor Carga', 'maxlength'=>false,'readonly'=>'readonly')); ?>	
	<?php echo $this->BForm->input('Sinistro.valor_sinistrado', array('class' => 'input-small moeda', 'label' => 'Valor Sinistrado','maxlength'=>false,'readonly'=>'readonly')); ?>
	<?php echo $this->BForm->input('Sinistro.valor_recuperado', array('class' => 'input-small moeda', 'label' => 'Valor Recuperado','maxlength'=>false,'readonly'=>'readonly')); ?>
</div>

<div class="row-fluid inline">
	<?php echo $this->BForm->input('Sinistro.latitude', array('class' => 'input-small', 'label' => 'Latitude','readonly'=>'readonly')); ?>
	<?php echo $this->BForm->input('Sinistro.longitude', array('class' => 'input-small', 'label' => 'Longitude','readonly'=>'readonly')); ?>
	<?php echo $this->BForm->input('Sinistro.endereco', array('class' => 'input-large', 'label' => 'Endereco','readonly'=>'readonly')); ?>
	<?php //echo $this->BForm->input('cidade', array('class' => 'input-medium', 'label' => 'Cidade')); ?>	
	<?php echo $this->BForm->input('Sinistro.cidade',    array('class' => 'input-large ui-autocomplete-input', 'label' => 'Informe uma Cidade','readonly'=>'readonly')) ?>
    
</div>

<div class="row-fluid inline">
	<?php echo $this->BForm->input('Sinistro.modo_de_operacao', array('class' => 'input-xxlarge', 'type'=>'textarea', 'rows'=>4, 'label' => 'Modus Operandi','readonly'=>'readonly')); ?>		
</div>

<div>
	<?php echo $this->BForm->input('Sinistro.observacao', array('class' => 'input-xxlarge', 'type'=>'textarea', 'rows'=>4,'label' => 'Observação','readonly'=>'readonly')); ?>
</div>

<div>
	<?php echo $this->BForm->input('Sinistro.atuacao_geral', array('class' => 'input-xxlarge', 'type'=>'textarea', 'rows'=>4,'label' => 'Atuação Central','readonly'=>'readonly')); ?>
</div>

<div>
	<?php echo $this->BForm->input('Sinistro.avalicao_geral', array('class' => 'input-medium', 'label'=>'Avaliação Geral', 'options'=>$avaliacao_geral, 'empty'=>'Escolha uma avaliação','disabled'=>'disabled')); ?>
</div>

<?php echo $this->BForm->end(); ?>