<title><?= $subject; ?></title>

<?php
if($tipoEmail == 'alteracao_cliente_produto'){
    echo '<ul style="font-weight: bold; list-style: none outside none; margin: 0; padding: 0;">';
    foreach ($mensagens as $mensagem) {
        echo '<li>' . $mensagem . '</li>';
    }
    echo '</ul>';
    echo '<br />';
}
?>
<table>
    <tr>
        <td>Produto:</td>
        <td><?= $log['Produto']['descricao'] ?></td>
    </tr>
    <tr>
        <td>Código Cliente:</td>
        <td><?= $log['Cliente']['codigo'] ?></td>
    </tr>
    <tr>
        <td>Razão Social:</td>
        <td><?= $log['Cliente']['razao_social'] ?></td>
    </tr>
    <tr>
        <td>CNPJ:</td>
        <td><?= $buonny->documento($log['Cliente']['codigo_documento']) ?></td>
    </tr>
    <tr>
        <td>Endereço Comercial:</td>
        <td><?= $arr_endereco_comercial['logradouro'] ?></td>
    </tr>
    <tr>
        <td></td>
        <td><?= $arr_endereco_comercial['bairro'] . ' - ' . $arr_endereco_comercial['cidade'] . ' - ' . $arr_endereco_comercial['estado']; ?></td>
    </tr>
    <tr>
        <td></td>
        <td><?= $arr_endereco_comercial['cep'] ?></td>
    </tr>
</table>
<br />
<?php
if($tipoEmail == 'alteracao_cliente_produto'){
    echo "<strong>Alterado por: </strong>{$log['UsuarioAlteracao']['apelido']}";
}
?>