<h5>Treinamento</h5>
<div id="cockpit-rma" >
	<table class='table table-striped'>
		<thead>
			<th class='input-xlarge'>Descrição</th>
			<th class='input-small'>Data Curso</th>
			<th class='input-mini numeric'>Nota</th>
			<th class='input-small'>Status</th>
		</thead>
		<tbody>
			<?php if(isset($dados_curso_motorista) && !empty($dados_curso_motorista)):?>
				<?php foreach ($dados_curso_motorista as $dado): ?>
					<tr>
						<td class='input-xlarge'><?= $dado['ErpCurso']['CUR_Descricao'] ?></td>
						<td class='input-small'><?= $dado['CbqTurmaItem']['Dta_Cad'] ?></td>
						<td class='input-min numeric'><?= $dado['CbqTurmaItem']['TUI_Nota'] ?></td>
						<td class='input-small '><?= ($dado['CbqTurmaItem']['TUI_Nota'] > 70)?'APROVADO':'REPROVADO' ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif;?>
		</tbody>
	</table>
</div>