<?php foreach ($objetos as $objeto): ?>
    <tr>
        <td><?= $nivel.' '.$objeto['ObjetoAcl']['descricao'] ?></td>
        <td><?= $objeto['ObjetoAcl']['aco_string'] ?></td>
        
		<td>
    		<?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusObjeto('{$objeto['ObjetoAcl']['id']}','{$objeto['ObjetoAcl']['homologado']}')"));?>
    	</td>
        <?php if($objeto['ObjetoAcl']['homologado']== 1): ?>
        	<td>	
        		<span class="badge-empty badge badge-success" title="Na produção"></span>
        	</td>
        <?php else:?>
        	
        	<td>
        	 	<span class="badge-empty badge badge-transito" title="Em desenvolvimento"></span>
        	</td>	
        <?php endif; ?>
        <td><?php echo ($objeto['ObjetoAcl']['aco_string'] != ' ' ? $html->link('', array('controller' => 'objetos_acl', 'action' => 'ver_perfis', $objeto['ObjetoAcl']['id']), array('class' => 'icon-eye-open', 'title' => 'Ver perfis')) : '') ?></td>
        <td><?php echo $html->link('', array('controller' => 'objetos_acl', 'action' => 'editar', $objeto['ObjetoAcl']['id']), array('class' => 'icon-edit', 'title' => 'Editar objeto')); ?></td>
        <td><?php echo $html->link('', array('controller' => 'objetos_acl', 'action' => 'excluir', $objeto['ObjetoAcl']['id']), array('class' => 'icon-trash', 'title' => 'Excluir objeto'), 'Confirma exclusão?'); ?></td>
        <td><?php echo $html->link('', array('controller' => 'dependencias_obj_acl', 'action' => 'index', $objeto['ObjetoAcl']['id']), array('class' => 'icon-wrench', 'title' => 'Gerenciar dependências')); ?></td>
    </tr>
    <?php if (!empty($objeto['children'])): ?>
        <?php echo $this->element('objetos_acl/listagem', array('objetos' => $objeto['children'], 'nivel' => $nivel.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')) ?>
    <?php endif; ?>
<?php endforeach; ?> 