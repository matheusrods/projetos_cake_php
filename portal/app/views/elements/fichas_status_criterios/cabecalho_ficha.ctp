<div class="row-fluid">
	<div class="span4">
		<h4>Profissional</h4>
		<strong>Nome do Profissional: </strong><?= $profissional['Profissional']['nome'] ?><br />
		<strong>CPF: </strong><?= $buonny->documento($profissional['Profissional']['codigo_documento']) ?><br />
		<strong>RG: </strong><?= $profissional['Profissional']['rg'] ?>
	</div>
	<div class="span4">
		<h4>Cliente</h4>
		<strong>Código do Cliente: </strong><?= $codigo_cliente ?><br />
		<strong>CNPJ/CPF: </strong><?=$buonny->documento($cliente['Cliente']['codigo_documento']) ?><br />
		<strong>Razão Social: </strong><?= $cliente['Cliente']['razao_social'] ?><br />
		<strong>Nome Fantasia: </strong><?= $cliente['Cliente']['nome_fantasia'] ?>
	</div>	
	<div class="span4">
		<h4>Resumo</h4>
		<?php if(!empty($readonly)): ?>
			<p><?php echo empty($resumo_ficha) ? '-' : $resumo_ficha; ?></p>
		<?php else: ?>
			<?php echo $this->Form->input('FichaStatusCriterio.resumo', array('class'=>'span12', 'rows'=>'4', 'value'=>$resumo_ficha, 'label'=>false));?>
		<?php endif; ?>
	</div>
	<br />
	<div>
		<h4>Embarcador / Transportador</h4>
		<strong>Embarcador: </strong>
		<?=( empty($embarcador) ? $cliente['Cliente']['razao_social'] : $embarcador )?>
	    <br />
		<strong>Transportador: </strong>
		<?=( empty($transportador) ? $cliente['Cliente']['razao_social'] : $transportador )?>
	</div>
	<hr />
	<div class="row-fluid inline">
		<div class="span2">
			<strong>Última Pesquisa: </strong><?= $ultima_consulta ?>
		</div>	
		<div class="span2">
			<strong>Pesquisa Anterior: </strong><?= $penultima_ficha['FichaScorecard']['data_inclusao'] ?>
		</div>		
		<div class="span2">
			<strong>Data:</strong><br /><?= date('d/m/Y H:i:s'); ?>
		</div>		
		<div class="span2">
			<strong>Data da Validade: </strong><?= $validade_ult_ficha ?>
		</div>	
		<div class="span2">			
			<strong>Data Cadastro Motorista: </strong><?= $profissional['Profissional']['data_inclusao'] ?>
		</div>	
	</div>
	<br />
	<div class="row-fluid inline">
		<div class="span4">
			<strong>Status anterior do Profissional: </strong><br />
			<?=$status_anterior_profissional?>
		</div>			
		<div class="span4">
			<strong>Status anterior do Proprietario: </strong><br />
			<?=$status_anterior_profissional_proprietario ?>
		</div>		
	</div>

</div>
<hr />