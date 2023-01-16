<table class='table table-striped tablesorter'>
    <thead>
        <tr>
            <th><?= $this->Html->link('Login', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Nome', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Status', 'javascript:void(0)') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($usuarios as $usuario): ?>
        <tr>
            <td><?= $usuario['Usuario']['apelido'] ?></td>
            <td><?= $usuario['Usuario']['nome'] ?></td>
            <td><?= ($usuario['Usuario']['ativo'] ? 'ativo' : 'inativo') ?></td>
            <td>
                <?= $html->link('', array('action' => 'editar_por_filial', $usuario['Usuario']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'onclick' => 'return open_dialog(this, \'Editar Usuário\', 800)')) ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter({sortList: [[0,1]], headers: {3: {sorter:false}} })")) ?> 