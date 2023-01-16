<?php if (empty($listar)): ?>
    <div class="alert">
        Nenhum registro encontrado.
    </div>
<?php else:     
    echo $this->Paginator->options(array('update' => 'div.lista')); 
?>
<div class='row-fluid'>
    <table class="table table-striped" style='table-layout:fixed' id="lista_tratativa_evento_sistema"> 
        <thead>
            <tr>
            
                <th class="input-small"><?php echo $this->Paginator->sort('Código', 'TTesiTratativaEventoSistema.tesi_codigo') ?></th>
                <th class="input-xxlarge"><?php echo $this->Paginator->sort('Evento', 'TEspaEventoSistemaPadrao.espa_descricao') ?></th>                
                <th class="input-xlarge"><?php echo $this->Paginator->sort('Descrição', 'TTesiTratativaEventoSistema.tesi_descricao') ?></th>
                <th class="input-mini numeric"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($listar as $tipo_evento):  ?>
                <tr>
                    <td><?= $tipo_evento['TTesiTratativaEventoSistema']['tesi_codigo'] ?></td>
                    <td><?= $tipo_evento['TEspaEventoSistemaPadrao']['espa_descricao'] ?></td>
                    <td><?= $tipo_evento['TTesiTratativaEventoSistema']['tesi_descricao'] ?></td>

                    <td class="numeric"><?php
                        echo $html->link('', array('action' => 'editar', $tipo_evento['TTesiTratativaEventoSistema']['tesi_codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
                        echo $html->link('', array('action' => 'excluir', $tipo_evento['TTesiTratativaEventoSistema']['tesi_codigo']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?'); 
                    ?></td>
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
<?php endif; ?>

