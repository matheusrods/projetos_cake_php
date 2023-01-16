<?php echo $paginator->options(array('update' => 'div.lista'));?>
<div class="row-fluid">
    <span class="span4">
        <? if(empty($authUsuario['Usuario']['codigo_cliente'])):?>
            <strong>Total:</strong> 
            <?php echo $this->Paginator->params['paging']['FichaScorecard']['count']; ?>
        <? endif; ?>
    </span>
</div>
<table  class="table table-striped horizontal-scroll" >
    <thead>
        <tr>
            <th class="input-small"><?php echo $this->Paginator->sort('Codigo', 'codigo') ?></th>
            <th class="input-large"><?php echo $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
            <th class="input-large"><?php echo $this->Paginator->sort('Usuário', 'codigo_usuario_inclusao') ?></th>
            <th class="input-xlarge"><?php echo $this->Paginator->sort('Profissional', 'nome') ?></th>
            <th class="input-medium"><?php echo $this->Paginator->sort('Codigo Documento', 'codigo_documento') ?></th>
            <th class="input-medium"><?php echo $this->Paginator->sort('Categoria', 'descricao') ?></th>
            <th class="input-medium"><?php echo $this->Paginator->sort('Data da Exclusão', 'data_alteracao') ?></th>
        </tr>
    </thead>
    <?php if(!empty($listar)):?>
    <?php foreach ( $listar as $key=> $dados): ?>
        <tr>
            <td><?php echo $dados['FichaScorecard']['codigo_cliente'];?></td>
            <td><?php echo $dados['Cliente']['razao_social'];?></td>
            <td><?php echo $dados['Usuario']['apelido'];?></td>
            <td><?php echo $dados['ProfissionalLog']['nome'];?></td>
            <td><?php echo comum::formatarDocumento($dados['ProfissionalLog']['codigo_documento']);?></td>
            <td><?php echo $dados['ProfissionalTipo']['descricao']?></td>
            <td><?php echo $dados['FichaScorecard']['data_inclusao']?></td>
        </tr>
    <?php endforeach;?>
    <?php endif;?>
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