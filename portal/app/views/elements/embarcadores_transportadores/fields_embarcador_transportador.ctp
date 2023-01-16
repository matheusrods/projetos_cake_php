<div class="row-fluid">
	<span class="span9">
		<div class="row-fluid inline">
		  <?php if($edit_mode): ?>
		      <?php echo $this->BForm->input('codigo',array('class' => 'input-mini', 'type' => 'text', 'label' => 'Código', 'readonly' => true)); ?>
		  <?php else: ?>
		      <?php echo $this->BForm->hidden('codigo'); ?> 
		  <?php endif; ?>
		  <?php echo $this->BForm->hidden('codigo_cliente_sub_tipo'); ?> 
		  <?php echo $this->BForm->hidden('data_inclusao', array('readonly' => $edit_mode)); ?>
		  <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'label' => 'CNPJ / CPF', 'readonly' => $edit_mode, 'maxlength' => 18)); ?>
		  <?php echo $this->BForm->input('inscricao_estadual', array('class' => 'input-medium', 'label' => 'RG / Inscrição Estadual', 'readonly' => $edit_mode)); ?>
		  <?php echo $this->BForm->input('ccm', array('label' => 'Incrição Municipal', 'class' => 'input-small', 'readonly' => $edit_mode)); ?>
		  <?php echo $this->BForm->input('suframa', array('class' => 'input-small', 'label' => 'SUFRAMA', 'readonly' => $edit_mode)); ?>
		</div>
		<div class="row-fluid inline">
		  <?php echo $this->BForm->input('cnae', array('label' => 'CNAE', 'class' => 'input-mini', 'readonly' => $edit_mode)); ?>
		  <?php echo $this->BForm->input('Cnae.descricao', array('class' => 'input-xxlarge', 'label' => 'RAMO DE ATIVIDADE', 'title' => (isset($this->data['Cnae']['descricao']) ? $this->data['Cnae']['descricao'] : ''), 'readonly' => true)); ?>
		</div>
	</span>
</div>
<?php echo $this->element('clientes_enderecos/fields', array('edit_mode' => $edit_mode )) ?>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>