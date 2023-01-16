<?php if(isset($dados)): ?>
<div class='row-fluid'>
    <table class="table table-striped table-bordered" style='width:2600px;max-width:none'>
    <thead>
        <tr>            
            <th style="width:13px"></th>
            <th class='input-small'>Código</th>
            <th class='input-small'>Razão Social</th>
            <th class='input-small'>Cadastrado Por</th>
            <th class='input-small'>Operação</th>
            <th class='input-small'>Profissional</th>
            <th class='input-small'>CPF</th>
            <th class='input-small'>Data</th>
            <th class='input-small'>Número Consulta</th>
            <th class='input-small'>Placa</th>
            <th class='input-small'>Carreta</th>
            <th class='input-small'>Origem</th>
            <th class='input-small'>Destino</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($dados as $log): ?>
        <tr>            
            <th><?php echo $html->link('', array('controller' => 'fichas_scorecard', 'action' => 'excluir_log_faturamento', $log[0]['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Log'), 'Confirma exclusão?'); ?></th>
            <td><?php echo $log[0]['codigo']; ?></td>
            <td><?php echo $log[0]['razao_social']; ?></td>
            <td><?php echo $log[0]['usuario']; ?></td>
            <td><?php echo $log[0]['tipo_operacao']; ?></td>
            <td><?php echo $log[0]['profissional']; ?></td>
            <td><?php echo $log[0]['cpf']; ?></td>
            <td><?php echo $log[0]['data_inclusao']; ?></td>
            <td><?php echo $log[0]['num_consulta']; ?></td>
            <td><?php echo $log[0]['placa']; ?></td>
            <td><?php echo $log[0]['carreta']; ?></td>
            <td><?php echo $log[0]['endereco_origem']; ?></td>
            <td><?php echo $log[0]['endereco_destino']; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
</div>
<?php endif; ?>