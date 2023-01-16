<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<div class='form-procurar'> 
	<div class='well'>
		<?php echo $this->BForm->create('DashboardRelatorio', array('autocomplete' => 'off', 'url' => array('controller' => 'dados_saude_consultas', 'action' => 'relatorio_imc'))) ?>
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

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_datepicker();
        setup_mascaras();
        if(!$("#DashboardRelatorioCodigoCliente").is(":visible")){
        	$("#DashboardRelatorioCodigoCliente").parent().hide();
        }
	});', false);
?>

<?php if (empty($resultado)): ?>
<div class="alert">
	Defina os critérios de filtros.
</div>
<?php else: ?>
<div id="grafico" class='gadget'></div>
	<table class="table table-striped table-bordered tablesorter">
		<thead>
			<tr>
				<th>Nível</th>
				<th class="numeric">Quantidade</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($faixas_imc as $k => $faixa): ?>
				<tr>
					<td><?php echo $faixa; ?></td>
					<td class="numeric"><?php echo $resultado[$k]; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php echo $this->Javascript->codeBlock(
$this->Highcharts->render(array(), $series, array(
'renderTo' => 'grafico',
'title' => 'IMC (Índice Massa Corporal)',
'chart' => array('type' => 'pie'),
))); ?> 
<?php endif; ?>
