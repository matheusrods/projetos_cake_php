<table class='table table-striped'>
	<thead>
		<th>Data</th>		
		<th>Status</th>
		<th>Dias</th>
	</thead>
	<tbody>
		<?php if($results): ?>
			<?php foreach ($results as $key => $result): ?>
				<?php
					if(empty($result['TCveiChecklistVeiculo']['cvei_data_cancelamento'])):
						if($result['TCveiChecklistVeiculo']['cvei_status']):
							$status_checklist = 1;
						else:
							$status_checklist = 2;
						endif;
					else:
						$status_checklist = 3;	
					endif;			
				?>
				<tr>
					<td><?= $result['TCveiChecklistVeiculo']['cvei_data_cadastro'] ?></td>					
					<td>
					<a href="javascript:abrir('
				<?= $this->Html->url(						
						array('controller'    => 'veiculos', 
						      'action'        => 'visualizar_checklist', 
						      'VeiculoSinteticoChecklist',
						      'cvei_codigo'   =>$result['TCveiChecklistVeiculo']['cvei_codigo'],
						      'veic_placa'    =>$result['TVeicVeiculo']['veic_placa'],
						      'codigo_cliente'=>$result['TCveiChecklistVeiculo']['cvei_pess_oras_codigo']
						)) 
			    ?>')"><?= $status[$status_checklist] ?> </a></td>
					<td><?= $result[0]['dias_checklist'] ?></td>					
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<?php $key = 0 ?>
		<?php endif ?>
	</tbody>
</table>
<?php echo $this->Javascript->codeBlock("
		function abrir(URL) {	
		var janela = window_sizes();	 
		  window.open(URL, janela, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
		 
		}
")
?>
