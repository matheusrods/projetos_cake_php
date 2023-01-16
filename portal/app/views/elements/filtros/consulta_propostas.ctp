<div class='well'>
  <?php echo $bajax->form('ConsultaProposta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ConsultaProposta', 'element_name' => 'consulta_propostas'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
    	<div class="span12">
    		<div class="span3">
		    	<div>
				 	<?php echo $this->Buonny->input_periodo($this, 'ConsultaProposta', 'data_inicial', 'data_final', 'Data Cadastro');  ?>
				</div>    	
	    	</div>
			<div class="span9" style="margin-left: -10px;">
		    	<?php echo $this->BForm->input('razao_social', array('class' => 'form-control input-xlarge', 'placeholder' => 'Razão Social', 'label' => 'Credenciado')) ?>
		    	<?php echo $this->BForm->input('usuario', array('label' => 'Responsável', 'class' => 'input-large form-control', 'default' => '','options' => $list_usuarios)); ?>
		    	<?php echo $this->BForm->input('codigo_estado_endereco', array('label' => 'Estado', 'class' => 'form-control input-small', 'default' => '','options' => $list_estados, 'onchange' => 'buscaCidade(this);')); ?>
		  		<span id="cidade_combo">
		  			<?php echo $this->BForm->input('codigo_cidade_endereco', array('label' => 'Cidade', 'class' => 'form-control input-large', 'default' => '','options' => $list_cidades)); ?>
		  		</span>
    			<div id="carregando_cidade" style="display: none;">
	    				<label>Cidade</label>
    					<img src="/portal/img/ajax-loader.gif" border="0" style="padding-top: 7px;"/>
    			</div>
	    	</div>
	    	<?php echo $this->BForm->input('codigo_status_proposta_credenciamento', array('label' => 'Status', 'class' => 'input-large', 'default' => '','options' => $array_status, 'onchange' => 'controlaReprovado(this);')); ?>
	    	<div style="display: <?php (isset($this->data['ConsultaProposta']['codigo_status_proposta_credenciamento']) && ($this->data['ConsultaProposta']['codigo_status_proposta_credenciamento'] != '11') && ($this->data['ConsultaProposta']['codigo_status_proposta_credenciamento'] != '10')) ? 'none' : ''; ?>" id="atributos_reprovado">
			    <div class="span3" style="margin-left: 0;">
			    	<?php echo $this->BForm->input('motivo', array('label' => 'Motivo Cancelamento', 'class' => 'input-xlarge form-control', 'default' => '','options' => $list_motivos)); ?>    	
				</div>	    	
			    <div class="span3" >
					 <?php echo $this->Buonny->input_periodo($this, 'ConsultaProposta', 'data_inicial_cancelamento', 'data_final_cancelamento', 'Data Cadastro');  ?>
				</div>
			</div>
    	</div>    	
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
	function controlaReprovado(element) {
		if(($(element).val() == 10) || ($(element).val() == 11)) {
			$("#atributos_reprovado").show();
		} else {
			$("#atributos_reprovado").hide();
			$("#ConsultaPropostaDataInicialCancelamento").val("");
			$("#ConsultaPropostaDataFinalCancelamento").val("");
			$("#ConsultaPropostaMotivo").val("");
		}
	}
		
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
	        	$("#ConsultaPropostaCodigoCidadeEndereco").html(retorno);
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
    	controlaReprovado($("#ConsultaPropostaCodigoStatusPropostaCredenciamento"));
    	
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "consultas/propostas/" + Math.random());
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PropostaCredenciamento/element_name:consulta_propostas/" + Math.random())
        });
    });', false);
?>