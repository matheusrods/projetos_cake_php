<?php echo $this->BForm->create('TProdProduto', array('url' => array('controller' => 'mercadorias', 'action' => 'editar', $codigo_produto))); ?>

<div class="well">
    
    <div class='row-fluid inline'>
      <?php echo $this->BForm->hidden('prod_codigo') ?>
      <?php echo $this->BForm->input('prod_descricao', array('label' => 'Descrição', 'class' => 'input-xlarge')); ?>
    </div>  
    
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('controller' => 'mercadorias', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>