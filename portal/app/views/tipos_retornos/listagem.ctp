<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?php echo $this->Paginator->sort('Tipo de Retorno', 'descricao') ?></th>
            <th><?php echo $this->Paginator->sort('Proprietario', 'proprietario') ?></th>
            <th><?php echo $this->Paginator->sort('Profissional', 'profissional') ?></th>
            <th><?php echo $this->Paginator->sort('Usuario Interno', 'usuario_interno') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tipos_retornos as $tipo_retorno): ?>
        <tr>
            <td class="input-mini"><?php echo $tipo_retorno['TipoRetorno']['codigo'] ?></td>
            <td><?php echo $tipo_retorno['TipoRetorno']['descricao'] ?></td>
            <td><?php echo ($tipo_retorno['TipoRetorno']['proprietario'] == true ? 'Sim' : 'Não') ?></td>
            <td><?php echo ($tipo_retorno['TipoRetorno']['profissional'] == true ? 'Sim' : 'Não') ?></td>
            <td><?php echo ($tipo_retorno['TipoRetorno']['usuario_interno'] == true ? 'Sim' : 'Não') ?></td>
            <td class="input-mini">
                <?php echo $html->link('', array('action' => 'editar', $tipo_retorno['TipoRetorno']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
				<?php echo $html->link('', array('controller' => 'tipos_retornos', 'action' => 'excluir', $tipo_retorno['TipoRetorno']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Tipo de Retorno'), 'Confirma exclusão?'); ?>
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