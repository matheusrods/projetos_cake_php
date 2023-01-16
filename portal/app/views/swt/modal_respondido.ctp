<div class="modal-dialog modal-lg" style="position: static; width:900px;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Respostas Questionário Walk & Talk: <?php echo $codigo_respondido; ?></h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<?php
			if($dados_formatado){
        		foreach ($dados_formatado as $dado){ 
        	?>
				<div>
					<span style="font-size: 1.2em">
						<div class='row-fluid inline'>
							<b><?php echo $dado['titulo']; ?>:</b><br>
						</div>

						<?php foreach ($dado['questao'] as $quest) : ?>
							<div class='row-fluid inline'>
								<li>
									<?php echo $quest['descricao']; ?>:
								</li>
							</div>
							<div class='row-fluid inline'>
								<li style="margin-left: 3%; list-style-type: none;">
									<b>Resposta:</b> <?php echo $quest['resposta']; ?>
									<?php if(!empty($quest['criticidade'])) :?>
										<br>
										<b>Criticidade:</b> <?php echo $quest['criticidade']; ?>
									<?php endif; ?>
									<?php if(!empty($quest['motivo'])) :?>
										<br>
										<b>Motivo:</b> <?php echo $quest['motivo']; ?>
									<?php endif; ?>
								</li>
							</div>

						<?php endforeach; ?>
					</span>
				</div>
				<br />
			<?php 
				}
			}//fim if
			else {
				echo "<div class='alert'>Não existem respostas para este Walk & Talk.</div>";
			}
			?>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="respostas_formulario(<?php echo $codigo_respondido; ?>, 0);"class="btn btn-danger">FECHAR</a>				
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