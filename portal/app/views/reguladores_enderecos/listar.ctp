<table class="table table-striped">
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
        <td class="numeric"><?php echo $endereco['ReguladorEndereco']['numero'] ?></td>
        <td><?php echo $endereco['ReguladorEndereco']['complemento'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_bairro'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_cidade'] ?></td>
        <td><?php echo $endereco['uvw_endereco']['endereco_estado_abreviacao'] ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'reguladores_enderecos', 'action' => 'atualizar', $endereco['ReguladorEndereco']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
            <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'excluir', 'onclick' => "return exclui_regulador_endereco({$endereco['ReguladorEndereco']['codigo']},{$endereco['ReguladorEndereco']['codigo_regulador']})")) ?>
            <?php echo $this->Form->input('endereco_'.$endereco['ReguladorEndereco']['codigo'], array('type' => 'hidden', 'value' => $endereco['ReguladorEndereco']['codigo'])) ?>
            <div class="clear"></div>
        </td>
    </tr>
<?php endforeach; ?>
</table>
<?php echo $javascript->codeBlock("
function exclui_regulador_endereco(codigo_regulador_endereco, codigo_regulador){
    if (confirm('Deseja realmente excluir ?'))
        jQuery.ajax({
            type: 'POST',
            url: baseUrl + 'reguladores_enderecos/excluir/' + codigo_regulador_endereco + '/' + Math.random(),
            success: function(data) {
                var div = jQuery('#endereco-regulador');
                bloquearDiv(div);
                div.load(baseUrl + 'reguladores_enderecos/listar/' + codigo_regulador + '/' + Math.random() );
            }
        });
    
}") ?>