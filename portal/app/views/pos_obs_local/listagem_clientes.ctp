<?php if (!empty($clientes)):?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th >CNPJ</th>
            <th>Nome Fantasia</th>
            <th style="text-align: center" >Locais</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($clientes as $cliente):?>
            <tr>
                <td class="input-mini"><?php echo $cliente['Cliente']['codigo'] ?></td>
                <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
                <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
                <td><?php echo $cliente['Cliente']['nome_fantasia'] ?></td>
                <td style="text-align: center">
                    <?php echo $this->Html->link('', array('controller' => 'pos_obs_local', 'action' => 'index_locais', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Visualizar locais')); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

