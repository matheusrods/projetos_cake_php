<div class='well'>
	<?php echo $this->BForm->create('TVeicVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'veiculos', 'action' => 'historico_posicoes'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa', 'readonly'=>true)) ?>
			<?php echo $this->Buonny->input_periodo($this) ?>
			<?php echo $this->BForm->input('intervalo', array('label' => false, 'class' => 'input-mini numeric tempo', 'placeholder' => 'Minutos', 'title' => 'Intervalo de tempo entre posições')) ?>
		</div>
		<?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end() ?>
</div>
<?php if(empty($posicoes)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
    <table class='table table-striped'>
	    <thead>
	        <tr>
	            <th>Posição</th>
	            <th>Data Inicial</th>
	            <th>Data Final</th>
	            <th class="numeric">Hodômetro</th>
	            <th class="numeric">Velocidade</th>
	            <th class="numeric">RPM</th>
	            <?php if(isset($alvo_sm) && $alvo_sm): ?>
		           	<th>Alvo</th>
		        <?php endif;?>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach($posicoes as $posicao):  $posicao = current($posicao); ?>
		        <tr>
		            <td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $this->Text->truncate($posicao['descricao'], 70)), $posicao['latitude'], $posicao['longitude']) ?></td>
		            <td><?php echo AppModel::dbDateToDate($posicao['data_inicial']); ?></td>
		            <td><?php echo AppModel::dbDateToDate($posicao['data_final']); ?></td>
		            <td class="numeric"><?= ($posicao['hodometro'] > 0) ? $posicao['hodometro'] : ''; ?></td>
		            <td class="numeric"><?= ($posicao['velocidade'] > 0 ) ? $posicao['velocidade'] : ''; ?></td>
		            <td class="numeric"><?= ($posicao['rpm'] > 0) ? $posicao['rpm'] : ''; ?></td>
		            <?php if(isset($alvo_sm) && $alvo_sm):?>
		            	<?php if(isset($posicao['alvo']['TRefeReferencia'])):?>
								<td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $this->Text->truncate($posicao['alvo']['TRefeReferencia']['refe_descricao'], 70)), $posicao['alvo']['TRefeReferencia']['refe_latitude'], $posicao['alvo']['TRefeReferencia']['refe_longitude']) ?></td>
		            	<?php else:?>
		            		<td></td>
		            	<?php endif;?>	
		            <?endif;?>
		        </tr>
	        <?php endforeach; ?>  
	    </tbody>
	</table>
<?php endif; ?>