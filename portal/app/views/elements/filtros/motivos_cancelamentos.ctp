<div class='well'>

	<?php echo $this->BForm->create('MotivoCancelamento', array('onsubmit' => 'atualizaListagem(); return false;')) ?>
	<div class="row-fluid inline">
	    <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>
	</div>        
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $this->BForm->end(); ?>
</div>

<?php echo $this->Javascript->codeBlock('
		
		
	$(document).ready(function(){
		atualizaListagem();
	});
		
		
	function atualizaListagem() {
		$.ajax({
	        type: "POST",
	        url: baseUrl + "motivos_cancelamentos/listagem/" + Math.random(),
	        dataType: "html",
			data: $("#MotivoCancelamentoIndexForm").serialize(),
	        beforeSend: function() {
				$("#lista").html("<div class=\"well\"><img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"></div>");
			},
	        success: function(conteudo) {
				$("#lista").html(conteudo);
	        },
	        complete: function() {
				
			}
		});		
	}
');


