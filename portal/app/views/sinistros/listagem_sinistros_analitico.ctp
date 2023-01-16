<table class='table table-striped'>
    <thead>
        <th>No Sinistro</th>
        <th>SM</th>
        <th>Transportador</th>
        <th>Embarcador</th>
        <th>Tipo Sinistro</th>
        <th>Valor Recuperado</th>
        <th>Data Sinistro</th>
        <th>Motorista</th>
        <th>CPF</th>
        <th>Seguradora</th>
        <th>Corretora</th>          
    </thead>
    <tbody>
        <?php $total = 0 ?>
        <?php if( isset($dados) && !empty($dados) ): ?>
            <?php foreach($dados as $value): ?>
                <?php $total++ ?>
                <tr>
                	<td><?php echo $this->Buonny->codigo_sinistro($value['Sinistro']['codigo']); ?></td>
                    <td><?php echo $this->Buonny->codigo_sm($value['Sinistro']['sm']); ?></td>
                    <td><?php echo $value['Transportador']['razao_social'] ?></td>
                    <td><?php echo $value['Embarcador']['razao_social'] ?></td>
                    
                    <td><?php echo $natureza[$value['Sinistro']['natureza']] ?></td>
                    <td><?php echo $this->Buonny->moeda($value['Sinistro']['valor_recuperado'],array('format'=>'R$'))?></td>
                    <td><?php echo substr($value['Sinistro']['data_evento'],0,10); ?></td>
                    <td><?php echo $value['Profissional']['nome'] ?></td>
                    <td><?php echo $value['Profissional']['codigo_documento'] ?></td>
                    <td><?php echo $value['Seguradora']['nome'] ?></td>
                    <td><?php echo $value['Corretora']['nome'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th><?= $total ?></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>   
        </tr>
    </tfoot>
</table>