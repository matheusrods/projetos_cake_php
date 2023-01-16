<div class="modal-dialog modal-lg" style="position: static; width:900px;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Ocorrência Integração Esocial: <?php echo $codigo_esocial_evento; ?></h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<?php
			$id_ocorrencia = 0;
			if($dados){
        		foreach ($dados as $dado){
        			$id_ocorrencia++; 
        	?>
				<div>
					<span style="font-size: 1.2em">							
						<div class='row-fluid inline'>
							<b>ID Ocorrênia:</b> <?php echo $id_ocorrencia; ?>
							<br><br>
							<b>Ocorrência:</b> <?php echo $dado['OcorrenciaIntEsocialEvento']['descricao_ocorrencia']; ?>
							<br><br>						
						</div>						
					</span>
				</div>
				<hr>
			<?php 
				}
			}//fim if
			else {
				echo "<div class='alert'>Não existem Ocorrências.</div>";
			}
			?>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="ocorrencias(<?php echo $codigo_esocial_evento; ?>, 0);"class="btn btn-danger">FECHAR</a>				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();

});



</script>