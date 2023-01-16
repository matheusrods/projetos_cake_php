<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
</div>
<table class='table table-striped tablesorter'>
    <thead>
        <tr>
            <th><?= $this->Html->link('Login', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Nome', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Email', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('CPF', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Perfil', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Administrador', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Status', 'javascript:void(0)') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($usuarios as $usuario): ?>
        <tr>
            <td><?= $usuario['Usuario']['apelido'] ?></td>
            <td><?= $usuario['Usuario']['nome'] ?></td>
            <td><?= $usuario['Usuario']['email'] ?></td>
            <td><?= $buonny->documento($usuario['Usuario']['codigo_documento']) ?></td>
            <td><?= $usuario['Uperfil']['descricao'] ?></td>
            <?php if($usuario['Usuario']['admin'] == true) :?>
                <td><?= 'Sim' ?></td>
                <?php else: ?>
                <td><?= 'Não' ?></td>
            <?php endif; ?>
            <td><?= ($usuario['Usuario']['ativo'] ? 'ativo' : 'inativo') ?></td>
            <td><?=$html->link('', array('action' => 'editar_alertas_por_cliente', $usuario['Usuario']['codigo']), 
                array('class' => 'icon-edit', 'title' => 'Editar')
                ) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php $this->addScript($this->Javascript->codeBlock("
jQuery('table.table').tablesorter({sortList: [[0,1]], headers: {3: {sorter:false}} })")) ?>
