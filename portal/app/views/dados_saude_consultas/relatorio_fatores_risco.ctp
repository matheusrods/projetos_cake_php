<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<div class='form-procurar'> 
    <div class='well'>
        <?php echo $this->BForm->create('DashboardRelatorio', array('autocomplete' => 'off', 'url' => array('controller' => 'dados_saude_consultas', 'action' => 'relatorio_fatores_risco'))) ?>
        <div class="row-fluid inline">
            <div class="row-fluid">
                <div class="span2">
                    <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'DashboardRelatorio'); ?>

                </div>

                <div id='tipos' >
			        <?php echo $this->BForm->input('tipo_sistemas', array('type' => 'radio', 'options' => $tipos_sistemas, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
			    </div>
<!--                 <div class="span3">
                    <?php // echo $this->BForm->input('codigo_unidade', array('class' => 'input-xxlarge multiple', 'title' => 'Selecione a(s) Unidade(s)', 'label' => false, 'options' => array(), 'multiple'=>'multiple', 'type'=>'select' )) ?>

                </div>
                <div class="span3">
                    <?php // echo $this->BForm->input('codigo_setor', array('class' => 'input-xxlarge multiple', 'title' => 'Selecione o(s) Setor(es)', 'label' => false, 'options' => array(), 'multiple'=>'multiple', 'type'=>'select' )) ?>

                </div>
                <div class="span3">
                    <?php // echo $this->BForm->input('codigo_cargo', array('class' => 'input-xxlarge multiple', 'title' => 'Selecione o(s) Cargo(s)', 'label' => false, 'options' => array(), 'multiple'=>'multiple', 'type'=>'select' )) ?>
                </div> -->
            </div>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-submit')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php if (empty($series)): ?>
<div class="alert">
	Defina os critérios de filtros.
</div>
<?php else: ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_datepicker();
        setup_mascaras();
        if(!$("#DashboardRelatorioCodigoCliente").is(":visible")){
        	$("#DashboardRelatorioCodigoCliente").parent().hide();
        }
	});', false);
?>
<div id="grafico"></div>
<script>
	var chart;
	$(document).ready(function() {
		
		chart = new Highcharts.Chart({
			colors: ['#90ee7e', '#6AB0F7', '#F47373'],
	        chart: {
	            type: 'column',
	            renderTo: 'grafico'
	        },
	        title: {
	            text: 'Fatores de Risco'
	        },
	        xAxis: {
	            categories: [<?php echo '\''.implode('\', \'',$array_nome_formularios).'\''; ?>],
	            labels:{
	    	        rotation: 0,
		   			align: 'right'
	            }
	        },
	        yAxis: {
	            min: 0,
	            title: {
	                text: 'Quantidade'
	            },
	            stackLabels: {
	                enabled: true,
	                style: {
	                    fontWeight: 'bold',
	                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
	                }
	            }
	        },
	        legend: {
	            align: 'center',
	            x: 30,
	            verticalAlign: 'top',
	            y: 25,
	            floating: true,
	            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
	            borderColor: '#AAA',
	            borderWidth: 3,
	            shadow: false
	        },
	        tooltip: {
	            formatter: function () {
	                return '<b>' + this.x + '</b><br/>' +
	                    this.series.name + ': ' + this.y + '<br/>' +
	                    'Total: ' + this.point.stackTotal;
	            }
	        },
	        plotOptions: {
	            column: {
	                stacking: 'normal',
	                dataLabels: {
	                    enabled: true,
	                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
	                }
	            }
	        },
	        series: [
				<?php
					foreach($series as $key => $linha){
						echo '{
							   name: \''.$linha['name'].'\', 
						       data: ['.implode(',', $linha['values']).']
						}, ';
					}
				?>
			]
	    });
	});
</script>
	<table class="table table-striped table-bordered tablesorter">
		<thead>
			<tr>
				<th></th>
				<?php foreach($series as $k => $campo) : ?>
					<th><?php echo $campo['name']; ?></th>
				<?php endforeach; ?>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($array_nome_formularios as $k_titulo => $titulo): ?>
			<?php $soma = 0; ?>
			<tr>
				<td><?php echo $titulo; ?></td>
				<?php foreach ($series as $k => $campo): ?>
					<?php $soma = $soma + $campo['values'][$k_titulo]; ?>
					<td>
						<?php echo $campo['values'][$k_titulo]; ?>
					</td>
				<?php endforeach; ?>
				<td><?php echo $soma; ?></td>
			</tr>				
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>