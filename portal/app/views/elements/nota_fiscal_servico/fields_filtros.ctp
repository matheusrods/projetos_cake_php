<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Credenciado','NotaFiscalServico'); ?>
	<?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => false, 'label' => 'CNPJ')) ?>	
	<?php echo $this->BForm->input('numero_nota_fiscal', array('class' => 'input-medium', 'placeholder' => false, 'label' => 'Número Nota Fiscal', 'type' => 'text')) ?>
	<?php echo $this->BForm->input('codigo_nota_fiscal_status', array('options' => $status_nfs, 'empty' => 'Todos', 'class' => 'input-medium', 'label' => 'Status')); ?>
</div>
<div class="row-fluid inline">
    <p class="label label-info">Período por:</p>
</div>
<div class="row-fluid inline">
    <div class="span6">
        <div id='agrupamento'>
            <?php echo $this->BForm->input('tipo_data', array('type' => 'radio', 'options' => $tipo_data, 'default' => "I", 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
            <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
            <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>        
            <?= $this->BForm->input('codigo_tipo_servicos_nfs', array('empty' => 'Todos', 'class' => 'input-large', 'label' => 'Tipos de Serviços', 'options' => $tiposServicosList)); ?>
        </div>
    </div>
</div>