<?php if (!empty($dados)): ?>
	<?php echo $this->element('fichas_scorecard/relatorios_gerenciais_scorecard', array('dados' => $dados, 'tipo_busca'=>$tipo_busca)) ?>
<?php endif ?> 