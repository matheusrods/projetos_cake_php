<?php //debug($clientes)?>

<?php if (!empty($clientes)):?>

    <table class="table table-striped">
        <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th>Nome Fantasia</th>
            <th >Documento</th>
            <th style="text-align: center" >Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($clientes as $cliente_array) :
            $cliente = $cliente_array['Cliente'];
            ?>
            <tr>
                <td class="input-mini"><?php echo $cliente['codigo'] ?></td>
                <td><?php echo $cliente['razao_social'] ?></td>
                <td><?php echo $cliente['nome_fantasia'] ?></td>
                <td><?php echo $buonny->documento($cliente['codigo_documento']) ?></td>

                <td style="text-align: center">
                    <?= $html->link('', array('controller' => 'pos_categorias', 'action' => 'gerenciar', $cliente['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar categorias')); ?>
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
