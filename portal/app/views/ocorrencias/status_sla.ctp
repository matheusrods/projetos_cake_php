<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Ocorrencia', array('autocomplete' => 'off', 'url' => array('controller' => 'ocorrencias', 'action' => 'status_sla'))) ?>
            <?php echo $this->BForm->input('tipo', array('class' => 'input-medium', 'options' => array(Ocorrencia::SETOR_GERAL => 'Geral', Ocorrencia::SETOR_BSAT => 'Buonnysat', Ocorrencia::SETOR_PRONTA_RESPOSTA => 'Pronta Resposta'), 'label' => false)) ?>
            <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn btn-primary')) ?>
        <?php echo $this->BForm->end() ?>
    </div>  
</div>
<?php $this->addScript($this->Javascript->codeBlock('setup_datepicker()')) ?>
<?php if (!empty($dados)): ?>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	<table class="table table-striped table-bordered tablesorter">
		<thead>
			<tr>
				<th><?= $this->Html->link('Tipo', 'javascript:void(0)') ?></th>
				<th class="numeric"><?= $this->Html->link('Ocorrências', 'javascript:void(0)') ?></th>
			</tr>
		</thead>
		<tbody>
		    <?php $qtd_tipos = 0; ?>
		    <?php $qtd_ocorrencias = 0; ?>
			<?php foreach ($dados['series'] as $serie): ?>
				<tr>
					<td><?php echo str_replace("'", "", $serie['name']) ?></td>
					<td class="numeric"><?php echo $this->Html->link($serie['values'], 'javascript:void(0)', array('onclick' => 'ocorrencias_consulta(this)')) ?></td>
				</tr>
				<?php $qtd_tipos += 1; ?>
				<?php $qtd_ocorrencias += $serie['values']; ?>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
		    <th class="numeric"><?= $qtd_tipos ?></th>
		    <th class="numeric"><?= $qtd_ocorrencias ?></th>
		</tfoot>
	</table>
	<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter()")) ?>
	<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
	<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dados['eixo_x'], $dados['series'], array(
	    'renderTo' => 'grafico',
	    'chart' => array('type' => 'pie'),
	))); ?>
<?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('ocorrencias')) ?>