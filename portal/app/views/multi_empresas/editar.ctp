<ul class="nav nav-tabs">
	<li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
  	<?php if($authUsuario['Usuario']['codigo_empresa']) : ?>
  		<li><a href="#personalizacao" data-toggle="tab">Personalização</a></li>
  	<?php endif; ?>
</ul>
<div class="tab-content">
	<div class="tab-pane active" id="gerais">
		<?php echo $this->BForm->create('MultiEmpresa', array('url' => array('controller' => 'multi_empresas', 'action' => 'editar'), 'type' => 'post')); ?>
		<div class="well" id="gerais">
			<div class="row-fluid inline">
		    	<?php echo $this->BForm->hidden('MultiEmpresa.codigo'); ?>
		    	<?php echo $this->BForm->hidden('MultiEmpresaEndereco.codigo'); ?>
		    	
		      	<?php echo $this->BForm->input('MultiEmpresa.razao_social', array('class' => 'input-xxlarge', 'label' => 'Razão Social (*)')); ?>
		      	<?php echo $this->BForm->input('MultiEmpresa.nome_fantasia', array('class' => 'input-xxlarge', 'label' => 'Nome Fantasia (*)')); ?>
		  	</div>
		  	<div class="row-fluid inline">
		    	<?php echo $this->BForm->input('MultiEmpresa.codigo_documento', array('class' => 'input-medium cnpj', 'label' => 'CNPJ')); ?>

		    	<?php if(is_null($authUsuario['Usuario']['codigo_empresa'])) : ?>
			    	<?php if(!empty($this->passedArgs[0])): ?>
			        	<?php echo $this->BForm->input('MultiEmpresa.codigo_status_multi_empresa', array('label' => 'Status(*)', 'class' => 'input', 'default' => '','empty' => 'Status', 'options' => array('1' => 'Período Experimental', '2' => 'Ativo', '3' => 'Inativo'))); ?>
			      	<?php else: ?>	
			        	<?php echo $this->BForm->hidden('MultiEmpresa.codigo_status_multi_empresa', array('value' => 1)); ?>
			    	<?php endif;  ?>
		    	<?php endif; ?>

		    	<?php echo $this->BForm->checkbox('MultiEmpresa.integrar_com_naveg', array('label' => '', 'div' => array('class' => 'control-group input checkbox checkbox-parent-with-label')))?>Gerar Integração com o Naveg<br />
		  	</div>
		</div>
		<div class="well">
			<h3 >Endereço da Empresa:</h3>
			<?php echo $this->BForm->input('MultiEmpresaEndereco.cep', array('class' => 'input-medium formata-cep', 'label' => false, 'multiple', 'onchange' => '$("#pesquisa_cep").show();', 'label' => 'Cep ( * )', 'style' => 'margin-bottom: 0;', 'div' => false)); ?>
			<img src="/portal/img/default.gif" id="carregando" style="display: none;" />
			<span style="font-size: 10px;" id="pesquisa_cep">
				<a href="javascript:void(0);" onclick="multiempresa.buscaCEP('MultiEmpresaEndereco', 'MultiEmpresa');">COMPLETAR ENDEREÇO</a>
			</span>    			
			<div style="clear: both; margin-bottom: 15px;"></div>
			<?php echo $this->BForm->input('MultiEmpresaEndereco.logradouro', array('class' => 'input-xxlarge', 'label' => false, 'multiple', 'label' => 'Logradouro ( * )')); ?>
			<?php echo $this->BForm->input('MultiEmpresaEndereco.numero', array('class' => 'input-medium', 'label' => false, 'multiple', 'label' => 'Número ( * )')); ?>
			<?php echo $this->BForm->input('MultiEmpresaEndereco.complemento', array('class' => 'input-xlarge', 'label' => false, 'multiple', 'label' => 'Complemento')); ?>
			<?php echo $this->BForm->input('MultiEmpresaEndereco.bairro', array('class' => 'input-xlarge', 'label' => false, 'multiple', 'label' => 'Bairro ( * )')); ?>
			<?php echo $this->BForm->input('MultiEmpresaEndereco.codigo_estado_endereco', array('label' => false, 'class' => 'input-xxlarge uf', 'style' => 'text-transform: uppercase;', 'empty' => false, 'options' => $estados, 'onchange' => 'multiempresa.buscaCidade(this, null, "MultiEmpresaEnderecoCodigoCidadeEndereco", null)', 'label' => 'Estado ( * )')) ?>
			<label>Cidade ( * )</label>
			<span id="cidade_combo">
				<?php echo $this->BForm->input('MultiEmpresaEndereco.codigo_cidade_endereco', array('label' => false, 'class' => 'input-xxlarge', 'style' => 'text-transform: uppercase;', 'empty' => false, 'options' => $cidades)) ?>
			</span>
			<div id="carregando_cidade" style="display: none; text-align: left; border: 1px solid #CCCCCC; padding: 8px;">
				<img src="/portal/img/ajax-loader.gif" border="0"/>
			</div>
		</div>
			
		<!-- 
		<div class="well">
		  	<b>TAG para inserir no Formulário de Proposta:</b><br />
		  	
		  	<p style="background: #333; color: #FFF; padding: 10px;">
		  		<?php //echo htmlspecialchars('<input type="hidden" name="codigo_empresa" value="' . base64_encode($this->data['MultiEmpresa']['codigo']) . '">'); ?>
		  	</p>
  			PS: Inserir esta tag no formulário de cadastro de proposta, para indicar qual a empresa que esta recebendo a proposta de credenciamento
		</div>
		 -->
			 			
		<div class="form-actions">
		  	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	  		<?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
		</div>    
	</div>
	<?php echo $this->BForm->end(); ?>
	
  	<?php if($authUsuario['Usuario']['codigo_empresa']) : ?>
		<div class="tab-pane" id="personalizacao">
		    <div class="well">
				<?php echo $this->BForm->input('MultiEmpresa.cor_menu', array('label' => 'Cor Fundo do Menu:', 'class' => 'jscolor', 'onchange' => 'atualiza_cor(this);')); ?>
		    </div>
		    <div class="well" id="logomarca">
		    </div>
		</div>
  	<?php endif; ?>

