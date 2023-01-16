<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>PCMSO</h3>
		</div>

		<div class="modal-body" style="max-height: 360px;font-size: 15px;">

			<b>Unidade: </b><?php echo $dados_modal['codigo_unidade'] ?> - <?php echo $dados_modal['nome_fantasia'] ?><br />
			<b>Setor: </b><?php echo $dados_modal['setor'] ?><br />
			<b>Cargo: </b><?php echo $dados_modal['cargo'] ?><br />
			<?php if( !empty($dados_modal['funcionario']) && isset($dados_modal['funcionario']) ) : ?>
				<b>Funcionário: </b><?php echo $dados_modal['funcionario'] ?><br />
			<?php endif; ?> 

			<hr style="border-bottom: 0px;">

			<?php if( !empty($dados_modal['exames']) ) : ?>

				<table class="table table-striped" style="margin-bottom: 0px;">

    				<thead>
        				<tr>
            				<th class="input-mini">Exames</th>
            			</tr>
            		</thead>

            		<tbody>
            			<?php foreach($dados_modal['exames'] as $codigo_exame => $descricao_exame) :?>
            				<tr>
            					<td><?php echo $descricao_exame ?></td>
            				</tr>
            			<?php endforeach; ?>
            		</tbody>

            	</table>

			<?php else : ?>

				<div class="alert" style="margin-bottom: 0px;">Nenhum exame encontrado.</div>

			<?php endif; ?>

		</div>

		<div class="modal-footer">
			<center><a href="javascript:void(0);"onclick="modal_visualizar_pcmso(<?php echo $dados_modal['codigo_unidade']; ?>,<?php echo $dados_modal['codigo_setor']; ?>,<?php echo $dados_modal['codigo_cargo']; ?>, 0);"class="btn btn-danger">FECHAR</a></center>
		</div>

	</div>
</div>