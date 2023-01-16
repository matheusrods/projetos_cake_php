<div class="row-fluid inline">
<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
<?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>  
<?php echo $this->BForm->input('codigo_conselho_profissional', array('class' => 'input-small', 'placeholder' => 'Conselho', 'label' => false, 'options' => $conselho_profissional,'empty' => 'Conselho', 'style' => 'width: 100px')) ?>  
<?php echo $this->BForm->input('numero_conselho', array('class' => 'input-medium', 'placeholder' => 'Número do Conselho', 'label' => false)) ?>  
<?php echo $this->BForm->input('conselho_uf', array('class' => 'input-small', 'placeholder' => 'Estado Conselho', 'label' => false, 'options' => $estado,'empty'=>'Estado' )) ?>  
</div>        