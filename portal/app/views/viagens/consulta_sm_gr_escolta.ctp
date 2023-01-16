	    	<?php foreach ($escoltas as $escolta): ?>
	    		<table class="table" >
	    			<thead>
	    				<tr>
	    					<th colspan="8">Empresa: <?php echo $escolta['TPjurPessoaJuridica']['pjur_razao_social'] ?></th>
	    				</tr>
	    				<tr>
	    					<th class="input-xlarge">Equipe</th>
	    					<th class="input-medium">Telefone</th>
	    					<th class="input-medium">Tecnologia</th>
	    					<th class="input-large">Versão Tecnologia</th>
	    					<th class="input-medium">Número Terminal</th>
	    					<th>Placa</th>
	    					<th>Armada</th>
	    					<th>Velada</th>
	    				</tr>
	    			</thead>
	    			<tbody>
	    				<?php foreach ($escolta['Equipes'] as $equipe): ?>
		    				<tr>
		    					<td><?php echo $equipe['TVescViagemEscolta']['vesc_equipe'] ?></td>
		    					<td><?php echo $equipe['TVescViagemEscolta']['vesc_telefone'] ?></td>
		    					<td><?php echo $equipe['TVtecVersaoTecnologia']['vtec_descricao']; ?></td>
								<td><?php echo $equipe['TVtecVersaoTecnologia']['vtec_versao']; ?></td>
								<td><?php echo $equipe['TVescViagemEscolta']['vesc_numero_terminal']; ?></td>
		    					<td><?php echo strtoupper($equipe['TVescViagemEscolta']['vesc_placa']) ?></td>
		    					<td><?php echo ($equipe['TVescViagemEscolta']['vesc_armada']) ? 'Sim' : 'Não' ?></td>
		    					<td><?php echo ($equipe['TVescViagemEscolta']['vesc_velada']) ? 'Sim' : 'Não' ?></td>
		    				</tr>
	    				<?php endforeach; ?>
	    			</tbody>

	    		</table>
	    	<?php endforeach; ?>