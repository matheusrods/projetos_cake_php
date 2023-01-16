<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('id'); ?>
    <?php echo $this->Tree->input('parent_id', array('class' => 'input-xlarge bselect2', 'label' => 'Pai', 'options' => $objetos, 'empty' => 'Selecionar Objeto Pai', 'model' => 'ObjetoAcl', 'field' => 'descricao')); ?>
    <?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'label' => 'Descrição')); ?>
    <?php echo $this->BForm->input('aco_string', array('class' => 'input-xlarge', 'label' => 'Aco String')); ?>
    <?php echo $this->BForm->input('codigo_tarefa_desenvolvimento', array('class' => 'input-large', 'label' => 'Controle Tarefa','empty' => 'Selecionar Tarefa' ,'options' => $tarefas)); ?>
</div>
<div class="row-fluid inline">
    <span class="label label-info">Perfil Permitido</span>
    <span class='pull-right'>
        <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("perfil")')) ?>
        <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("perfil")')) ?>
    </span>
    <div id='perfil'>
        <?php echo $this->BForm->input('codigo_tipo_perfil', array('label' => false, 'class' => 'checkbox inline input-medium', 'options' => $lista_perfis, 'multiple' => 'checkbox')); ?>
    </div>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery(".bselect2").select2();
    });', false);
?>