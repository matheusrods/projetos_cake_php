<div class='form-procurar'>	
    <div class='well'>
    	<h5><?= $this->Html->link('Definir Filtros', 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>	
		<div id="filtros">
			<?php echo $this->Bajax->form('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'acompanhamento_temperatura', 'model' => 'Recebsm'), 'divupdate' => '.form-procurar')) ?>
			<div class="row-fluid inline">				
				<?php echo $this->Buonny->input_cliente_tipo($this, 0, $clientes_tipos) ?>		        
			</div>			
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('quantidade', array('class' => 'input-small numeric', 'label' => 'Qtd.Placas', 'value'=> 10)); ?>                
                <?php echo $this->BForm->input('intervalo', array('class' => 'input-medium numeric', 'label' => 'Segundos para Atualização', 'value'=> 30)); ?>
			</div>		
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php //echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
			<?php echo $this->BForm->end();?>
		</div>
	</div>
</div>

<div class='lista' style="min-height:150px;">

	<div class='well' id="info-cliente" style="display:none;">
    	<strong>Código:  </strong><span id="cliente-codigo"></span>
    	<strong>Cliente: </strong><span id="cliente-razao-social"></span>
    	<strong>Viagens: </strong><span id="qtd-viagens"></span>
    	<strong>Posicionamento normal:  </strong><span id="posicionamento-normal" class="text-success"></span>
    	<strong>Sem posicionamento:  </strong><span id="sem-posicionamento" class="text-error"></span>
	</div>
	<div id="nagevacao" style="display:none; height:20px;">
	    <p id="info-pagina" style="float:left;"><strong>Página:</strong> <span id="info-pagina-dtr"></span></p>
	    <div id="setas" style="float:right;">	        
	        <?= $this->Html->link('<i class="cus-resultset-previous"></i>', 'javascript:acompanhamentoTemperaturaPrev()', array('escape' => false)) ?>
			<?= $this->Html->link('<i class="cus-resultset-next"></i>', 'javascript:acompanhamentoTemperaturaNext()', array('escape' => false)) ?>
		</div>
	</div>

	<table class="table table-striped tablesorter" id="info-temperatura" style="clear:both; visibility:hidden;">
        
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
                
        </tbody>        

    </table>
</div>
<?php $this->addScript( $this->Buonny->link_js('estatisticas') ) ?>
<?php $this->addScript( $this->Buonny->link_js('solicitacoes_monitoramento') ) ?>
<?php	
	echo $this->Javascript->codeBlock("

		jQuery(document).ready(function(){

			

			$('.btn').click(function(event){
                event.preventDefault();
                //if( $('#RecebsmCodigoCliente').val() != '' ){
	                carregaDadosAcompanhamentoTemperatura(
	                	$('#RecebsmQuantidade').val(), 
	                	$('#RecebsmIntervalo').val(),                 	
	                	$('#RecebsmCodigoCliente').val(),
	                	$('#RecebsmClienteTipo').val()
	                );
				//}
                jQuery('div#filtros').slideToggle('slow');
            })

			$('#filtros').click(function(event){
				jQuery('div#filtros').slideToggle('slow');
			})

		});
	");
?>