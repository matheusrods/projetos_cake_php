<div class='well'>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input("TUsuaUsuario.usua_login", array('class' => 'input-large', 'label' => 'Operador', 'readonly' => TRUE )) ?>
		<div class="control-group input text">
			<label>&nbsp;</label>
			<?php if($this->data[0]['logado']): ?>
				<span class="badge-empty badge badge-success" title="Operador Logado"></span>
			<?php else: ?>
				<span class="badge-empty badge badge-important" title="Operador Offline"></span>
			<?php endif; ?>
		</div>	
	</div>
</div>
<div><?php echo $this->Html->link('Voltar', array('controller' => 'Operadores','action' => 'viagens_operadores',rand()), array('title' => 'Voltar' ,'class' => 'btn')); ?></div>
	<div class='row-fluid' style='overflow-x:auto'>
        <table class='table table-striped horizontal-scroll' style='width:3000px;max-width:none;'>
		    <thead>
		        <tr>
                    <th class='input-small'  title="Código SM">SM</th>
                    <th class='input-medium' title="Placa">Placa</th>
                    <th class='input-medium' title="Início">Início</th>
                    <th class='input-medium' title="Fim">Fim</th>
                    <th class='input-xxlarge' title="Transportador">Transportador</th>
                    <th class='input-xxlarge' title="Embarcador">Embarcador</th>
                    <th class='input-xxlarge' title="Gerenciadora">Gerenciadora</th>
                    <th class='input-large' title="Estação">Estação</th>
                    <th class='input-large' title="Tecnologia">Tecnologia</th>
                    <th class='input-large' title="Número Terminal">Número Terminal</th>
                    <th class='input-medium' title="Previsão de Inicio">Previsão de Inicio</th>
                    <th class='input-medium' title="Previsão de Fim">Previsão de Fim</th>
                    <th class='input-xlarge' title="Cidade Origem">Cidade Origem</th>
                    <th class='input-small' title="Estado Origem">Estado Origem</th>
                    <th class='input-xlarge' title="Cidade Destino">Cidade Destino</th>
                    <th class='input-small' title="Estado Destino">Estado Destino</th>
                    <th class='input-xlarge' title="Nome">Nome Motorista</th>
                    <th class='input-medium' title="CPF">CPF Motorista</th>
                    <th style="text-align: right;" class='input-small' title="Valor SM">Valor SM</th>
		        </tr>
		    </thead>
		    <tbody>
		<?php foreach($listagem as $registro): ?>
			<?php $registro   = $registro[0]; ?>
			<?php $inicioReal = AppModel::dbDateToDate($registro['InicioPrevisto']); ?>
			<?php $fimReal    = empty($registro['FimReal']) ? date('d/m/Y H:i:s') : AppModel::dbDateToDate($registro['FimReal']); ?>
			<tr>
				<td><?php echo $this->Buonny->codigo_sm($registro['SM']); ?></td>
				<td><?php echo isset($registro['Placa'][0]) && ctype_alpha($registro['Placa'][0]) ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $registro['Placa']), $inicioReal, $fimReal) : $registro['Chassi'];?></td>
				<td><?php echo $inicioReal ?></td>
				<td><?php echo isset($registro['FimReal']) ? AppModel::dbDateToDate($registro['FimReal']) : NULL; ?></td>
				<td><?php echo $this->Buonny->truncate(iconv('ISO-8859-1', 'UTF-8', $registro['Transportadora']), 30); ?></td>
				<td><?php echo $this->Buonny->truncate(iconv('ISO-8859-1', 'UTF-8', $registro['Embarcador']), 30); ?></td>
				<td><?php echo isset($registro['Gerenciadora']) ? $this->Buonny->truncate(iconv('ISO-8859-1', 'UTF-8', $registro['Gerenciadora']), 30) : 'Não Possui Gerenciadora'; ?></td>
				<td><?php echo $registro['estacao'] ?></td>
				<td><?php echo $registro['Tecnologia'] ?></td>
				<td><?php echo $registro['numero_terminal'] ?></td>
				<td><?php echo AppModel::dbDateToDate($registro['InicioPrevisto']); ?></td>		            
				<td><?php echo AppModel::dbDateToDate($registro['FimPrevisto']); ?></td>
				<td><?php echo $registro['cidade_origem'] ?></td>
				<td><?php echo $registro['estado_origem'] ?></td>
				<td><?php echo $registro['cidade_destino'] ?></td>
				<td><?php echo $registro['estado_destino'] ?></td>
				<td><?php echo $registro['pess_nome'] ?></td>
				<td><?php echo comum::formatarDocumento($registro['pfis_cpf']);?></td>
				<td style="text-align: right;" ><?= $this->Buonny->moeda( $registro['valor_carga'] ); ?></td>					
			</tr>
		<?php endforeach; ?>        
		</tbody>		
		<tfoot>
			<tr>
				<td id="boxTotReg" class='numeric' colspan="3">
					<span class="totRegTxtBasico">Total de registro(s) ( </span>
					<?php echo count($listagem) ?>
					<span class="totRegTxtBasico">) retornado(s)</span>
				</td>
				<td colspan="19"></td>
			</tr>
		</tfoot>
	</table>	
</div>
<div><?php echo $this->Html->link('Voltar', array('controller' => 'Operadores','action' => 'viagens_operadores',rand()), array('title' => 'Voltar' ,'class' => 'btn')); ?></div>
<?php echo $this->Buonny->link_css('tablesorter') ?>
<?php echo $this->Buonny->link_js('jquery.tablesorter.min') ?>
<?php echo $this->Javascript->codeBlock("jQuery('table.tablesorter').tablesorter()") ?>