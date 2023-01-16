<table class="table table-striped">
    <thead>
        <tr>
            <th class="tabela_operacao_operacoes">Operação</th>
            <th></th>
        </tr>
    </thead>
    <?php foreach ($this->data as $operacao): ?>
        <tr>
            <td><?php echo $operacao['Operacao']['descricao'] ?></td>
            <td style="width:20px">
                <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir-cliente-operacao', 'title' => 'excluir', 'onclick' => "javascript:excluir_cliente_operacao({$operacao['ClienteOperacao']['codigo']}, {$operacao['ClienteOperacao']['codigo_cliente']})")) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>