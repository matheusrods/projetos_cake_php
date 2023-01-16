<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Medico.nome', array('label' => 'Nome (*)', 'class' => 'input-xxlarge',)); ?>
	<?php echo $this->BForm->hidden('Medico.codigo', array('value' =>  !empty($this->data['Medico']['codigo'])? $this->data['Medico']['codigo'] : '')); ?>
</div>

<div class="row-fluid inline">
	<?php echo $this->BForm->input('Medico.especialidade', array('label' => 'Especialidade (*)', 'class' => 'input-xxlarge')); ?>
</div>  
<div class='row-fluid inline'>
	<?php echo $this->BForm->input("Medico.codigo_conselho_profissional", array('label' => 'Conselho Profissional (*)', 'class' => 'input-medium', 'options'=>$conselho_profissional, 'empty'=>'Selecione')) ?>
	<?php echo $this->BForm->input('Medico.numero_conselho', array('label' => 'Número do Conselho (*)', 'class' => 'input just-number', 'maxlength' => 10)); ?>
	<?php echo $this->BForm->input("Medico.conselho_uf", array('label' => 'Estado (*)', 'class' => 'input-small', 'options'=>$estado, 'empty'=>'Selecione')) ?>
 </div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('cpf', array('class' => 'input-medium cpf', 'label' => 'CPF'));?>
	<?php echo $this->BForm->input('nis', array('class' => 'input just-number', 'label' => 'Nº NIS (PIS)'));?>
	<?php echo $this->BForm->input('rqe', array('class' => 'form-control', 'label' => 'RQE'));?>
</div>
 <hr />
<h3>Endereço</h3>
 <?php echo $this->BForm->hidden('MedicoEndereco.codigo', array('value' =>  !empty($this->data['MedicoEndereco']['codigo'])? $this->data['MedicoEndereco']['codigo'] : '')); ?>
 <div class='row-fluid inline'>
   	<?php echo $this->BForm->input('MedicoEndereco.cep', array('class' => 'form-control formata-cep', 'label' => 'CEP', 'maxlength' => 8, 'class' => 'input-medium')); ?>
	<img src="/portal/img/default.gif" id="carregando_endereco" style="padding: 30px 0 0 10px; display: none;">
	<span style="float: left; padding: 30px 0 0 10px; font-size: 10px;"><a href="javascript:void(0);" onclick="buscaCEP();">COMPLETAR ENDEREÇO</a></span>
 </div>
 <div class='row-fluid inline'>
   	<?php echo $this->BForm->input('MedicoEndereco.logradouro', array('class' => 'form-control', 'label' => 'Logradouro ( * )', 'class' => 'input-xlarge')); ?>
   	<?php echo $this->BForm->input('MedicoEndereco.numero', array('class' => 'form-control', 'label' => 'Número ( * )', 'class' => 'input-small')); ?>
   	<?php echo $this->BForm->input('MedicoEndereco.complemento', array('class' => 'form-control', 'label' => 'Complemento', 'class' => 'input-medium')); ?>
   	<?php echo $this->BForm->input('MedicoEndereco.bairro', array('class' => 'form-control', 'label' => 'Bairro ( * )', 'class' => 'input-large')); ?>
</div>
<div class='row-fluid inline'>   
   	<?php echo $this->BForm->input('MedicoEndereco.codigo_estado_endereco', array('label' => 'Estado ( * )', 'class' => 'form-control uf input-xarge', 'style' => 'text-transform: uppercase;', 'empty' => false, 'options' => $estados, 'onchange' => 'proposta.buscaCidade(this, null, null, "PropostaCredEndereco0CodigoCidadeEndereco", null, null, 0)')) ?>
    <span id="cidade_combo">
    	<?php echo $this->BForm->input('MedicoEndereco.codigo_cidade_endereco', array('label' => 'Cidade ( * )', 'class' => 'form-control', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $cidades)) ?>
    </span>
    <div id="carregando_cidade" style="display: none; text-align: left; border: 1px solid #CCCCCC; padding: 30px;">
    	<img src="/portal/img/ajax-loader.gif" border="0"/>
    </div>
</div> 
			
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'medicos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(function() { setup_mascaras(); });
		
	function buscaCEP() {
		var erCep = /^\d{5}-\d{3}$/;
		var cepCliente = $.trim($("#MedicoEnderecoCep").val());
		
		if(cepCliente != "") {
			cepCliente = cepCliente.replace("-", "");
			if(cepCliente.length == 8) {
			    $.ajax({
			        type: "POST",
			        url: "/portal/enderecos/buscar_endereco_cep/" + cepCliente,
			        dataType: "json",
			        beforeSend: function() { $("#carregando_endereco").show(); },
			        success: function(json) {
			    		if(json.VEndereco) {
							$("select[name=\"data[MedicoEndereco][codigo_estado_endereco]\"]").val(json.VEndereco.endereco_codigo_estado);
							$("input[name=\"data[MedicoEndereco][logradouro]\"]").val(json.VEndereco.endereco_tipo + " " +  json.VEndereco.endereco_logradouro);
							$("input[name=\"data[MedicoEndereco][bairro]\"]").val( json.VEndereco.endereco_bairro );
							$.ajax({
						        type: "POST",
								url: "/portal/enderecos/carrega_combo_cidade/" + json.VEndereco.endereco_codigo_estado,
								dataType: "html",
								beforeSend: function() {
						        	$("#cidade_combo").hide();
						        	$("#carregando_cidade").show();
						        },
						        success: function(retorno) {
						        	$("#MedicoEnderecoCodigoCidadeEndereco").html( retorno );
						        },
						        complete: function() { 
									$("select[name=\"data[MedicoEndereco][codigo_cidade_endereco]\"]").val(json.VEndereco.endereco_codigo_cidade);
			
						        	$("#cidade_combo").show();
						        	$("#carregando_cidade").hide();
									$("#MedicoEnderecoCodigoCidadeEndereco").show();		
						        }
						    });
			    		} else {
			    			alert("Cep não encontrado!");
			    		}		
			        },
			        complete: function() { $("#carregando_endereco").hide(); }
			    });
			} else if(cepCliente.length > 0) {
				alert("cep inválido");
			}			
		}
	}
'); ?>
