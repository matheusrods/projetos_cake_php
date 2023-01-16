<table width="100%" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;"><strong>Usuário:</strong></td>
        <td style="font-size:12px;"><?php echo $nome_usuario; ?></td>
    </tr>
    <tr>
        <td style="font-size:12px;"><strong>Senha:</strong></td>
        <td style="font-size:12px;">
            <?php
            foreach ($mensagens as $mensagem) {
                echo $mensagem;
            }
            ?>
        </td>
    </tr>
</table>