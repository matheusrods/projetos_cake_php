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
        <?php foreach($clientes as $cliente) :?>
            <tr>
                <td class="input-mini"><?php echo $cliente['codigo'] ?></td>
                <td><?php echo $cliente['razao_social'] ?></td>
                <td><?php echo $cliente['nome_fantasia'] ?></td>
                <td><?php echo $buonny->documento($cliente['codigo_documento']) ?></td>

                <td style="text-align: center">
                    <?php echo $this->Html->link('', array('controller' => 'clientes', 'action' => 'logos_cores', $cliente['codigo']), array('class' => 'icon-cog', 'title' => 'Visualizar'));?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php

echo $this->Javascript->codeBlock(" ");
