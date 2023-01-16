<div class="row-fluid inline">
  <?php echo $this->BForm->input('ttra_codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
  <?php echo $this->BForm->input('ttra_descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>
   
</div>        
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>