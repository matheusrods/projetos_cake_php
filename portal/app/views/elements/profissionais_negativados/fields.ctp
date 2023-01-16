<div class="row-fluid inline parent">
        <h4>Buscar Profissional</h4> 
        <?php echo $this->element('profissionais/fields', array('edit_mode' => $edit_mode)) ?>
    </div>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('codigo') ?>
    <?php echo $this->BForm->hidden('codigo_profissional') ?>
    <?php echo $this->BForm->input('codigo_negativacao',array('label'=>'Tipo Negativaçao', "options" => $tipoNegativacao)); ?>
    <?php echo $this->BForm->input('observacao',array('class' => 'input-xxlarge data','type'=>'textarea input-large','label' => 'Observação')); ?>   
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>