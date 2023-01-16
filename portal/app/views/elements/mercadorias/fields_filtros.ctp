<div class="row-fluid inline">
  <?php echo $this->BForm->input('codigo_prod', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
  <?php echo $this->BForm->input('prod_descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>
  <?php echo $this->BForm->input('status',array('label' => false, 'empty' => 'Status','options' => array(1 => 'Ativo', 2 => 'Inativo'),'class'=>'input-medium'));?>
  
</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>