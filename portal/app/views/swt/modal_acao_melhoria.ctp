<div class="modal-dialog modal-lg" style="position: static; width:900px;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Ações de Melhoria Criada/Relacionada pelo Walk & Talk: <?php echo $codigo_respondido; ?></h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<?php
			if($dados){
        		foreach ($dados as $dado){ 
        			$dado = $dado[0];
        	?>
				<div>
					<span style="font-size: 1.2em">							
						<div class='row-fluid inline'>
							<b>ID de Ação:</b> <?php echo $dado['codigo']; ?>
							<br><br>
							<b>Item Observado:</b> <?php echo $dado['item_observado']; ?>
							<br><br>
							<b>Tipo da Ação:</b> <?php echo $dado['tipo']; ?>
							<br><br>
							<b>Criticidade:</b> <?php echo $dado['criticidade']; ?>
							<br><br>
							<b>Origem:</b> <?php echo $dado['origem']; ?>
							<br><br>
							<b>Responsável:</b> <?php echo $dado['responsavel']; ?>
							<br><br>
							<b>Prazo:</b> <?php echo Comum::formataData($dado['prazo'],'ymd','dmy'); ?>
							<br><br>
							<b>Descrição Desvio:</b> <?php echo $dado['desc_desvio']; ?>
							<br><br>
							<b>Descrição Ação:</b> <?php echo $dado['desc_acao']; ?>
							<br><br>
							<b>Descrição Local Ação:</b> <?php echo $dado['desc_local_acao']; ?>
							<br><br>
						</div>						
					</span>
				</div>
				<hr>
			<?php 
				}
			}//fim if
			else {
				echo "<div class='alert'>Não existem ações de melhorias para esta análise de Walk & Talk.</div>";
			}
			?>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="respostas_acoes_melhoria(<?php echo $codigo_respondido; ?>, 0);"class="btn btn-danger">FECHAR</a>				
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