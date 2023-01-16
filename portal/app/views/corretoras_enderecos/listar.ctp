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
        <td><?php echo $endereco['CorretoraEndereco']['cep'] ?></td>
        <td><?php echo $endereco['CorretoraEndereco']['logradouro'] ?></td>
        <td class="numeric"><?php echo $endereco['CorretoraEndereco']['numero'] ?></td>
        <td><?php echo $endereco['CorretoraEndereco']['complemento'] ?></td>
        <td><?php echo $endereco['CorretoraEndereco']['bairro'] ?></td>
        <td><?php echo $endereco['CorretoraEndereco']['cidade'] ?></td>
        <td><?php echo $endereco['CorretoraEndereco']['estado_descricao'] ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'corretoras_enderecos', 'action' => 'atualizar', $endereco['CorretoraEndereco']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
            <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'excluir', 'onclick' => "return exclui_corretora_endereco({$endereco['CorretoraEndereco']['codigo']},{$endereco['CorretoraEndereco']['codigo_corretora']})")) ?>
            <?php echo $this->Form->input('endereco_'.$endereco['CorretoraEndereco']['codigo'], array('type' => 'hidden', 'value' => $endereco['CorretoraEndereco']['codigo'])) ?>
            <div class="clear"></div>
        </td>
    </tr>
<?php endforeach; ?>
</table>