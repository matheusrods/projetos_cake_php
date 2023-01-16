<div class="row-fluid inline">  
	<div class="row-fluid inline">
		<div class="row-fluid inline">
			<span class="label label-info">Fornecedor:</span>
		</div>
    	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código *','Fornecedor','NotaFiscalServico');?> 
		<?php echo $this->BForm->input('cnpj_fornecedor', array('class' => 'input-medium cnpj', 'label' => 'CNPJ Fornecedor')); ?>
		<?php echo $this->BForm->input('razao_social', array('class' => 'input-medium', 'label' => 'Razão Social')); ?>
		<?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-medium', 'label' => 'Nome Fantasia')); ?>
	</div>
	<div class="row-fluid inline">
    	<span class="label label-info">Data da nota fiscal(Inclusão da nota/ Baixa do pedido) :</span>
	</div>
	<div class="row-fluid inline">
        <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
        <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
    </div>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_pedido_exame', array('class' => 'input-medium', 'label' => 'Número do Pedido *')); ?>
	<?php echo $this->BForm->input('funcionario', array('label' => 'Funcionário', 'class' => 'input-large', 'placeholder' => 'Nome Funcionário', 'type'=>'text')); ?>
	<?php echo $this->BForm->input('cpf_funcionario', array('label' => 'CPF Funcionário *', 'placeholder' => 'CPF', 'class' => 'input-medium cpf')); ?>
	<?php echo $this->BForm->input('numero_nota_fiscal', array('maxlength' => '10','label' => 'Número NFS *', 'placeholder' => 'Nota fiscal', 'class' => 'input-medium')); ?>
	<?php echo $this->BForm->input('concluida', array('label' => 'Nota Concluída?', 'class' => 'input-small', 'default' => 2,'options' => array(1 =>'Sim',2 => 'Não'),)); ?>
</div>