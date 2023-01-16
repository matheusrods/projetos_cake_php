<?php echo $this->Buonny->link_css('jquery-ui-timepicker-addon'); ?>
<style type="text/css">
	div.control-group.input.select label { float: left; line-height: 30px;  margin: 0 15px 5px; }
	#ajtStatus { line-height: 30px; margin: 0 0 0 5px; }
	.icoStatus { float: left; margin: 0 15px 0 0; }
	#ManutencaoDataRetorno { width: 110px !important; }
	.dtRetorno { min-width: 370px; }
</style>
<?php echo $this->BForm->create(
		'Manutencao', 
		array(
			'autocomplete'=>'off', 
			'url'=>array(
				'controller'=>'Sistemas', 
				'action'=>'manutencao'
			)
		)
	)?>
	<div class="well">
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('aplicacao_ativa',array('label' => 'Status da Aplicação', 'options' => $aplicacao_ativa,'class'=>'input-large'));?>	
			<div class="icoStatus">
			<?php 
				if($this->data['Manutencao']['aplicacao_ativa'] == 0) {
					echo '<span id="ajtStatus" title="Aplicação Desativada" class="badge-empty badge badge-important"></span>';
				} elseif ($this->data['Manutencao']['aplicacao_ativa'] == 1) {
					echo '<span id="ajtStatus" title="Aplicação Ativada" class="badge-empty badge badge-success"></span>';
				}
			?>
			</div>
			<?php 
				($infoUserOnOffAplication == '') ? $pValor = $infoUserOnOffAplication : $pValor = $infoUserOnOffAplication[0];
				echo $this->BForm->input('data_retorno', array(
						'label' => 'Data prevista de retorno da aplicação',
						'placeholder'=>'Data Retorno', 
						'class' => 'input-small datahora', 
						'title' => 'Data prevista para o retorno da aplicação',
						'value'=> $pValor
					)
				); 
				if(!($infoUserOnOffAplication == '')){
					echo '<div id="usrOperador" class="control-group input select"><label>Desativado por .: '.$infoUserOnOffAplication[1].'</label></div>';
				}
			?>
		</div>	
	</div>
		<div class='form-actions'>
    	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    	<?php echo $html->link('Voltar', array('controller'=>'Sistemas','action'=>'tarefas_desenvolvimento'), array('class'=>'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
<?php if ( isset($logs)) : ?>
	<?php if (!empty($logs)) : ?>
		<table class='table table-striped'>
			<thead>
				<th class='input-mini' style="text-align:center">Status</th>
				<th class='input-mini'>Data</th>
				<th class="input-mini">Código</th>
				<th class='input-medium'>Usuário</th>
			</thead>
			<tbody>
				<?php foreach ($logs as $key => $log): ?>
					<tr>
						<td style="text-align:center">
						<?php 
							if(trim($log[0]) == 'ON')
								echo "<span title='Aplicação Ativada' class='badge-empty badge badge-success'></span>";
							else
								echo "<span title='Aplicação Desativada' class='badge-empty badge badge-important'></span>";
						?>
						</td>
						<td><?php echo $log[1]; ?></td>
						<td><?php echo $log[3]; ?></td>
						<td><?php echo $log[2]; ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4"><!--strong>Total: <?php echo $total; ?></strong--></td>
				</tr>
			</tfoot>
		</table>
	<?php endif; ?>
<?php endif; ?>
<script type="text/javascript">
	$(document).ready(function(){
		setup_datepicker();
		$(this).datetimepicker({ minDate: -0 });
		$('#ManutencaoDataRetorno').parent().attr('class','control-group input select dtRetorno');
		$('#ManutencaoDataRetorno').parent().hide();
		if($('#ManutencaoAplicacaoAtiva').val() == 0){
			$('#ManutencaoDataRetorno').parent().show();
			$('#usrOperador').show();
		} 
		$('#ManutencaoAplicacaoAtiva').on('change', function() {
			if(!($('#ManutencaoAplicacaoAtiva option:selected').val() == 0)){
				$('#ManutencaoDataRetorno').parent().hide();
				$('#usrOperador').hide();
			} else{
				$('#ManutencaoDataRetorno').parent().show();
				$('#usrOperador').show();
			}
		});
	});
</script>
