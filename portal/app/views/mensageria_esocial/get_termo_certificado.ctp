<div class="modal-dialog modal-lg" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>TERMO DE CIÊNCIA E RESPONSABILIDADE</h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">


			<div class="inline">
				<?php
					echo $termo['TermoUso']['descricao'];
				?>
			</div>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="termo_uso(0);"class="btn btn-danger">FECHAR</a>
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