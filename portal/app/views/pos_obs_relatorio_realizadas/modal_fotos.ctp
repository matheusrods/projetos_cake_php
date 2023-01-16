<div class="modal-dialog modal-lg" style="position: static; width:900px;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Fotos da Observação: <?php echo $codigo_obs; ?></h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<?php
			if($dados_fotos){
        		foreach ($dados_fotos as $dado){ 
        	?>
				<div>
					<img src="<?php echo $dado[0]['foto']; ?>" >
				</div>
				<br />
			<?php 
				}
			}//fim if
			else {
				echo "<div class='alert'>Não existem fotos para esta observação.</div>";
			}
			?>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="fotos(<?php echo $codigo_obs; ?>, 0);"class="btn btn-danger">FECHAR</a>				
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