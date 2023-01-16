<div class="row-fluid">
	<span class="span12 span-right">			
		<?= $this->Html->link('<i class="icon-plus icon-white"></i>',
		"javascript:adicionar({$this->data['Regulador']['codigo']})",
		array('escape' => false,'class' => 'btn btn-success', 'title' => 'Incluir Região', 
			)
		) ?>
	</span>	
</div>
<div id="regioes-reguladores" class="grupo"></div> 

<?= $this->Javascript->codeBlock("
	$(function(){
		var codigo_regulador = {$this->data['Regulador']['codigo']};
		var element_div      = '#regioes-reguladores';
	    var div = jQuery(element_div);
	    bloquearDiv(div);
	    div.load(baseUrl + 'reguladores_regioes/regioes_por_regulador/' + codigo_regulador + '/' + Math.random() );
	});

	function carrega_por_window(codigo_regulador){
		var element_div      = '#regioes-reguladores';
	    var div = jQuery(element_div);
	    bloquearDiv(div);
	    div.load(baseUrl + 'reguladores_regioes/regioes_por_regulador/' + codigo_regulador + '/' + Math.random() );
	}	

	function adicionar(codigo){
		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/reguladores_regioes/incluir/'+ codigo +'/'+ Math.random());

	    field = document.createElement('input');

	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();
	}
");?>



