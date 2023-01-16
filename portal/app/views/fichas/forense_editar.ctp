<?php echo $this->BForm->create('FichaForense', array('url'=>array('controller'=>'fichas', 'action'=>'forense_editar')));?>
<?php echo $this->BForm->hidden('codigo'); ?>


<div class="row-fluid inline">		
	<?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'readonly'=>'readonly', 'label' => 'Motorista', 'value'=>$profissional['Profissional']['nome'])); ?>	
	<?php echo $this->BForm->input('cpf', array('class' => 'input-medium', 'readonly'=>'readonly', 'label' => 'CPF', 'value'=>$profissional['Profissional']['codigo_documento'])); ?>	
	<?php echo $this->BForm->input('rg', array('class' => 'input-medium', 'readonly'=>'readonly', 'label' => 'RG', 'value'=>$profissional['Profissional']['rg'])); ?>		
	<?php echo $this->BForm->input('inclusao', array('class' => 'input-medium', 'readonly'=>'readonly', 'label' => 'Data Cadastro', 'value'=>$profissional['Profissional']['data_inclusao'])); ?>		
</div>

<div>    
    <?php echo $this->BForm->input('observacao', array('class' => 'input-xxlarge', 'type'=>'textarea', 'rows'=>10, 'cols'=>20, 'label' => 'Observação:')); ?>
</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'forense_voltar/'.$this->passedArgs[0]), array('class' => 'btn')); ?>
</div>

<?php echo $this->BForm->end(); ?>

