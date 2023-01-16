    <table class='table table-striped'>
	    <thead>
	        <tr>
	            <th>Placa/Chassi</th>
	            <th>Tecnologia</th>
	            <th>Terminal</th>
	            <th>Tipo</th>
	            <th>Última Posição</th>
	            <th>Data Computador Bordo</th>
	            <th>SM</th>
	            <th>Embarcadora</th>
	            <th>Transportadora</th>
	            <th>Status</th>
	            <th>Motorista</th>
	            <th>Alvo</th>
	            <th>Data Entrada Alvo</th>
	            <th>Permanência</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach($veiculos as $veiculo):
                    $veiculo = $veiculo[0];
                    $now = new DateTime();
		            $ref = new DateTime($veiculo['vlev_data']);
                    $permanencia = $now->diff($ref);

        	        $inicioReal = AppModel::dbDateToDate(empty($veiculo['viag_data_inicio']) ? (empty($veiculo['viag_previsao_inicio']) ? date('Y-m-d H:i:s') : $veiculo['viag_previsao_inicio']) : $veiculo['viag_data_inicio']);
			        $fimReal = AppModel::dbDateToDate(empty($veiculo['viag_data_fim']) ? date('Y-m-d H:i:s') : $veiculo['viag_data_fim']);
	            ?>
	        <tr>
	            <td><?php echo isset($veiculo['veic_placa'][0]) && ctype_alpha($veiculo['veic_placa'][0])
	                ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $veiculo['veic_placa']), $inicioReal, $fimReal)
	                : $veiculo['veic_chassi'];
	             ?></td>
	            <td><?php echo $veiculo['tecn_descricao']; ?></td>
	            <td><?php echo $veiculo['term_numero_terminal']; ?></td>
	            <td><?php echo $veiculo['tvei_descricao']; ?></td>
	            <td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $veiculo['upos_descricao_sistema']), $veiculo['upos_latitude'], $veiculo['upos_longitude']) ?></td>
	            <td><?php echo AppModel::dbDateToDate($veiculo['upos_data_comp_bordo']); ?></td>
	            <td><?php echo $this->Buonny->codigo_sm($veiculo['viag_codigo_sm']); ?></td>
	            <td><?php echo $veiculo['embarcador']; ?></td>
	            <td><?php echo $veiculo['transportador']; ?></td>
	            <td><?php echo $veiculo['status']; ?></td>
	            <td><?php echo $veiculo['pess_nome']; ?></td>
	            <td><?php echo $this->Buonny->posicao_geografica($veiculo['refe_descricao'], $veiculo['refe_latitude'], $veiculo['refe_longitude']) ?></td>
	            <td><?php echo AppModel::dbDateToDate($veiculo['vlev_data']); ?></td>
	            <td><?php if (!empty($veiculo['vlev_data'])) printf('%d dias, %d horas, %d minutos', $permanencia->d, $permanencia->h, $permanencia->i); ?></td>
	        </tr>
        <?php endforeach; ?>  
    </tbody>
    <tfoot>
    	<tr>
        	<td colspan="2" class='numeric'><strong>Total de veículos: <?php echo count(array_flip(Set::extract('/0/veic_placa', $veiculos))); ?></strong></td>
            <td colspan="12"></td>
		</tr>
   	</tfoot>
</table>