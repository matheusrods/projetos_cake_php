
<table class="table table-striped">
    <thead>
        <tr>
            <th>Data Inclusão</th>
            <th>Usuário Adicionou</th>
            <th>Data Alterou</th>
            <th>Usuário Alterou</th>
            <th>Cliente</th>
            <th>Placa</th>
            <th>Tipo Frota</th>
            <th>Ação</th>            
        </tr>
    </thead>
    <tbody>
      <?php if(!empty($log)): ?>
        <?php foreach($log as $logs): ?>
            <tr>
                <td><?php  echo $logs['ClienteVeiculoLog']['data_inclusao']?></td>
                <td><?php  echo $logs['UsuarioInclusao']['apelido'];?></td>
                <td><?php  echo $logs['ClienteVeiculoLog']['data_alteracao'];?></td>
                <td><?php  echo $logs['UsuarioAlteracao']['apelido'];?></td>
                <td><?php  echo $logs['ClienteVeiculoLog']['codigo_cliente'];?></td>
                <td><?php  echo $veic_placa;?></td>
                <td>
                	<?php if($logs['ClienteVeiculoLog']['codigo_tipo_frota']==1) echo 'Frota'; ?>
                	<?php if($logs['ClienteVeiculoLog']['codigo_tipo_frota']==2) echo 'Terceiro';?>
                </td>
				<td>
				 	<?php if($logs['ClienteVeiculoLog']['acao_sistema']==1) echo 'Atualização';?>
				 	<?php if($logs['ClienteVeiculoLog']['acao_sistema']==0) echo 'Inclusão';?>
			   </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
    <tr><td colspan='13'>Não há registro(s) para exibição</td></tr>
    <?php endif; ?>
    </tbody>
</table>