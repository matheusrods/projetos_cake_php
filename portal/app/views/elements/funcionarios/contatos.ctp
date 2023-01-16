<div class="row-fluid">
	<span class="span12 span-right">
    <?= $this->BMenu->linkOnClick('<i class="icon-plus icon-white"></i>', 
		array('controller' => 'funcionarios_contatos', 'action' => 'incluir', $this->data['Funcionario']['codigo'] ), 
		array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Contato', 'onclick' => "return open_dialog(this, 'Contato', 960)")
	)?>
	</span>
</div>
<div id="contatos-funcionario" class="grupo"></div>

<?php echo $javascript->codeblock("
		
	jQuery(document).ready(function() {
	    codigo_funcionario = jQuery('#FuncionarioCodigo').val();
	    
	    if (window.vizualizar) {
	        carrega_contatos_funcionario_visualizar(codigo_funcionario);
	    } else {
	        carrega_contatos_funcionario(codigo_funcionario);
	    }
	});
		
	function carrega_contatos_funcionario(codigo_funcionario) {
	    var div = jQuery('#contatos-funcionario');
	    bloquearDiv(div);
	    div.load(baseUrl + 'funcionarios_contatos/contatos_por_funcionario/' + codigo_funcionario + '/' + Math.random() );
	}
	
	function carrega_contatos_funcionario_visualizar(codigo_cliente) {
	    var div = jQuery('#contatos-funcionario');
	    bloquearDiv(div);
	    div.load(baseUrl + 'funcionarios_contatos/contatos_por_funcionario_visualizar/' + codigo_funcionario + '/' + Math.random() );
	}
		
	function excluir_funcionario_contato(codigo_funcionario_contato, codigo_funcionario) {
	    if (confirm('Deseja realmente excluir ?')) {
			jQuery.ajax({
			    type: 'POST',
				url: baseUrl + 'funcionarios_contatos/excluir/' + codigo_funcionario_contato + '/' + Math.random()
				,success: function(data) {
					carrega_contatos_funcionario(codigo_funcionario);
				}
			});		
		}
	}		
");
?>