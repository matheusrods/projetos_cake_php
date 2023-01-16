<div class='well'>
    <strong>Objeto: </strong><?php echo $this->Html->tag('span', $objeto['ObjetoAcl']['descricao']); ?>
    <strong>Aco String: </strong><?php echo $this->Html->tag('span', $objeto['ObjetoAcl']['aco_string']); ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'dependencias_obj_acl', 'action' => 'incluir', $this->passedArgs[0]), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novo Objeto'));?>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Aco String</th>
            <th style="width:13px"></th>
            <th style="width:13px"></th>
        </tr>
    </thead>
    <?php foreach ($dependencias as $dependencia): ?> 
        <tr>
            <td><?= $dependencia['DependenciaObjAcl']['aco_string'] ?></td>
            <td><?php echo $html->link('', array('controller' => 'dependencias_obj_acl', 'action' => 'editar', $dependencia['DependenciaObjAcl']['id']), array('class' => 'icon-edit', 'title' => 'Editar dependência')); ?></td>
            <td><?php echo $html->link('', array('controller' => 'dependencias_obj_acl', 'action' => 'excluir', $dependencia['DependenciaObjAcl']['id']), array('class' => 'icon-trash', 'title' => 'Excluir dependência'), 'Confirma exclusão?'); ?></td>
        </tr>
    <?php endforeach; ?> 
</table>
<div class="form-actions">
  <?= $html->link('Voltar', array('controller' => 'objetos_acl', 'action' => 'index'), array('class' => 'btn')); ?>
</div>    