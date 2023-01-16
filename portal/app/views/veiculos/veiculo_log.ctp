
<table class="table table-striped">
    <thead>
        <tr>
            <th>Data</th>
            <th>Usuário Adicionou(Código)</th>
            <th>Data Alterou</th>
            <th>Usuário Alterou(Código)</th>
            <th>Cliente</th>
            <th>Veículo</th>
            <th>Tipo Frota</th>
            <th>Ação do Sistema</th>            
        </tr>
    </thead>
    <tbody>
      <?php if(!empty($log)): ?>
        <?php foreach($log as $logs): ?>
            <tr>
                <td><?php  echo $logs['ClienteVeiculoLog']['data_inclusao']?></td>
                <td><?php  echo $logs['ClienteVeiculoLog']['codigo_usuario_inclusao'];?></td>
                <td><?php  echo $logs['ClienteVeiculoLog']['data_alteracao'];?></td>
                <td><?php  echo $logs['ClienteVeiculoLog']['codigo_usuario_alteracao'];?></td>
                <td><?php  echo $logs['ClienteVeiculoLog']['codigo_cliente'];?></td>
                <td><?php  echo $logs['ClienteVeiculoLog']['codigo_veiculo'];?></td>
                <td><?php  echo $logs['ClienteVeiculoLog']['codigo_tipo_frota'];?></td>
				<td><?php  echo $logs['ClienteVeiculoLog']['acao_sistema'];?></td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
    <tr><td colspan='13'>Não há registro(s) para exibição</td></tr>
    <?php endif; ?>
    </tbody>
</table>