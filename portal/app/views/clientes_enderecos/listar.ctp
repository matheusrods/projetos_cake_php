<table class="table table-striped">
    <thead>
        <tr>
            <th>Tipo</th>
            <th>CEP</th>
            <th>Logradouro</th>
            <th class="numeric">Número</th>
            <th>Complemento</th>
            <th>Bairro</th>
            <th>Cidade</th>
            <th>UF</th>
            <th></th>
        </tr>
    </thead>
<?php foreach ($enderecos as $endereco): ?>
    <tr>
        <td><?php echo $endereco['TipoContato']['descricao'] ?></td>
        <td><?php echo $endereco['ClienteEndereco']['cep'] ?></td>
        <td><?php echo $endereco['ClienteEndereco']['logradouro'] ?></td>
        <td class="numeric"><?php echo $endereco['ClienteEndereco']['numero'] ?></td>
        <td><?php echo $endereco['ClienteEndereco']['complemento'] ?></td>
        <td><?php echo $endereco['ClienteEndereco']['bairro'] ?></td>
        <td><?php echo $endereco['ClienteEndereco']['cidade'] ?></td>
        <td><?php echo $endereco['ClienteEndereco']['estado_abreviacao'] ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'clientes_enderecos', 'action' => 'atualizar', $endereco['ClienteEndereco']['codigo']), array('class' => 'icon-edit dialog_cliente_endereco', 'title' => 'editar')) ?>
            <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'excluir', 'onclick' => "return exclui_cliente_endereco({$endereco['ClienteEndereco']['codigo']},{$endereco['ClienteEndereco']['codigo_cliente']})")) ?>
            <?php echo $this->Form->input('endereco_'.$endereco['ClienteEndereco']['codigo'], array('type' => 'hidden', 'value' => $endereco['ClienteEndereco']['codigo'])) ?>
            <div class="clear"></div>
        </td>
    </tr>
<?php endforeach; ?>
</table>