<div id="regulador-regioes">
	<table class="table table-striped">
	  <thead>
	    <tr>
	      <th>Cidade</th>
	      <th>Latitude</th>
	      <th>Longitude</th>
	      <th>Raio</th>
	      <th>Prioridade</th>
	      <th></th>
	    </tr>
	  </thead>
		<?php foreach ($regioes as $regiao): ?>
		  <tr>
		      <td><?php echo $regiao['ReguladorRegiao']['cidade'] ?></td>
		      <td><?php echo $regiao['ReguladorRegiao']['latitude'] ?></td>
		      <td><?php echo $regiao['ReguladorRegiao']['longitude'] ?></td>
		      <td><?php echo $regiao['ReguladorRegiao']['raio'] ?></td>
		      <td><?php echo $regiao['ReguladorRegiao']['prioridade'] ?></td>
		      <td>
		          <?php echo $html->link('', "javascript:visualizar({$regiao['ReguladorRegiao']['codigo']})", array('class' => 'icon-eye-open', 'title' => 'Visualizar')) ?>
		          <?php echo $html->link('', "javascript:editar({$regiao['ReguladorRegiao']['codigo']})", array('class' => 'icon-edit', 'title' => 'Editar')) ?>
		          <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir-cliente-contato', 'title' => 'Excluir', 'onclick' => "excluir_regulador_contato({$regiao['ReguladorRegiao']['codigo']},{$regiao['ReguladorRegiao']['codigo_regulador']})")) ?>
		      </td>
		  </tr>
		<?php endforeach; ?>
	</table>
</div>
<?php echo $javascript->codeBlock("
	function excluir_regulador_contato(codigo_regulador_contato, codigo_regulador ) {
	    if (confirm('Deseja realmente excluir ?')){
			jQuery.ajax({
			    type: 'POST',
				url: baseUrl + 'reguladores_regioes/excluir/' + codigo_regulador_contato + '/' + Math.random(),
				success: function(data) {
					var div = jQuery('#regulador-regioes');
					bloquearDiv(div);
					div.load(baseUrl + 'reguladores_regioes/regioes_por_regulador/' + codigo_regulador + '/' + Math.random() );
				}
			});
		}
	}
	function editar(codigo){
		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/reguladores_regioes/editar/'+ codigo +'/'+ Math.random());

	    field = document.createElement('input');

	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();
	}

	function visualizar(codigo){
		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/reguladores_regioes/visualizar/'+ codigo +'/'+ Math.random());

	    field = document.createElement('input');

	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();
	}
");?>