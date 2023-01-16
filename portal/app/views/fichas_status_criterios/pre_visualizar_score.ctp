<div>
	<legend>Pré Resultados Score</legend>
	<strong>Total de pontos: </strong><?=($pre_classificacao['classificacao']['ParametroScore']['pontos'] < 0 ? 0 : $pre_classificacao['classificacao']['ParametroScore']['pontos']);?><br/>
	<strong>Percentual de pontos: </strong><?=($pre_classificacao['percentual_total'] < 0 ? 0 : $pre_classificacao['percentual_total']);?>%<br/>
	<strong>Classificação do Profissional: </strong>
	<span class="label <?=($pre_classificacao['classificacao']['ParametroScore']['pontos'] <= 0 ? 'label-important' : 'label-success')?>">
		<?=$pre_classificacao['classificacao']['ParametroScore']['nivel'] ?>
	</span>	
	<br/>
</div>