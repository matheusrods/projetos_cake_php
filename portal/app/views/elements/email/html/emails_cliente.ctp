<title><?= $subject; ?></title>

<?php
if($tipoEmail == 'alteracao_cliente'){
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
        <td>Código:</td>
        <td><?= $log['ClienteLog']['codigo_cliente'] ?></td>
    </tr>
    <tr>
        <td>Razão Social:</td>
        <td><?= $log['ClienteLog']['razao_social'] ?></td>
    </tr>
    <tr>
        <td>CNPJ:</td>
        <td><?= $buonny->documento($log['ClienteLog']['codigo_documento']) ?></td>
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
if($tipoEmail == 'alteracao_cliente'){
    echo "<strong>Alterado por: </strong>{$log['UsuarioAlteracao']['apelido']}";
}
?>