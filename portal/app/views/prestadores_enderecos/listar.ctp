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
        <td class="numeric"><?php echo $endereco['PrestadorEndereco']['numero'] ?></td>
        <td><?php echo $endereco['PrestadorEndereco']['complemento'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_bairro'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_cidade'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_estado_abreviacao'] ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'prestadores_enderecos', 'action' => 'atualizar', $endereco['PrestadorEndereco']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
            <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'excluir', 'onclick' => "return exclui_prestador_endereco({$endereco['PrestadorEndereco']['codigo']},{$endereco['PrestadorEndereco']['codigo_prestador']})")) ?>
            <?php echo $this->Form->input('endereco_'.$endereco['PrestadorEndereco']['codigo'], array('type' => 'hidden', 'value' => $endereco['PrestadorEndereco']['codigo'])) ?>
            <div class="clear"></div>
        </td>
    </tr>
<?php endforeach; ?>
</table>
<?php echo $javascript->codeBlock("
function exclui_prestador_endereco(codigo_prestador_endereco, codigo_prestador){
    if (confirm('Deseja realmente excluir ?'))
        jQuery.ajax({
            type: 'POST',
            url: baseUrl + 'prestadores_enderecos/excluir/' + codigo_prestador_endereco + '/' + Math.random(),
            success: function(data) {
                var div = jQuery('#endereco-prestador');
                bloquearDiv(div);
                div.load(baseUrl + 'prestadores_enderecos/listar/' + codigo_prestador + '/' + Math.random() );
            }
        });
    
}") ?>