<h4>Equipamentos Móveis (Iscas)</h4>
<div class="actionbar-right">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success nova-isca', 'escape' => false)); ?>
</div>
<table class='table table-striped isca'>
	<thead>
		<th class='input-large'>Tecnologia</th>
		<th class='input-large'>Terminal</th>
		<th></th>
	</thead>
	<tbody>
	    <?php
            $contadorIsca = 0;
            if (empty($this->data['RecebsmIsca'])):
                echo $this->element('solicitacoes_monitoramento/incluir_sm_iscas_item', array('key'=>0));
            else:
                foreach ($this->data['RecebsmIsca'] as $key => $recebsmIsca):
                    $contadorIsca = max($contadorIsca, $key);
                    echo $this->element('solicitacoes_monitoramento/incluir_sm_iscas_item', array('key'=>$key));
                endforeach;
            endif;
        ?>
	</tbody>
</table>

<?php echo $this->Javascript->codeBlock('
	var contador_isca = ' . $contadorIsca . ';
	
	$(document).ready(function() {
		$(document).on("click","a.nova-isca", function(){
			var conteiner = $("table.isca tbody");
			contador_isca++;

			$.ajax({
				url: baseUrl + "solicitacoes_monitoramento/nova_isca_item/"+ contador_isca +"/"+ Math.random(),
				dataType: "html",
				success: function(data){
					conteiner.prepend(data);
				}
			});
		});

		$(document).on("click", "a.remove-isca", function(){
			$(this).parent().parent().remove();
			return false;
		});
		
	});
	
');
?>