<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>PGR</h3>
		</div>

		<div class="modal-body" style="max-height: 360px;font-size: 15px;">

			<b>Unidade: </b><?php echo $dados_modal['codigo_unidade'] ?> - <?php echo $dados_modal['nome_fantasia'] ?><br />
			<b>Setor: </b><?php echo $dados_modal['setor'] ?><br />
			<b>Cargo: </b><?php echo $dados_modal['cargo'] ?><br />
			<?php if( !empty($dados_modal['funcionario']) && isset($dados_modal['funcionario']) ) : ?>
				<b>Funcionário: </b><?php echo $dados_modal['funcionario'] ?><br />
			<?php endif; ?> 
			
			<hr style="border-bottom: 0px;">

			<?php if( !empty($dados_modal['atribuicao']) ) : ?>
				<table class="table table-striped" style="margin-bottom: 0px;">
    				<thead>
        				<tr>
            				<th class="input-mini">Atribuição</th>            				
            			</tr>
            		</thead>
            		<tbody>
            			<?php foreach($dados_modal['atribuicao'] as $atribuicao) :?>
            				<?php if(!empty($atribuicao)): ?>
	            				<tr>
	            					<td><?php echo $atribuicao ?></td>
	            				</tr>
	            			<?php endif; ?>
            			<?php endforeach; ?>
            		</tbody>
            	</table>
			<?php else : ?>
				<div class="alert" style="margin-bottom: 0px;">Nenhuma atribuição encontrada.</div>
			<?php endif; ?>

			<hr style="border-bottom: 0px;">

			<?php if( !empty($dados_modal['riscos']) ) : ?>

				<table class="table table-striped" style="margin-bottom: 0px;">

    				<thead>
        				<tr>
            				<th class="input-mini">Agentes</th>
            				<th class="input-xlarge">Substâncias</th>
            			</tr>
            		</thead>

            		<tbody>
            			<?php foreach($dados_modal['riscos'] as $risco) :?>
            				<tr>
            					<td><?php echo $risco[0]['grupo'] ?></td>
            					<td><?php echo $risco[0]['nome_agente'] ?></td>
            				</tr>
            			<?php endforeach; ?>
            		</tbody>

            	</table>

			<?php else : ?>

				<div class="alert" style="margin-bottom: 0px;">Nenhum risco encontrado.</div>

			<?php endif; ?>

		</div>

		<div class="modal-footer">
			<center><a href="javascript:void(0);"onclick="modal_visualizar_ppra(<?php echo $dados_modal['codigo_unidade']; ?>,<?php echo $dados_modal['codigo_setor']; ?>,<?php echo $dados_modal['codigo_cargo']; ?>, 0);"class="btn btn-danger">FECHAR</a></center>
		</div>

	</div>
</div>