
<style>
    .font21{
        font-size: 21px;
        line-height: 30px;
    }
</style>
<div class="modal-dialog modal-lg" style="position: static; width:900px;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Ações de melhorias da Observação: #<?php echo $codigo_obs; ?></b>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<?php
			if($acoes_melhorias){
        		foreach ($acoes_melhorias as $dado){
        	?>
			<div>
				<span style="font-size: 1.2em">							
					<div class='row-fluid inline'>
						<b>ID Ação de Melhoria:</b> <?= $dado['AcoesMelhorias']['codigo'];?>                   
						<br><br>
						<b>Tipo da Ação:</b> <?= $dado['AcoesMelhoriasTipo']['descricao'];?>
						<br><br>
						<b>Status da Ação:</b> <?= $dado['AcoesMelhoriasStatus']['descricao'];?>
						<br><br>
						<b>Criticidade da Ação:</b> <?= $dado['OrigemFerramenta']['descricao'];?>
						<br><br>
						<b>Descreva o desvio:</b> <?= $dado['AcoesMelhorias']['descricao_desvio'];?>
						<br><br>
						<b>Descreva a ação:</b> <?= $dado['AcoesMelhorias']['descricao_acao'];?>
						<br><br>
						<b>Local da Ação:</b> <?= $dado['Cliente']['razao_social'];?>
						<br><br>
						<b>Responsável da Ação:</b> <?= $dado['Responsavel']['nome'];?>
						<br><br>
						<b>Prazo de Conclusão da Ação:</b> <?= $dado['AcoesMelhorias']['prazo'];?>
						<br><br>
					</div>	
				</span>
			</div>
			<hr>
			<?php 
				}
			}//fim if
			else {
				echo "<div class='alert'>Não existem ações melhorias para esta observação.</div>";
			}
			?>
		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="acoes(<?php echo $codigo_obs; ?>, 0);"class="btn btn-danger">FECHAR</a>
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
