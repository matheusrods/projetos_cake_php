<?php if (isset($data)): ?>
    <?php if (isset($data[0][0])): ?>
        <div class='well'>
			<?php if($tipo_empresa == 1): ?>
				<strong>Embarcador: </strong><?php echo $data[0][0]['nome_cliente']; ?>
			<?php else: ?>
				<strong>Transportador: </strong><?php echo $data[0][0]['nome_cliente']; ?>
			<?php endif ?>
				
            <strong>Período de: </strong><?php echo $datas_selecionadas['data_inicial']; ?><strong> até: </strong><?php echo $datas_selecionadas['data_final']; ?><br />
            <strong>Ocorrência: </strong><?php echo $data[0][0]['titulo_ocorrencia']; ?>
        </div>
    <?php endif ?>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>SM</th>
                <th>Placa</th>
                <th>Motorista</th>
                <th>Tecnologia</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $dados_rma): ?>
            <tr>
                <td><?php echo $this->Buonny->codigo_sm($dados_rma[0]['codigo_sm']); ?></td>
                <td><?php echo $dados_rma[0]['placa_veiculo']; ?></td>
                <td><?php echo $dados_rma[0]['nome_motorista']; ?></td>
                <td><?php echo $dados_rma[0]['tecnologia']; ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>