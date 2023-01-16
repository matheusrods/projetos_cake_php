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
        <td><?php echo $endereco['SeguradoraEndereco']['cep'] ?></td>
        <td><?php echo $endereco['SeguradoraEndereco']['logradouro'] ?></td>
        <td class="numeric"><?php echo $endereco['SeguradoraEndereco']['numero'] ?></td>
        <td><?php echo $endereco['SeguradoraEndereco']['complemento'] ?></td>
        <td><?php echo $endereco['SeguradoraEndereco']['bairro'] ?></td>
        <td><?php echo $endereco['SeguradoraEndereco']['cidade'] ?></td>
        <td><?php echo $endereco['SeguradoraEndereco']['estado_descricao'] ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'seguradoras_enderecos', 'action' => 'atualizar', $endereco['SeguradoraEndereco']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
            <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'excluir', 'onclick' => "return exclui_seguradora_endereco({$endereco['SeguradoraEndereco']['codigo']},{$endereco['SeguradoraEndereco']['codigo_seguradora']})")) ?>
            <?php echo $this->Form->input('endereco_'.$endereco['SeguradoraEndereco']['codigo'], array('type' => 'hidden', 'value' => $endereco['SeguradoraEndereco']['codigo'])) ?>
            <div class="clear"></div>
        </td>
    </tr>
<?php endforeach; ?>
</table>