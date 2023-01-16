<div class="modal-dialog modal-lg" style="position: static; width:900px;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Riscos da Observação: <?php echo $codigo_obs; ?></h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<?php
			if ($dados_riscos) {
				foreach ($dados_riscos as $dado) {
			?>
					<div class='well'>
						<div class="row-fluid inline">
							<?= $this->BForm->input('tipo_perigo_aspecto', array(
								"class"       => "input-large",
								"placeholder" => "Tipo do Perigo/Aspecto",
								"label"       => "Tipo do Perigo/Aspecto",
								"value"       => $dado[0]['risco_tipo_descricao'],
								"readonly"    => true,
							)) ?>
							<?= $this->BForm->input('perigo', array(
								"class"       => "input-large",
								"placeholder" => "Perigo",
								"label"       => "Perigo",
								"value"       => $dado[0]['perigo_aspecto_descricao'],
								"readonly"    => true,
							)) ?>
							<?= $this->BForm->input('risco', array(
								"class"       => "input-large",
								"placeholder" => "Risco",
								"label"       => "Risco",
								"value"       => $dado[0]['risco_impacto_descricao'],
								"readonly"    => true,
							)) ?>
						</div>
					</div>
					<br />
			<?php
				}
			} //fim if
			else {
				echo "<div class='alert'>Não existem riscos para esta observação.</div>";
			}
			?>
		</div>

		<div class="modal-footer">
			<div class="right">
				<a href="javascript:void(0);" onclick="riscos(<?php echo $codigo_obs; ?>, 0);" class="btn btn-danger">FECHAR</a>
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