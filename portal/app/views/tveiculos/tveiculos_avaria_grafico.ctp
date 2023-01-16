<?php if(empty($dadosGrafico)): ?>
    <div class="alert">
        Nenhum registro encontrado.
    </div>
<?php else: ?>  
    <div id="grafico_tipo_veiculo" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
<script>
var chart;
$(document).ready(function() {
    var colors = Highcharts.getOptions().colors,
        categories = <?php echo "['".implode("','",array_keys($dadosGrafico))."']" ?>,
        data = [
        <?php 
        $i = 0;
        $total_geral = $dadosGrafico['total'];
        unset($dadosGrafico['total']);
        foreach($dadosGrafico as $veiculo => $avarias){ 
            if($agrupamento == 'local')
        	   unset($avarias['Sem Avaria']);
        	   unset($avarias[' ']);
        	?>
        {
        	y: <?php echo array_sum($avarias); ?>,
        	color: colors[<?php echo $i; ?>],
        	drilldown:{
        		name: 'Avarias <?php echo $veiculo; ?>',
        		categories: [<?php echo "'".implode("','", array_keys($avarias))."'"; ?>],
    			data: [<?php echo implode(",", $avarias); ?>],
    			color: colors[<?php echo $i; ?>]
        	}
        	
    	},
    	<?php 
			$i++;
    	} ?>
    	],       
        browserData = [],
        versionsData = [],
        i,
        j,
        dataLen = data.length,
        drillDataLen,
        brightness;

    // Build the data arrays
    for (i = 0; i < dataLen; i += 1) {

        // add browser data
        browserData.push({
            name: categories[i],
            y: data[i].y,
            color: data[i].color
        });

        // add version data
        drillDataLen = data[i].drilldown.data.length;
        for (j = 0; j < drillDataLen; j += 1) {
            brightness = 0.2 - (j / drillDataLen) / 5;
            versionsData.push({
                name: data[i].drilldown.categories[j],
                y: data[i].drilldown.data[j],
                color: Highcharts.Color(data[i].color).brighten(brightness).get()
            });
        }
    }

    // Create the chart
    chart = new Highcharts.Chart({
    //$('#grafico_tipo_veiculo').highcharts({
        chart: {
            type: 'pie',
            renderTo: 'grafico_tipo_veiculo'
        },
        title: {        
            text: <?php switch ($agrupamento) { 
                    case 'tipo':
                        echo '"Tipo de Veículos por Avaria"';
                        break;
                    case 'local':
                        echo '"Tipo de Veículos por Local Avariado"';
                        break;
                    case 'total':
                        echo '"Tipo de Veículos Total Avariado"';
                        break;
                    case 'local_vistoria':
                        echo '"Local Vistoria / Avaria"';
                        break;
                    case 'transportadora':
                        echo '"Transportadora / Avaria"';
                        break;

                }    ?>
        },
        yAxis: {
            title: {
                text: 'Total percent market share'
            }
        },
        plotOptions: {
            pie: {
                shadow: false,
                center: ['50%', '50%']
            }
        },
        credits: {
            enabled: false
        },
        // tooltip: {
        //     valueSuffix: '%'
        // },
        series: [{
            name: 'Veículos',
            data: browserData,
            size: '60%',
            dataLabels: {
                formatter: function () {
                    return this.y > 2 ? this.point.name + " " + this.percentage.toFixed(2) + " %": null;
                },
                color: 'white',
                distance: -30
            }
        }, {
            name: 'Avarias',
            data: versionsData,
            size: '80%',
            innerSize: '60%',
            dataLabels: {
                formatter: function () {
                    // display only if larger than 1
                    return this.y > 0 ? '<b>' + this.point.name + ':</b> ' + this.percentage.toFixed(2) + '%'  : null;
                }
            }
        }]
    });
});
</script>
<?php endif; ?>