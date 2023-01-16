    <table class='table table-striped'>
	    <thead>
	        <tr>
	            <th>Placa/Chassi</th>
	            <th>Transportadora</th>
	            <th>Tecnologia</th>
	            <th>N/S</th>
	            <th>Tipo</th>
	            <th>Última Posição</th>
	            <th>Data Computador Bordo</th>
	            <th>Status</th>
	            <th>Alvo</th>
	            <th>SM</th>
	            <th>Data Entrada Alvo</th>
	            <th>Permanência</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach($posicoes as $posicao):
	        		$posicao = current($posicao); 
                    $now = new DateTime();
                    $ref = new DateTime($posicao['vlev_data']);
                    $permanencia = $now->diff($ref);

        	        $inicioReal = AppModel::dbDateToDate(empty($posicao['viag_data_inicio']) ? (empty($posicao['viag_previsao_inicio']) ? date('Y-m-d H:i:s') : $posicao['viag_previsao_inicio']) : $posicao['viag_data_inicio']);
			        $fimReal = AppModel::dbDateToDate(empty($posicao['viag_data_fim']) ? date('Y-m-d H:i:s') : $posicao['viag_data_fim']);
                    
                    $status = $this->Buonny->status_viagem($posicao);
	            ?>
	        <tr>
	            <td><?php echo isset($posicao['veic_placa'][0]) && ctype_alpha($posicao['veic_placa'][0])
	                ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $posicao['veic_placa']), $inicioReal, $fimReal)
	                : $posicao['veic_chassi'];
	             ?></td>
	            <td><?php echo $posicao['pjur_razao_social']; ?></td>
	            <td><?php echo $posicao['tecn_descricao']; ?></td>
	            <td><?php echo $posicao['term_numero_terminal']; ?></td>
	            <td><?php echo $posicao['tvei_descricao']; ?></td>
	            <td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $posicao['upos_descricao_sistema']), $posicao['upos_latitude'], $posicao['upos_longitude']) ?></td>
	            <td><?php echo AppModel::dbDateToDate($posicao['upos_data_comp_bordo']); ?></td>
	            <td><?php echo $status; ?></td>
	            <td><?php echo $this->Buonny->posicao_geografica($posicao['refe_descricao'], $posicao['refe_latitude'], $posicao['refe_longitude']) ?></td>
	            <td><?php echo $this->Buonny->codigo_sm($posicao['viag_codigo_sm']); ?></td>
	            <td><?php echo AppModel::dbDateToDate($posicao['vlev_data']); ?></td>
	            <td><?php if (!empty($posicao['vlev_data'])) printf('%d dias, %d horas, %d minutos', $permanencia->d, $permanencia->h, $permanencia->i); ?></td>
	        </tr>
        <?php endforeach; ?>  
    </tbody>
    <tfoot>
    	<tr>
        	<td colspan="2" class='numeric'><strong>Total de veículos: <?php echo count(array_flip(Set::extract('/0/veic_placa', $posicoes))); ?></strong></td>
            <td colspan="10"></td>
		</tr>
   	</tfoot>
</table>