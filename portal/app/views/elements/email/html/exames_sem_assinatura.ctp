<h3>Exames necessários no PCMSO, sem assinatura:</h3>
<ul>
	<?php foreach($exames_sem_assinatura as $key => $exame) : ?>
		<li>
			<span style="font-size: 15px;"><b><?php echo $exame['Servico']['descricao']; ?></b></span>
			<span style="font-size: 13px;">(necessário para unidade: <?php echo $exame['Cliente']['razao_social']; ?>)</span>
		</li>
	<?php endforeach; ?>
</ul>