<div class='well'>
    <strong>Objeto: </strong><?php echo $this->Html->tag('span', $objeto['ObjetoAcl']['descricao']); ?>
    <strong>Aco String: </strong><?php echo $this->Html->tag('span', $objeto['ObjetoAcl']['aco_string']); ?>
</div>
<?php echo $this->BForm->create('DependenciaObjAcl', array('url' => array('controller' => 'dependencias_obj_acl', 'action' => 'incluir', $this->passedArgs[0]))); ?>
<?php echo $this->element('dependencias_obj_acl/fields'); ?>