</div>

<?php echo $this->Buonny->link_js('jscolor'); ?>
<?php echo $this->Buonny->link_js('multi_empresa'); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	setup_time();
    	setup_mascaras();
		atualizaListaMultiEmpresa();
    });

	function atualizaListaMultiEmpresa() {
    	var div = jQuery("div#logomarca");
        bloquearDiv(div);
        div.load(baseUrl + "multi_empresas/logomarca/" + Math.random());
	}
		
	function atualiza_cor(jscolor) {
		$(".navbar-inner").css("background", "#" + $(jscolor).val());
		
		$.ajax({
	    	type: "POST",
	    	url: "/portal/multi_empresas/atualiza_cor_menu/",
	    	dataType: "json",
	    	data: "cor=" + $(jscolor).val(),
	    	beforeSend: function() {},
	    	success: function(json) {
				
	    	},
	    	complete: function() {
		
			}
	    });
	}
		
	function enviar_logomarca() {
		var file_data = $("#MultiEmpresaLogomarca").prop("files")[0];
		var form_logomarca = document.querySelector("#MultiEmpresaLogomarcaForm");
		var form_data = new FormData(form_logomarca);
		form_data.append("file", file_data);

		var div = jQuery("div#logomarca");
		bloquearDiv(div);
	
	    $.ajax({
            url: baseUrl + "multi_empresas/logomarca/" + Math.random(),  
            dataType: "json",
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: "post",
            success: function(json){	       
	            if(json.erro){	           
	            	swal("Deu erro!", json.erro, "error");	       
	            }
	            if(json.status == true) {
	            	swal("Deu boa!", json.data.message, "success");
					atualizaListaMultiEmpresa();
					if($("#img_thumb").lenght){
						$("#img_thumb img:first").attr("src", $("img#logomarca-thumb").attr("src"));
					} else {
						console.log($("img#logomarca-thumb"));
						$("#logomarca-do-sistema").html("<a id=\"img_thumb\" class=\"brand logo\" href=\"/portal/usuarios/inicio\"><img src=\"" + $("img#logomarca-thumb").attr("src") +"\"></a>");
					}
	            }
			},
	        complete: function(data) {	   
	        	atualizaListaMultiEmpresa();
	        }
		});
	}				
', false);
?>

