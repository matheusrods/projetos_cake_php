<div class='well' id="info-cliente">
	<strong>Código:  </strong><?php echo $cliente['Cliente']['codigo'] ?>
	<strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social'] ?>
	<strong>Viagens: </strong><?php echo $dados[0]['qtd_viagens'] ?>
	<strong>Posicionamento normal:  </strong><span id="posicionamento-normal" class="text-success"><?php echo $dados[0]['qtd_normal'] ?></span>
	<strong>Sem posicionamento:  </strong><span id="sem-posicionamento" class="text-error"><?php echo ($dados[0]['qtd_viagens']-$dados[0]['qtd_normal']) ?></span>
</div>
<div id="nagevacao" style="height:20px;">
    <p id="info-pagina" style="float:left;"><strong>Página:</strong> <span id="info-pagina-dtr"></span></p>
    <div id="setas" style="float:right;">	        
        <?= $this->Html->link('<i class="cus-resultset-previous"></i>', 'javascript:acompanhamentoTemperaturaPrev()', array('escape' => false)) ?>
		<?= $this->Html->link('<i class="cus-resultset-next"></i>', 'javascript:acompanhamentoTemperaturaNext()', array('escape' => false)) ?>
	</div>
</div>

<table class="table table-striped tablesorter" id="info-temperatura" style="clear:both;">    
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th>SM</th>
            <th>Placa</th>
            <th class="numeric">Última Temperatura</th>
            <th>Data Temperatura</th>
            <th>Posição</th>
            <th>Índice</th>
            <th>St. Veículo</th>
        </tr>
    </thead>

    <tbody id="dados-temperatura">
        <?php for ($i = 1; isset($dados[$i]);$i++): ?>
        	<tr>
        		<td></td>
        	</tr>
        <?php endfor; ?>    
    </tbody>        

</table>
<?php $this->addScript( $this->Buonny->link_js('estatisticas') ) ?>
<?php $this->addScript( $this->Buonny->link_js('solicitacoes_monitoramento') ) ?>