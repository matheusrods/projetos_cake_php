<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<div class='actionbar-right'><?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Nova Documentação'));?></div>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?= $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?= $this->Paginator->sort('Descrição', 'descricao') ?></th>
            <th colspan="3"><?= $this->Paginator->sort('Obrigatório', 'obrigatorio') ?></th>
        </tr>
    </thead>
    
    <tbody>
        <?php foreach ($tipos_documentos as $doc): ?>
        <tr>
            <td class="input-mini">
				<?= $doc['TipoDocumento']['codigo'] ?>
			</td>
            <td>
				<?= $doc['TipoDocumento']['descricao'] ?>
			</td>
            <td>
				<?php echo $doc['TipoDocumento']['obrigatorio'] == '1' ? 'Sim' : 'Não'; ?>
			</td>
            <td class="pagination-centered">
				<?//= $html->link('', array('action' => 'editar', $doc['TipoDocumento']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
			</td>
			<td class="pagination-centered">
                <?= $html->link('', array('action' => 'excluir', $doc['TipoDocumento']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?') ?>
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