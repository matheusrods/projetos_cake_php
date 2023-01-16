<?php //debug($clientes)?>

<?php if (!empty($clientes)):?>

    <table class="table table-striped">
        <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th >CNPJ</th>
            <th>Nome Fantasia</th>
            <th style="text-align: center" >Unidades</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($clientes as $cliente):?>
            <tr>
                <td class="input-mini"><?php echo $cliente['GrupoEconomicoCliente']['matriz'] ?></td>
                <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
                <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
                <td><?php echo $cliente['Cliente']['nome_fantasia'] ?></td>
                <td style="text-align: center">
                    <?php echo $this->Html->link('', array('action' => 'matriz_responsabilidade_unidades', $cliente['GrupoEconomicoCliente']['matriz'], $cliente['GrupoEconomicoCliente']['codigo_grupo_economico']), array('class' => 'icon-eye-open ', 'title' => 'Visualizar unidades')); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado! Verificar se a configuração da assinatura está ativa.</div>
<?php endif;?>

<?php

echo $this->Javascript->codeBlock(" ");
?>
