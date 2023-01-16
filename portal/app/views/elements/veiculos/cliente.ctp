<?php if($cliente): ?>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?> &nbsp; &nbsp;
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?> &nbsp; &nbsp;
		<strong>Placa: </strong><?= $veic_placa ?> &nbsp; &nbsp;
		<strong>Qtd. dias: </strong><?= $this->data['TVeicVeiculo']['racs_validade_checklist'] ?> &nbsp; &nbsp;

		 
		 
	</div>
	<div id="checklist" class='well'>
		<strong>Último Checklist:  </strong> <?= !empty($data_checklist)?$data_checklist:' ' ?> 
		<strong>Dias do Último Checklist:  </strong> <?= ($dias_checklist == 0 || !empty($dias_checklist))?$dias_checklist:'' ?>  
		<? 	if (!empty($posicao_checklist)) {
		 		echo "<strong>Status: </strong>&nbsp;".$posicao_checklist;
		 	}
		?>

	</div>
<?php endif; ?>
