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
        <td><?php echo $endereco['uvw_endereco']['endereco_cep'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_tipo'].' ' ?><?php echo $endereco['uvw_endereco']['endereco_logradouro'] ?></td>
        <td class="numeric"><?php echo $endereco['FornecedorEndereco']['numero'] ?></td>
        <td><?php echo $endereco['FornecedorEndereco']['complemento'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_bairro'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_cidade'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_estado_abreviacao'] ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'fornecedores_enderecos', 'action' => 'atualizar', $endereco['FornecedorEndereco']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
            <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'excluir', 'onclick' => "return exclui_fornecedor_endereco({$endereco['FornecedorEndereco']['codigo']},{$endereco['FornecedorEndereco']['codigo_fornecedor']})")) ?>
            <?php echo $this->Form->input('endereco_'.$endereco['FornecedorEndereco']['codigo'], array('type' => 'hidden', 'value' => $endereco['FornecedorEndereco']['codigo'])) ?>
            <div class="clear"></div>
        </td>
    </tr>
<?php endforeach; ?>
</table>