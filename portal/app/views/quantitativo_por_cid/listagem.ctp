
<?php if(isset($listagem) && count($listagem)) : ?>

	<div class="row">
		<div class="span12">
			<div id="grafico_pizza" class='gadget'></div>
		</div>
	</div>
	<div class="row">
		<div class="span6" id="div_setor"></div>
		<div class="span6" id="div_cargo"></div>		
	</div>
	<hr />
	
	<?php 
		echo $this->Javascript->codeBlock(
		    $this->Highcharts->render(array(), $series_pizza, array(
				'renderTo' => 'grafico_pizza',
		    	'title' => 'Percentual (por CIDS)',
		        'chart' => array('type' => 'pie'),
			))
		); 
	?>
	
	<div class="right" style="margin-bottom: 10px; text-align: right;">
		<a href="/portal/quantitativo_por_cid/exportar/<?php echo $codigo_grupo_economico; ?>" class="btn btn-success">Exportar em Excel</a>
	</div>
	
    <div class="row-fluid inline">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="input-mini">CID 10</th>
                    <th>Descrição</th>
                    <th class='input-small numeric'>Atestados</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listagem as $key => $linha): ?>
                    <tr>
                        <td class="input-mini"><?= $linha['Cid']['codigo_cid10']; ?></td>
                        <td><?= $linha['Cid']['descricao']; ?></td>
                        <td class="input numeric"><?php echo $linha[0]['qtd']; ?></td>
                    </tr>
                <?php endforeach; ?>        
            </tbody>
        </table>
    </div>	 
	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function() {
    	var div_setor = $("#div_setor");
    	var div_cargo = $("#div_cargo");
    	bloquearDiv(div_setor);
    	bloquearDiv(div_cargo);
		div_setor.load(baseUrl + "quantitativo_por_cid/grafico_setor/" + $("#codigo_grupo_economico").val() + "/" + Math.random());
    	div_cargo.load(baseUrl + "quantitativo_por_cid/grafico_cargo/" + $("#codigo_grupo_economico").val() + "/" + Math.random());
    });
'); ?>
    
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>



    
    