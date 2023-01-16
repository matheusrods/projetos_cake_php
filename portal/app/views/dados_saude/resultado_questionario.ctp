<div class="modal-header">
	<h4 class="modal-title" id="gridSystemModalLabel">Checkup: <?php echo $checkups;?></h4>
</div>
<div class="modal-body">
	<table class="table table-striped">
		<thead>
			<tr>
				<td>Pergunta</td>
				<td>Resposta</td>
			</tr>
		</thead>
		<?php foreach($dados_questionarios as $k => $info_imc) : ?>
			<tbody>
    			<tr>
    				<td><?php echo $info_imc['Questao']['label']; ?></td>
    				<td><?php echo $info_imc['Resposta']['label']; ?></td>
    			</tr>					    			
			</tbody>
		<?php endforeach; ?>
	</table>
	<div>
		<h4 class="modal-title" id="gridSystemModalLabel">Pontuação: <?php echo $resultado;?></h4>
	</div>
</div>
<div class="modal-footer">
	<a href="javascript:void(0);" class="btn btn-warning" onclick="manipula_modal('modal_resultado', 0)">Fechar</a>
</div>