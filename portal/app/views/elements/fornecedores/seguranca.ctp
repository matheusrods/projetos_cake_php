<div id="fornecedor-seguranca" class="fieldset" style="display: block;">
   <h3>Serviços</h3>
	<?php
	if(!empty($dados_seguranca)): ?>
		<table class="table table-striped">
		    <thead>
		        <tr>
		          <th>Serviço</th>
		        </tr>
		    </thead>
		    <?php foreach ($dados_seguranca as $seguranca): ?>
		      <tr>
		            <td><?php echo $seguranca['Servico']['descricao'] ?></td>
		      </tr>
		    <?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>