<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3><?php echo ($campo == 'conteudo' ? 'Conteúdo' : 'Retorno') ?> - Integração <?php echo $dados['LogIntegracao']['codigo'] ?></h3>
		</div>

		<div class="modal-body">
			<?php echo $dados['LogIntegracao'][$campo]?>
		</div>

		<div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="modal_exibicao_integracao(<?php echo $dados['LogIntegracao']['codigo']; ?>,'', 0);"class="btn btn-danger">FECHAR</a>
			</div>
		</div>

	</div>
</div>