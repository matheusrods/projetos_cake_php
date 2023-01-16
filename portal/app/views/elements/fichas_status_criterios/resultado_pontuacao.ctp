<?php $codigo_status = !empty($dados_ficha['FichaScorecard']['codigo_status']) ? $dados_ficha['FichaScorecard']['codigo_status'] : (!empty($this->data['FichaScorecard']['codigo_status']) ? $this->data['FichaScorecard']['codigo_status'] : NULL);?>
<?php if( $codigo_status ) : ?>
	<?php if( !in_array($codigo_status, array(FichaScorecardStatus::A_PESQUISAR, FichaScorecardStatus::EM_PESQUISA) ) ) : ?>
	<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>
	<div>
		<legend>Resultados Score</legend>
		<strong>Total de pontos: </strong><?php echo $pontuacao['total_pontos'] ?><br/>
		<strong>Percentual de pontos: </strong><?php echo $pontuacao['percentual_pontos'] ?>%<br/>
		<strong>Classificação do Profissional: </strong>
			<span class="label <?php echo ($pontuacao['valor'] == 0 ? 'label-important' : 'label-success')?>"><?= $pontuacao['nivel'] ?></span>
			<?php 
			//Não mostra Score de alguns profissionais
			$profissional_sem_score = array('Funcionario'=>5,'Ajudante'=>6,'Conferente'=>7);
			if ($pontuacao['nivel']!='Divergente' and $pontuacao['nivel']!='Insuficiente'){ 
				if(!in_array($pontuacao['tipo_profissional'],$profissional_sem_score)) { ?>
					<?php for($i = 1; $i <= 5; $i++): ?>
						<i class="<?php echo $pontuacao['total_pontos'] > 0 && $i <= $pontuacao['pontos'] ? 'score-pneu-dourado' : 'score-pneu-cinza'; ?>"></i>
					<?php endfor; ?>
					(Carga máxima permitida: R$ <?= $this->Buonny->moeda($pontuacao['valor']) ?>)
				<?php } ?>
			<?php } ?>	
			<br/>
	</div>
	<?php endif;?>
<?php endif;?>