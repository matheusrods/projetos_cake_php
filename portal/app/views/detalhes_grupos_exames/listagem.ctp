<?php if(!empty($detalhes_grupos_exames)): ?>
    <?php 
        echo $paginator->options(array('update' => 'div.lista')); 
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
                <th><?php echo $this->Paginator->sort('Descrição', 'descricao') ?></th> 
                <th style='width:55px'>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($detalhes_grupos_exames as $detalhe_grupo_exame): ?>
            <tr>
                <td><?php echo $detalhe_grupo_exame['DetalheGrupoExame']['codigo'] ?></td>
                <td><?php echo $detalhe_grupo_exame['DetalheGrupoExame']['descricao'] ?></td>
                <td class="pagination-centered">
                    <?php echo $html->link('', array('controller' => 'DetalhesGruposExames', 'action' => 'trocar_status', $detalhe_grupo_exame['DetalheGrupoExame']['codigo'], $codigo_cliente), array('class' => 'icon-random', 'title' => 'Trocar Status do Grupo')) ?>
                    <?php if($detalhe_grupo_exame['DetalheGrupoExame']['ativo']): ?>
                        <span class="badge badge-empty badge-success" title="Ativo"></span>
                    <?php else: ?>
                        <span class="badge badge-empty badge-important" title="Inativo"></span>
                    <?php endif; ?>
                    <?php echo $html->link('', array('controller' => 'GruposExames', 'action' => 'index', $detalhe_grupo_exame['DetalheGrupoExame']['codigo'],$codigo_cliente), array('class' => 'icon-wrench', 'title' => 'Exames do Grupo')) ?>
                </td>
            </tr>
            <?php endforeach; ?>        
        </tbody>
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>