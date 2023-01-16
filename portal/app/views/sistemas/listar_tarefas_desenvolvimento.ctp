</br>

<div class='row-fluid inline'>
    <?php echo $this->Paginator->options(array('update' => '.lista')); ?>
    <table class="table table-striped">
        <thead>
            <th class='input-large'>Usuário</th>
            <th class='input-xlarge'>Título </th>
            <th class='input-medium'>Data Criação </th>
            <th class='input-medium'>Data Alteração </th>
            <th class='input-medium'>Data Publicação</th> 
            <th class='input-medium'>Tipo</th>  
            <th style='width:13px'></th>
            <th style='width:13px'></th>
            <th style='width:13px'></th>
        </thead>
    
        <tbody>
            <?php foreach($lista_tarefas as $tarefas): ?>

                <tr> 
                    <td><?php echo $tarefas['Usuario']['apelido']?></td>
                    <td><?php echo $tarefas['TarefaDesenvolvimento']['titulo']?></td>
                    <td><?php echo $tarefas['TarefaDesenvolvimento']['data_inclusao']?></td>
                    <td><?php echo $tarefas['TarefaDesenvolvimento']['data_alteracao']?></td>
                    <td><?php echo $tarefas['TarefaDesenvolvimento']['data_publicacao']?></td>
                    <td><?php echo $tarefas['TarefaDesenvolvimentoTipo']['descricao']?></td>
                    <td> 
                        <?php if( ($tarefas['TarefaDesenvolvimentoTipo']['codigo'] == 1 && $tarefas['TarefaDesenvolvimento']['status']!= 3) && $tarefas['Usuario']['codigo'] == $authUsuario['Usuario']['codigo']  ):?>
                            <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusTarefaDesenvolvimento('{$tarefas['TarefaDesenvolvimento']['codigo']}','{$tarefas['TarefaDesenvolvimento']['status']}')"));?>
                        <?php elseif($tarefas['TarefaDesenvolvimentoTipo']['codigo'] == 2 && $tarefas['Usuario']['codigo'] == $authUsuario['Usuario']['codigo']  ): ?>
                        	<?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusTarefaDesenvolvimentoDelphi('{$tarefas['TarefaDesenvolvimento']['codigo']}','{$tarefas['TarefaDesenvolvimento']['status']}')"));?>
                        <?php endif; ?>
                    </td>

                    <td>
                    <?php if($tarefas['TarefaDesenvolvimento']['status']== 1):?>
                        <span class="badge-empty badge badge-transito" title="Em andamento"></span>
                    <?php elseif($tarefas['TarefaDesenvolvimento']['status']== 2): ?>
                        <span class="badge-empty badge badge-important" title="Homologação"></span>
                    <?php elseif($tarefas['TarefaDesenvolvimento']['status']== 3): ?>
                        <span class="badge-empty badge badge-success" title="Pronto"></span>
                    <?php endif; ?>
                    </td>
                    
                    <td> 
                        <?php echo $html->link('', array('controller' => 'Sistemas', 'action' => 'editar_tarefas_desenvolvimento',$tarefas['TarefaDesenvolvimento']['codigo']), array('class' => 'icon-edit', 'title' => 'Alterar')); ?>
                    </td>

                    
                </tr> 

            <?php endforeach; ?>       
        </tbody>
        <tfoot>
            <tr>
                <td><strong>Total</strong></td>
                <td class="input-xlarge"><strong><?php echo $count ?>&nbsp;Tarefa(s)</strong></td>
                <td></td>
                <td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>                
            </tr>
        </tfoot>
    </table>
    <div class='row-fluid'>
	    <div class='numbers span6'>
	        <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	        <?php echo $this->Paginator->numbers(); ?>
	        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	    </div>
	    <div class='counter span6'>
	        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	    </div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
</div>    