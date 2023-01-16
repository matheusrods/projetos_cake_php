<?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?= $this->Paginator->sort('CEP', 'endereco_cep') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Tipo', 'endereco_tipo') ?></th>
            <th class="list-enderecos"><?= $this->Paginator->sort('Endereco', 'endereco_codigo') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Bairro', 'endereco_bairro') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Cidade', 'endereco_cidade') ?></th>
            <th><?= $this->Paginator->sort('Estado', 'endereco_estado') ?></th>
			<th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($enderecos as $endereco): ?>
        <tr>
            <td class="input-mini"><?= $endereco['VEndereco']['endereco_cep'] ?></td>
            <td class="input-medium"><?= $endereco['VEndereco']['endereco_tipo'] ?></td>
            <td class="list-enderecos"><?= $endereco['VEndereco']['endereco_logradouro'] ?></td>
            <td class="input-medium"><?= $endereco['VEndereco']['endereco_bairro'] ?></td>
            <td class="input-medium"><?= $endereco['VEndereco']['endereco_cidade'] ?></td>
            <td><?= $endereco['VEndereco']['endereco_estado'] ?></td>
            <td><?= $html->link('', array('action' => 'editar', $endereco['VEndereco']['endereco_codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?></td>
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