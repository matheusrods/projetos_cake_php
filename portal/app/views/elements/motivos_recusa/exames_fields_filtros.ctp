<div class="row-fluid inline">
    <?php echo $this->BForm->input('ativo', array('type' => 'select', 'options' => $status, 'empty' => 'Todos', 'label' => 'Status', 'class' => 'input-large')); ?>
    <?php echo $this->BForm->input('descricao', array('type' => 'text', 'label' => 'Descrição', 'class' => 'input-large')); ?>
</div>