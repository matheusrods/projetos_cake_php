<div class="row-fluid inline">
  <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
  <?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>
  <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array('Inativos', 'Ativos'), 'empty' => 'Status', 'default' => 1)); ?>
  
</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>