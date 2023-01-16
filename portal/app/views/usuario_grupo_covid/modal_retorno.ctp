<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Respostas Retorno ao Trabalho</h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<?php
			if($dados){
				
				print "<h5>Data/Hora da última resposta: {$dados[0][0]['dt']} - {$dados[0][0]['hr']}</h5><br>";

        		foreach ($dados as $dado){ 
        	?>
				<div style="float: left;">
					<span style="font-size: 1.2em">
						<b><?php echo utf8_encode($dado[0]['label_questao']); ?>:</b>
						<?php echo utf8_encode($dado[0]['label']); ?>
					</span>
				</div>
				<br /><br /><br />
			<?php 
				}
			}//fim if
			else {
				echo "Não existe respostas de retorno ao trabalho.";
			}
			?>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="retorno(<?php echo $cpf; ?>, 0);"class="btn btn-danger">FECHAR</a>				
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