<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<div class='form-procurar'> 
	<div class='well'>
		<?php echo $this->BForm->create('DashboardRelatorio', array('autocomplete' => 'off', 'url' => array('controller' => 'dados_saude_consultas', 'action' => 'relatorio_posicao_questionarios'))) ?>
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

<table class="table table-striped table-bordered tablesorter">
	<thead>
		<tr>
			<th>Título do Formulário:</th>
			<th><center>Quantidade de pessoas que responderam:</center></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($series['values'] as $k_titulo => $field): ?>
			<tr>
				<td><?php echo $titulos[$k_titulo]; ?></td>
				<td>
					<center><?php echo $field; ?></center>
				</td>
			</tr>				
		<?php endforeach; ?>
	</tbody>
</table>

<script>
	var chart;
	$(document).ready(function() {
		
		chart = new Highcharts.Chart({
			colors: ['#85B4DB'],
	        chart: {
	            type: 'bar',
	            renderTo: 'grafico'
	        },
	        title: {
	            text: false
	        },
	        xAxis: {
	            categories: [<?php echo '\''.implode('\', \'', $titulos).'\''; ?>],
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
	            y: -5,
	            floating: true,
	            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
	            borderColor: '#AAA',
	            borderWidth: 3,
	            shadow: false
	        },
	        tooltip: {
	            formatter: function () {
	                return '<b>' + this.x + '</b><br/>' +
	                    'Total: ' + this.y;
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
						echo '{
							   name: \'Quantidade Preenchimentos\', 
						       data: ['.implode(',', $series['values']).']
						}, ';
				?>
			]
	    });
	});
</script>
<?php endif; ?>