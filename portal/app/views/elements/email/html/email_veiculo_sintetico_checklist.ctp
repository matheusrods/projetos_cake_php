<?php $this->data = $dados;?>

<style type="text/css">
	.input-mini {
	  width: 60px;
	}

	.input-small {
	  width: 90px;
	}

	.input-medium {
	  width: 150px;
	}

	.input-large {
	  width: 210px;
	}

	.input-xlarge {
	  width: 270px;
	}

	.input-xxlarge {
	  width: 530px;
	}
	
	.table-striped tbody > tr:nth-child(odd) > td,
	.table-striped tbody > tr:nth-child(odd) > th {
  		background-color: #f9f9f9;
	}
	.table-striped tbody tr:nth-child(2n+1) td, .table-striped tbody tr:nth-child(2n+1) th {
    	background-color: #EEEEEE;
	}
	
	.table-striped tbody > tr:nth-child(2n+1) > td, .table-striped tbody > tr:nth-child(2n+1) > th {
		background-color: #F9F9F9;
	}
	
	.table tbody tr.info > td {
  		background-color: #d9edf7;
	}
	
	.table th, .table td {
		border-top: 1px solid #DDDDDD;
		line-height: 20px;
		padding: 8px;
		text-align: left;
		vertical-align: top;
	}
	
	table {
	  max-width: 100%;
	  background-color: transparent;
	  border-collapse: collapse;
	  border-spacing: 0;
	}

	.table {
	  width: 100%;
	  margin-bottom: 20px;
	}

	.table th,
	.table td {
	  padding: 8px;
	  line-height: 20px;
	  text-align: left;
	  vertical-align: top;
	  border-top: 1px solid #dddddd;
	}

	.table th {
	  font-weight: bold;
	}

	.table thead th {
	  vertical-align: bottom;
	  background-color:#daeaf7;
	}

	.row-fluid {
    	width: 130%;
	}
</style>
<div class="container" style="clear:both;padding-top:50px;padding-left:50px;width:98.4%;min-height:300px;">
	<div class="page-title">
		<h2>Veículo Sintético Checklist</h2> 
		<table>
				<tr >
					<td><strong>Cliente:</strong></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->data['Cliente']['razao_social'] ?></td> 
				</tr>
				<tr>
					<td><strong>Tecnologia:</strong></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->data['TTecnTecnologia']['tecn_descricao'] ?> </td> 
				</tr>
				<tr>	
					<td><strong>Nº Terminal:</strong></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->data['TTermTerminal']['term_numero_terminal']?></td> 
				</tr>
				<tr>	
					<td><strong>Placa:</strong></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo $this->data['TVeicVeiculo']['veic_placa'];?></td>
				</tr>
				<tr>	
					<td><strong>Posicionamento:</strong></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo($posicionando) ? 'Veículo posicionando':'Sem posicionamento'?></td>
				</tr>	
				<tr>	
					<td><strong>Ultimo Checklist:</strong></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->data['TCveiUltimoChecklist']['cvei_data_cadastro']; ?> </td>
					
				</tr>
				<tr>	
					<td><strong>Vencimento do Checklist:</strong></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo $this->data['TCveiUltimoChecklist']['cvei_data_vencimento']?></td>
				</tr>
				<tr>
					<td><strong>Status:</strong></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 	
						<?php if($vencimento == 1){ 
							echo "Válido / ";
						}elseif($vencimento == 2){
						  echo "Inválido / ";
						}else{
							echo "Sem Checklist / ";
						}
						if($estatus == 1){ 
							echo "Aprovado";
						}elseif($estatus == 2){
						  echo " Reprovado";
						}else{
								echo "Sem Checklist";
							}	

						?>							
					</td>
				</tr>	
				<tr>
					<td><strong>Operador:</strong></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->data['TCveiUltimoChecklist']['cvei_usuario_adicionou']; ?> </td>
				</tr>	
		</table>
	</div>	

<div>		

<br/><br/><br/>

	<div style="maxheight:180px">
		<table class='table table-striped' id="problemas">
			<thead>
				<th class='input-small'>SM</th>
				<th class='input-small'>Nº Pedido</th>
				<th class='input-medium'>Previsão Inicio</th>
				<th class='input-xlarge'>Transportador</th>
				<th class='input-xlarge'>Embarcador</th>
				<th class='input-small'>CPF</th>
				<th class='input-large'>Motorista</th>
			</thead>
			<tbody >
				<?php if($viagens): ?>
					<?php foreach ($viagens as $viag): ?>
						<tr>
							<td><?php echo $this->Buonny->codigo_sm($viag['TViagViagem']['viag_codigo_sm']) ?></td>
							<td><?php echo $viag['TViagViagem']['viag_pedido_cliente'] ?></td>
							<td><?php echo $viag['TViagViagem']['viag_previsao_inicio'] ?></td>
							<td><?php echo $viag['Transportador']['pess_nome'] ?></td>
							<td><?php echo $viag['Embarcador']['pess_nome'] ?></td>	
							<td><?php echo $viag['MotoristaCpf']['pfis_cpf'] ?></td>
							<td><?php echo $viag['Motorista']['pess_nome'] ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<div style="maxheight:180px" >
		<table class='table table-striped'>
			<thead>
				<th class="input-large">Periferico</th>
				<th style="padding-left:20px">
				<?php if(false): ?>
					<?php foreach ($status as $esta_codigo => $esta_descricao): ?>
						<input style="margin-left:7px" type="radio" name="option1" value="<?php echo $esta_codigo ?>" />
						<?php echo $esta_descricao ?>
					<?php endforeach; ?>
				<?php endif; ?>
				</th>

				<th class="input-large">Periferico</th>
				<th style="padding-left:20px">
				<?php if(false): ?>
					<?php foreach ($status as $esta_codigo => $esta_descricao): ?>
						<input style="margin-left:7px" type="radio" name="option2" value="<?php echo $esta_codigo ?>" />
						<?php echo $esta_descricao ?>
					<?php endforeach; ?>
				<?php endif; ?>
				</th>
			</thead>
					
			<? if($perifericos): ?>
			<tbody>
				<tr>
				<?php $count = 1 ?>
				<?php foreach ($perifericos as $ppadKey => $ppad):?>
					
					<?php $class = ($ppadKey%2 === 0)?'option1':'option2' ?>
					<td style="color:red">
						<?php echo $ppad['TPpadPerifericoPadrao']['ppad_descricao'] ?>
					</td>
					
					<td>
						<?php if($readonly): ?>
						<?php echo $this->data["TEstaEstatus"][$ppadKey]["esta_descricao"]?>
						<?php else: ?>
							<?php echo $this->data["TIcveItemChecklistVeiculo"][$ppadKey]["icve_esta_codigo"]?>
						<?php endif; ?>
					</td>
							
					<?php if($ppadKey%2 !== 0): ?>
						</tr><tr> 
					<?php endif; ?>
				<?php endforeach;?>
				</tr>
			</tbody>
			<? endif; ?>
		</table>
	</div>
</div>	