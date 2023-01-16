<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>
<?php echo $this->element('fichas_scorecard/formulario_ficha', array('codigo_ficha'=>$codigo_ficha)); ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('fichas_scorecard')) ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		jQuery("#cliente :input, #ProfissionalCodigoDocumento").attr("readonly", "readonly");
		jQuery("#cliente :input option:not(:selected), #cliente a, .btn-limpar").remove();
		jQuery("#ProfissionalCodigoDocumento, #FichaScorecardCodigoCliente").unbind("blur");
		
		setup_mascaras();
		setup_datepicker();
		setup_limpar_sessao();
		
		jQuery("BODY").scrollspy({
		  offset: 80
		});
		var $window = jQuery(window);
		// side bar
		jQuery(".bs-docs-sidenav").affix({
		  offset: {
			top: function () { return $window.width() <= 980 ? 290 : 50 }
		  }
		});
	});
');
?>