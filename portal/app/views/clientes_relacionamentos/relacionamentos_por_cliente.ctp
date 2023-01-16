<table class="table table-striped">
    <thead>
        <tr>
            <th>Código Cliente</th>
            <th>Cliente</th>
            <th>Tipo Relacionamento</th>
            <th></th>
        </tr>
    </thead>
    <?php foreach ($this->data as $relacionamento): ?>
        <?php $cr = $relacionamento['ClienteRelacionamento'] ?>
        <tr>
            <td><?php echo $cr['codigo_cliente_relacao']; ?></td>
            <td><?php echo $relacionamento['Cliente']['razao_social'] ?></td>
            <td><?php echo $relacionamento['TipoRelacionamento']['descricao'] ?></td>
            <td>
                <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'excluir', 'onclick' => "return excluir_cliente_relacionamento({$cr['codigo']}, {$this->passedArgs[0]})")) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>