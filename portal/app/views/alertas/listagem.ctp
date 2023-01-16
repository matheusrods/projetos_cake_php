<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="2">Alerta</th>
            <th colspan="3">Tratamento</th>
        </tr>
        <tr>
            <th><?= $this->Paginator->sort('Descrição', 'descricao') ?></th>
            <th><?= $this->Paginator->sort('Incluído', 'data_inclusao') ?></th>
            <th><?= $this->Paginator->sort('Tratado', 'data_tratamento') ?></th>
            <th><?= $this->Paginator->sort('Observação', 'observacao_tratamento') ?></th>
            <th><?= $this->Paginator->sort('Usuário', 'codigo_usuario_tratamento') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alertas as $alerta): ?>
        <tr>
            <td><?= $alerta['Alerta']['descricao'] ?></td>
            <td><?= $alerta['Alerta']['data_inclusao'] ?></td>
            <td><?= $alerta['Alerta']['data_tratamento'] ?></td>
            <td><?= $alerta['Alerta']['observacao_tratamento'] ?></td>
            <td><?= $alerta['Alerta']['codigo_usuario_tratamento'] ?></td>
<!--            <td class="pagination-centered">
                <?php //echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "excluir_area_atuacao({$area_atuacao['AreaAtuacao']['codigo']})")) ?>
			</td> -->
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
