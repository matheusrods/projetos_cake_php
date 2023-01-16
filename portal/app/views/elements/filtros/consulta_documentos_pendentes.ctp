<div class='well'>
	<?php echo $bajax->form('Consulta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Consulta', 'element_name' => 'consulta_documentos_pendentes'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<div class="span12">
				<div class="span5" >
			  		<?php echo $this->BForm->input('codigo_estado_endereco', array('label' => 'UF', 'class' => 'form-control input-small', 'default' => '','options' => $list_estados, 'onchange' => 'buscaCidade(this);')); ?>
			  		<span id="cidade_combo">
			  			<?php echo $this->BForm->input('codigo_cidade_endereco', array('label' => 'Cidade', 'class' => 'form-control input-xlarge', 'default' => '','options' => $list_cidades)); ?>
			  		</span>
	    			<div id="carregando_cidade" style="display: none;">
	    				<label>Cidade</label>
    					<img src="/portal/img/ajax-loader.gif" border="0" style="padding-top: 7px;"/>
	    			</div>
			    </div>
				<div class="span5" style="margin-left: -70px;">
					<label>Credenciando:</label>
					<?php echo $this->Buonny->input_codigo_credenciado($this, 'codigo_proposta_credenciamento', 'Credenciado',false,'Consulta') ?>
				</div>
				<div class="span2" style="margin-left: -75px;">
					<?php echo $this->BForm->input('documento', array('label' => 'Documento', 'class' => 'form-control', 'default' => '','options' => $list_documentos)); ?>
				</div>
			</div>
    	</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
	function buscaCidade(element) {
		var	idEstado = $(element).val();
		$.ajax({
	        type: "POST",
	        url: "/portal/enderecos/carrega_combo_cidade/" + idEstado,
	        dataType: "html",
	        beforeSend: function() { 
	        	$("#cidade_combo").hide();
	        	$("#carregando_cidade").show();
	        },
	        success: function(retorno) {
	        	$("#ConsultaCodigoCidadeEndereco").html(retorno);
	        },
	        complete: function() { 
	        	$("#carregando_cidade").hide();
	        	$("#cidade_combo").show();
	        }
	    });
	}
		
    jQuery(document).ready(function(){
    	setup_mascaras();
    	setup_datepicker();
    	
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "consultas/documentos_pendentes/" + Math.random());
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Consulta/element_name:consulta_documentos_pendentes/" + Math.random())
        });
    });', false);
?>