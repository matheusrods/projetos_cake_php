<?php echo $this->BForm->create('Cat', array('url' => array('controller' => 'cat','action' => 'editar', $codigo), 'type' => 'post')); ?>
	<?php echo $this->BForm->input('codigo', array('type' => 'hidden', 'value' => $codigo)); ?>
	<?php echo $this->element('cat/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>
<div class="modal fade" id="modal_retif" data-backdrop="static"></div>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setup_mascaras(); setup_time(); setup_datepicker();
		$(".modal").css("z-index", "-1");
		$(".modal").css("width", "43%");
		$(".modal").css("top", "15%");

		if("' . $chama_modal . '" == "1") {
			modal_retificacao("'.$codigo.'", 1);
		}
	});

	function modal_retificacao(codigo_cat,mostra) {
		if(mostra) {
			
			var div = jQuery("div#modal_retif");
			bloquearDiv(div);
			div.load(baseUrl + "cat/modal_retificacao_cat/" + codigo_cat + "/" + Math.random());
	
			$("#modal_retif").css("z-index", "1050");
			$("#modal_retif").modal("show");

		} else {
			$(".modal").css("z-index", "-1");
			$("#modal_retif").modal("hide");
		}

	}		
'); ?>