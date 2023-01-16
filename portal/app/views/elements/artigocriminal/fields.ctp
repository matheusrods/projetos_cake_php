<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('nome', array('class' => 'input-small', 'label' => 'Número Artigo')); ?>
    <?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge','type'=>'text', 'label' => 'Descricao')); ?>
    <?php echo $this->BForm->input('data_vigencia',array('class' => 'input-small data','type'=>'text', 'label' => 'Data da Vigência')); ?>  

</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>

</div>    


  <?php echo $this->Javascript->codeBlock('
       jQuery(document).ready(function(){
           setup_datepicker();
        });', false); 
    ?>    
