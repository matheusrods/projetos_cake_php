<div class='form-procurar'> 
    <div class='well'>
        <?php echo $this->BForm->create('ClienteProdutoContrato', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes_produtos_contratos', 'action' => 'listagem_contratos_por_codigo'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php if (isset($cliente)): ?>
    <div class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Status</th>
                <th>Data Vencimento</th>
                <th>N° Contrato</th>
                <th class="acoes">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes_produtos as $produto): ?>
                <tr>
                    <td><?php echo $produto['Produto']['descricao'] ?></td>
                    <td><?php echo $produto['MotivoBloqueio']['descricao']?></td>
                    <td><?php echo $produto['ClienteProdutoContrato']['data_vigencia'] ? preg_replace('/\s+.*$/', '', $produto['ClienteProdutoContrato']['data_vigencia']) : '&nbsp;'; ?></td>
                    <td><?php echo $produto['ClienteProdutoContrato']['numero'] ? $produto['ClienteProdutoContrato']['numero'] : '&nbsp;' ?></td>
                    <td width="10"><?php echo $this->Html->link('', array('controller' => 'clientes_produtos_contratos', 'action' => 'atualizar', $produto['ClienteProduto']['codigo']), array('escape' => false, 'class' => 'icon-edit', 'title' => 'Editar contratos')); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php 
    echo $this->Javascript->codeBlock(" 
        setup_mascaras();
    ");
?>