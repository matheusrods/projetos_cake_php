<?php echo $this->BForm->create('ProfNegativacaoCliente', array('type' => 'post' ,'url' => array('controller' => 'profissionais_negativados_clientes','action' => 'editar',$this->passedArgs[0])));?>
<?php echo $this->BForm->hidden("ProfNegativacaoCliente.codigo")?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input("ProfNegativacaoCliente.codigo_cliente", array('label' => 'Cliente', 'class' => 'input-mini', 'readonly'=>TRUE )) ?>
	<?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>TRUE )) ?>
</div>
<div class="row-fluid inline parent">
	<?php echo $this->BForm->input('Profissional.codigo_documento', array('label' => 'CPF', 'class' => 'input-medium formata-cpf', 'readonly' => TRUE )); ?>
    <?php echo $this->BForm->input('Profissional.nome', array('label' => 'Nome', 'class' => 'input-xxlarge', 'readonly' => TRUE)); ?>        
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('Profissional.codigo') ?>
    <?php echo $this->BForm->input('codigo_negativacao', array('label'=>'Tipo Negativaçao', "empty"=>"Tipo de Negativaçao","options" => $tipo_negativacao)); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('observacao',array('class' => 'input-xxlarge','type'=>'textarea input-large','label' => 'Observação')); ?>   
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>