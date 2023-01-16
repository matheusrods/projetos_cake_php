
<table class="table table-striped" style='width:3000px;max-width:none;'>
    <thead>
        <tr>
            <th>Código Cliente</th>
            <th>Cliente</th>
            <th>Código Credenciado</th>
            <th>Credenciado</th>
            <th>Funcionário</th>
            <th>CPF</th>
            <th>Exame</th>
            <th>Data Realizado</th>
            <th>Data Baixa</th>
            <th>Valor Custo</th>
            <th>Glosado</th>
        </tr>
    </thead>
    <tbody>        
        <?php foreach($dados as $key => $dado) : ?>           
            <tr>
                <td><?php echo $dado[0]['codigo_cliente']; ?></td>
                <td><?php echo $dado[0]['nome_cliente']; ?></td>
                <td><?php echo $dado[0]['codigo_fornecedor']; ?></td>
                <td><?php echo $dado[0]['nome_fornecedor']; ?></td>
                <td><?php echo $dado[0]['nome_funcionario']; ?></td>
                <td><?php echo $buonny->documento($dado[0]['cpf']); ?></td>
                <td><?php echo $dado[0]['exame']; ?></td>
                <td><?php echo AppModel::dbDateToDate($dado[0]['data_realizacao']); ?></td>
                <td><?php echo AppModel::dbDateToDate($dado[0]['data_baixa']); ?></td>
                <td><?php echo $dado[0]['valor_custo']; ?></td>
                <td><?php echo (!empty($dado[0]['codigo_glosa']) ? 'Sim' : 'Não'); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>