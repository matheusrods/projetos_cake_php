<table class="table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Data</th>            
            <th>Usuário</th>
            <th>Status</th>
            <th>Tipo de Evento</th>
            <th>Descrição</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Prestador</th>
        </tr>
    </thead>
<?php foreach ($historicos as $historico): ?>
    <tr style="<?php echo ($historico['HistoricoSmPrestador']['status'] != HistoricoSmPrestador::CANCELADO) ? '' : '  color: darkgray; background-color: #eee;' ?>">
        <td><?php echo $historico['HistoricoSm']['codigo']; ?></td>
        <td><?php echo $historico['HistoricoSm']['data_inclusao']; ?></td>
        <td><?php echo ($historico['Usuario']['apelido']) ? $historico['Usuario']['apelido'] : $historico['TUsuaUsuario']['usua_login']; ?></td>
        <td><?php echo $historico[0]['descricao']; ?></td>
        <td><?php echo $historico['HistoricoSm']['descricao']; ?></td>
        <td><?php echo $historico[0]['texto']; ?></td>
        <td class="numeric"><?php echo $this->Buonny->posicao_geografica($historico[0]['latitude'], $historico[0]['latitude'], $historico[0]['longitude']) ?></td>
        <td class="numeric"><?php echo $this->Buonny->posicao_geografica($historico[0]['longitude'], $historico[0]['latitude'], $historico[0]['longitude']) ?></td>
        <td><?php echo $historico['Prestador']['nome'] ?></td>
    </tr>
<?php endforeach; ?>
</table>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>