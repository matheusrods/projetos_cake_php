<?php if($cliente): ?>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?> &nbsp; &nbsp;
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?> &nbsp; &nbsp;
		<strong>Placa: </strong><?=$this->Buonny->placa($filtros['placa'], Date('d/m/Y 00:00:00'), Date('d/m/Y 23:59:59')) ?> &nbsp; &nbsp;


		<?php if(isset($dados_veiculo) && !empty($dados_veiculo['TTecnTecnologia']['tecn_descricao'])): ?>
			<strong>Tecnologia: </strong><?= $dados_veiculo['TTecnTecnologia']['tecn_descricao'] ?> &nbsp; &nbsp;
		<?php endif; ?>

		<?php if($posiciona): ?>
			<span class="badge-empty badge badge-success" title="Posicionamento Normal"></span>
		<?php else: ?>
			<span class="badge-empty badge" title="Sem Posicionamento"></span>
		<?php endif; ?> &nbsp; &nbsp;

		<strong>Última Posição: </strong>
		<? if (!empty($veiculo['TUposUltimaPosicao']['upos_descricao_sistema'] )) : ?>
			<?= $veiculo['TUposUltimaPosicao']['upos_descricao_sistema'] ?> - <?=   $veiculo['TUposUltimaPosicao']['upos_data_comp_bordo']   ?>
		<? else : ?>
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		<? endif; ?>&nbsp; &nbsp; 
		 
		 
	</div>
	<div id="checklist" class='well'>
		<strong>Último Checklist:  </strong> <?= !empty($data_checklist)?$data_checklist:' ' ?> 
		<strong>Dias do Último Checklist:  </strong> <?= (isset($dias_checklist) && ($dias_checklist == 0 || !empty($dias_checklist)))?$dias_checklist:'' ?>  
		<? /*if (isset($status)) {
		 		echo "<strong>Status: </strong>";
		 		if ($status=='1')   echo ' &nbsp; APROVADO'; 
		 		if ($status=='0')	echo ' &nbsp; REPROVADO';
		 	}
		 	*/
		 	if (!empty($posicao_checklist)) {
		 		echo "<strong>Status: </strong>&nbsp;".$posicao_checklist;
		 	}
		?>

	</div>
<?php endif; ?>
<?php echo $this->Buonny->link_js('estatisticas') ?>
