<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir_excecao'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Área Atuação'));?>
</div>
<?php if(isset($clientes_excecoes) && !empty($clientes_excecoes)):?>
<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?php echo $this->Paginator->sort('Código', 'codigo_cliente') ?></th>
            <th><?php echo $this->Paginator->sort('Cliente', 'codigo_cliente') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes_excecoes as $cliente_excecao): ?>
        <tr>
            <td class="input-mini">
                <?php echo $cliente_excecao['AutotracExcecao']['codigo_cliente'] ?>
            </td>
            <td>
                <?php echo $cliente_excecao['Cliente']['razao_social'] ?>
            </td>
            <td class="input-mini">
				<?php echo $html->link('', array('action' => 'excluir_excecao', $cliente_excecao['AutotracExcecao']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir área de atuação'), 'Confirma exclusão?'); ?>
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
    <div class="alert">Nenhum registro encontrado.</div>
<?php endif;?>
