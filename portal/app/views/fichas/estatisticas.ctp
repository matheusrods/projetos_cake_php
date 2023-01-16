<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Ficha', array('autocomplete' => 'off', 'url' => array('controller' => 'fichas', 'action' => 'estatisticas'))) ?>
            <?php echo $this->BForm->input('tipo_cobranca', array('class' => 'input-medium', 'options' => array(1 => 'Somente Cobrados', 2 => 'Sem Cobrança'), 'label' => false, 'empty' => 'Todos')) ?>
            <?php echo $this->BForm->input('tipo_smonline', array('class' => 'input-medium', 'options' => array(1 => 'Somente SM Online', 2 => 'Sem SM Online'), 'label' => false, 'empty' => 'Todos')) ?>
            <?php echo $this->BForm->input('tipo_periodo', array('class' => 'input-small', 'options' => array(1 => 'Mensal', 2 => 'Diário', 3 => 'Hora'), 'label' => false)) ?>
            <?php $tipo_periodo = (isset($this->data['Ficha']['tipo_periodo']) ? $this->data['Ficha']['tipo_periodo'] : LogFaturamentoTeleconsult::TIPO_PERIODO_MENSAL) ?>
            <div id='tipo_periodo_mensal'>
                <?php echo $this->BForm->input('somente_ano', array('class' => 'input-small', 'options' => $anos, 'label' => false)) ?>
            </div>
			<div id='tipo_periodo_diario'>
                <?php echo $this->BForm->input('ano', array('class' => 'input-small', 'options' => $anos, 'label' => false)) ?>
                <?php echo $this->BForm->input('mes', array('class' => 'input-small', 'options' => $meses, 'label' => false)) ?>
			</div>
            <div id='tipo_periodo_hora'>
                <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Início', 'label' => false)) ?>
            </div>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->addScript($this->Javascript->codeBlock("
		jQuery(document).ready(function() {
			setup_datepicker();						
			function controla_campos(){			
				tipo_periodo = jQuery('#FichaTipoPeriodo').val();
				if (tipo_periodo == 1) {				
					jQuery('#tipo_periodo_mensal').show();
					jQuery('#tipo_periodo_hora').hide();
					jQuery('#tipo_periodo_diario').hide();
				} else if (tipo_periodo == 2) {				
					jQuery('#tipo_periodo_diario').show();
					jQuery('#tipo_periodo_mensal').hide();
					jQuery('#tipo_periodo_hora').hide();
				} else {
					jQuery('#tipo_periodo_hora').show();
					jQuery('#tipo_periodo_diario').hide();
					jQuery('#tipo_periodo_mensal').hide();
				}
			}

			$('#FichaTipoPeriodo').change(function() {
				controla_campos();
			});
			controla_campos();
		})"
	)) ?>
<?php if (!empty($dados)): ?>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	<?php echo $this->element('fichas/estatisticas_teleconsult', array('dados' => $dados)) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>	
    <?php echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
        'renderTo' => 'grafico',
        'chart' => array('type' => 'line'),
        'yAxis' => array('title' => ''),
        'xAxis' => array('labels' => array('rotation' => -75, 'y' => 10), 'gridLineWidth' => 1),
        'legend' => array('align' => 'center', 'verticalAlign' => 'bottom', 'layout' => 'horizontal', 'y' => 0, 'x' => 0),
        
    ))); ?>
<?php endif ?>
 