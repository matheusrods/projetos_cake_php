<?php echo $this->BForm->create('DetalheGrupoExame', array('url'=>array('controller' => 'DetalhesGruposExames', 'action' => 'incluir',$codigo_grupo_economico,$codigo_cliente))); ?>
 <div class="well">
    <div class='row-fluid inline'>
      <?php echo $this->BForm->input('descricao', array('label' => 'Descrição', 'class' => 'input-xlarge')); ?>   
    </div>  
</div>    
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('controller' => 'DetalhesGruposExames', 'action' => 'index',$codigo_cliente), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>