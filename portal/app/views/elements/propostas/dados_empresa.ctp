<h5>Dados da Empresa</h5>
<div class='well'>
  <div class="row-fluid inline">
      <?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'label' => 'Razão Social', 'required'=>false, 'readonly'=>$readonly)); ?>
      <?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'label' => 'Nome Fantasia', 'readonly'=>$readonly)); ?>
      <?php echo $this->BForm->input("tipo_cliente", array('class'=>'input-medium','options' => $tipo_cliente,'label' => 'Tipo Cliente', 'disabled'=>$readonly)) ?>
      <?php echo $this->BForm->input('cpf_cnpj', array('class' => 'input-medium','label' => 'CPF / CNPJ','div'=>Array('id'=>'divPropostaCPFCNPJ'),'required'=>true, 'readonly'=>$readonly)); ?>
      <?php echo $this->BForm->input('codigo_documento_real', array('class' => 'input-medium cnpj', 'label' => 'CNPJ / CPF Real', 'readonly' => $edit_mode));?>  </div>
</div>