<h3>Envio de Documentos para Análise:</h3>
<?php echo $this->BForm->create('TiposDocumentos', array('type' => 'file' ,'url' => array('action' => 'enviar'))); ?>
	<?php echo $this->BForm->hidden('codigo_proposta_credenciamento',array('value'=>$codigo_proposta_credenciamento)) ?>
	
	<?php if(($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) || ($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::PRESTADOR)) : ?>
		<?php if($obrigatorio) : ?>
			<div class="alert alert-danger">
			  <strong>Atenção!</strong> Você tem documentos pendentes para enviar. Faça o envio dos documentos abaixo para análise do credenciamento. ( * )
			  <a href="#" data-dismiss="alert" aria-label="close" title="close" class="close">×</a>
			</div>
		<?php else : ?>
			<div class="alert alert-info">
			  <strong>Atenção!</strong> Seus arquivos foram enviados e já estão em análise, em breve entraremos em contato!
			  <a href="#" data-dismiss="alert" aria-label="close" title="close" class="close">×</a>
			</div>		
		<?php endif; ?>
	<?php endif; ?>
	    
	<table class="table table-striped">
	    <thead>
	        <tr>
	        	<th>Situação</th>
	            <th>Documento</th>
	            <th colspan="2">Arquivo</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach($tipos_documentos as $key => $documento): ?>
		        <?php if($proposta['codigo_status_proposta_credenciamento'] == '9' && ($documento['TipoDocumento']['codigo_status_proposta_credenciamento'] == '9') && (($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) || ($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::PRESTADOR))) : ?> 
			    	<tr style="border: 3px solid red;">
			    <?php else : ?>
			    	<tr>
		        <?php endif; ?>
		        	<td>
		        		<?php if($proposta['codigo_status_proposta_credenciamento'] == '9' && ($documento['TipoDocumento']['codigo_status_proposta_credenciamento'] == '9') && (($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) || ($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::PRESTADOR)) && !isset($documento['PropostaCredDocumento']['caminho_arquivo'])) : ?>
		        			<div style="display: inline-block; padding: 4px 12px; background: #ee5f5b; color: #FFF; font-weight: bold; width: 60px;">pendente</div>
		        		<?php elseif(isset($documento['PropostaCredDocumento']['caminho_arquivo']) && !empty($documento['PropostaCredDocumento']['caminho_arquivo'])) : ?>
		        			<div style="display: inline-block; padding: 4px 12px; background: green; color: #FFF; font-weight: bold; width: 60px;">enviado</div>
		            	<?php elseif($proposta['codigo_status_proposta_credenciamento'] == $documento['TipoDocumento']['codigo_status_proposta_credenciamento'] && !count($documento['PropostaCredDocumento'])): ?>
		            		<?php if($documento['TipoDocumento']['obrigatorio']) : ?>
		            			<div style="display: inline-block; padding: 4px 12px; background: #ee5f5b; color: #FFF; font-weight: bold; width: 60px;">pendente</div>
		            		<?php else : ?>
		            			<div style="display: inline-block; padding: 4px 12px; background: #FFF; color: #000; font-weight: bold; width: 60px;">pendente</div>
		            		<?php endif; ?>
		            	<?php else : ?>
		            		<div style="display: inline-block; padding: 4px 12px; background: #FFF; color: #000; font-weight: bold; width: 60px;">pendente</div>
		            	<?php endif; ?>
		        	</td>
		            <td style="width: 800px;">
		            	<?php echo $documento['TipoDocumento']['descricao'] ?><?php echo ($documento['TipoDocumento']['obrigatorio']) ? ' *' : ''; ?>
		            </td>
		            <td id="arquivo_<?php echo $documento['TipoDocumento']['codigo']; ?>">
		            	<?php if(count($documento['PropostaCredDocumento'])) : ?>
		            		<?php echo $this->Html->link($documento['PropostaCredDocumento']['caminho_arquivo'], '/files/documentacao/' . $codigo_proposta_credenciamento . '/' . $documento['PropostaCredDocumento']['caminho_arquivo'], array('target'=>'_blank')); ?>
		            	<?php else : ?>
		            		<?php echo $this->BForm->input('filename_' . $documento['TipoDocumento']['codigo'], array('type'=>'file', 'label' => false)); ?>
		            	<?php endif; ?>
		            </td>
		            <td id="op_<?php echo $documento['TipoDocumento']['codigo']; ?>" style="width: 250px;">
			            <?php if(count($documento['PropostaCredDocumento'])) : ?>
							<?php if(empty($documento['PropostaCredDocumento']['validado'])) : ?>
		            			<?php if(($authUsuario['Usuario']['codigo_uperfil'] != Uperfil::CREDENCIANDO) && ($authUsuario['Usuario']['codigo_uperfil'] != Uperfil::PRESTADOR)) : ?>
			            				<a href="javascript:void(0);" style="float: right;" class="label label-info" onclick="manipula_modal('modal_validade_<?php echo $documento["TipoDocumento"]["codigo"]; ?>', 1);"><i class="icon-white icon-check"></i>Aprovar o Arquivo</a>
		            			<?php else : ?>
			            			<?php if(is_null($documento['PropostaCredDocumento']['validado']) || $documento['PropostaCredDocumento']['validado'] == '0') : ?>
					            		<a href="javascript:void(0);" style="float: right;" class="label label-important" onclick="remove_doc(<?php echo $codigo_proposta_credenciamento; ?>, <?php echo $documento["TipoDocumento"]["codigo"]; ?>, this);"><i class="icon-white icon-random"></i> Quero Reenviar</a>
					            	<?php endif; ?>		            			
		            			<?php endif; ?>
			            	<?php else : ?>			            		
			            		<?php if(($authUsuario['Usuario']['codigo_uperfil'] != Uperfil::CREDENCIANDO) && ($authUsuario['Usuario']['codigo_uperfil'] != Uperfil::PRESTADOR)) : ?>
			            			<a href="javascript:void(0);" style="float: left; cursor:default;" class="label label-success"><i class="icon-white icon-ok-sign"></i> Validado</a>
			            			<a href="javascript:void(0);" style="float: right;" class="label label-important" onclick="desfazer_aprovar(<?php echo $codigo_proposta_credenciamento; ?>, <?php echo $documento["TipoDocumento"]["codigo"]; ?>, this);"><i class="icon-white icon-fast-backward"></i> Desfazer</a>
			            		<?php else : ?>
			            			<a href="javascript:void(0);" style="float: right; cursor:default;" class="label label-success"><i class="icon-white icon-ok-sign"></i> Validado</a>
			            		<?php endif; ?>
		            		<?php endif; ?>
		            	<?php endif; ?>
		            </td>
		        </tr>
	        <?php endforeach; ?>
	    </tbody>
	</table>   
	<?php foreach($tipos_documentos as $key => $documento): ?>
		<div class="modal fade" id="modal_validade_<?php echo $documento["TipoDocumento"]["codigo"]; ?>">
			<div class="modal-dialog modal-sm">
		    	<div class="modal-content">
		    		<div class="modal-header">
		    			<?php  if(
	    					$documento["TipoDocumento"]["codigo"] == 32 OR
	    					$documento["TipoDocumento"]["codigo"] == 30 OR
	    					$documento["TipoDocumento"]["codigo"] == 29 OR
	    					$documento["TipoDocumento"]["codigo"] == 36 OR
	    					$documento["TipoDocumento"]["codigo"] == 42
		           		): ?>
		    			<h4 class="modal-title" id="gridSystemModalLabel">Informe a Data de Validade do Documento?</h4>
		    			<?php else : ?>
		    				<h4 class="modal-title" id="gridSystemModalLabel">Aprovar o Arquivo</h4>
		    			<?php endif; ?>	
		    		</div>
		    		<div class="modal-body right" >
		    			<?php  if(
	    					$documento["TipoDocumento"]["codigo"] == 32 OR
	    					$documento["TipoDocumento"]["codigo"] == 30 OR
	    					$documento["TipoDocumento"]["codigo"] == 29 OR
	    					$documento["TipoDocumento"]["codigo"] == 36 OR
	    					$documento["TipoDocumento"]["codigo"] == 42
		           		): ?>
	            		<div style="text-align: left">
	            			<input type="text" class="form-control datepicker data date" id="data_validade_<?php echo $documento["TipoDocumento"]["codigo"]; ?>" style="width: 100px;" onchange="$(this).css('border', '1px solid #CCC');"/>
	            		</div>
	            		(Informe a data de validade do documento, ao vencer o sistema cobrará automaticamente o mesmo atualizado.)<br />
	            		<?php endif; ?>	
	            		<div id="carregando_<?php echo $documento["TipoDocumento"]["codigo"]; ?>" style="display: none;">
	            			<img src="/portal/img/ajax-loader.gif"/>
	            		</div>
		    		</div>
		    		<div class="modal-footer">
		    			<a href="javascript:void(0);" class="btn btn-danger" onclick="manipula_modal('modal_validade_<?php echo $documento["TipoDocumento"]["codigo"]; ?>', 0);">X</a>
		    			<?php  if(
	    					$documento["TipoDocumento"]["codigo"] == 32 OR
	    					$documento["TipoDocumento"]["codigo"] == 30 OR
	    					$documento["TipoDocumento"]["codigo"] == 29 OR
	    					$documento["TipoDocumento"]["codigo"] == 36 OR
	    					$documento["TipoDocumento"]["codigo"] == 42
		           		): ?>
		    				<a href="javascript:void(0);" class="btn btn-success" onclick="aprova_arquivo(<?php echo $codigo_proposta_credenciamento; ?>, <?php echo $documento["TipoDocumento"]["codigo"]; ?>, this);"><i class="icon-white icon-thumbs-up"></i> Aprovar Arquivo!</a>
		    			<?php else : ?>
		    				<a href="javascript:void(0);" class="btn btn-success" onclick="aprova_arquivo_sem_data_validade(<?php echo $codigo_proposta_credenciamento; ?>, <?php echo $documento["TipoDocumento"]["codigo"]; ?>, this);"><i class="icon-white icon-thumbs-up"></i> Aprovar Arquivo!</a>
		    			<?php endif; ?>
		    		</div>
		    	</div>
		  	</div>
		</div>
	<?php endforeach; ?>
	
	<?php if($falta_arquivo) : ?>
		<div class="row" style="margin-left:0;">
			<div class="form-actions right">
				<button class="btn btn-success" title="Enviar para Análise">
					Enviar Documentos
				</button>
				(Enviar toda a documentação solicitada, lembre-se os documentos com * são obrigatorios!)
			</div>
		</div>
	<?php endif; ?>
<?php echo $this->BForm->end(); ?>		 
<div id="carregando" style="display:none; text-align: center;">
	<img src="/portal/img/ajax-loader-medio.gif"/>
</div>
		
<div style="clear:both;"></div>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setup_datepicker();
		verifica_documentos_aprovados(\''.$codigo_proposta_credenciamento.'\');
		verifica_proposta_aprovada(\''.$codigo_proposta_credenciamento.'\');
	});
	
	function remove_doc(codigoProposta, codigoDocumento, elemento) {
	    $.ajax({
	        type: "POST",
	        url: "/portal/tipos_documentos/remove_arquivo",
	        dataType: "json",
	        data: "codigo_proposta=" + codigoProposta + "&codigo_documento=" + codigoDocumento,
	        beforeSend: function() {
	        	$("#op_" + codigoDocumento).html($("#carregando").clone().show());
	        },
	        success: function(json) {
	        	if(json == "1")
	        		$("#arquivo_" + codigoDocumento).html("<input id=\"TiposDocumentosFilename"+codigoDocumento+"\" type=\"file\" name=\"data[TiposDocumentos][filename_" + codigoDocumento + "]\">");
	        },
	        complete: function() {
	        	$("#op_" + codigoDocumento).html("");
		
				if(codigoDocumento == 39) {
					location.reload();
				}		
	        }
	    });
		
	}
	
	function aprova_arquivo(codigoProposta, codigoDocumento, elemento) {
		console.log("com data de validade");
		if($("#data_validade_" + codigoDocumento).val() != "") {
		    $.ajax({
		        type: "POST",
		        url: "/portal/tipos_documentos/aprova_arquivo",
		        dataType: "json",
		        data: "codigo_proposta=" + codigoProposta + "&codigo_documento=" + codigoDocumento + "&data_validade=" + $("#data_validade_" + codigoDocumento).val(),
		        beforeSend: function() { 
		        	$("#carregando_" + codigoDocumento).show();
		        },
		        success: function(json) {
		        	if(json == "1")
		       			$("#op_" + codigoDocumento).html("<a class=\"label label-success\" style=\"float: left; cursor:default;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-ok-sign\"></i> Validado</a><a class=\"label label-important\" onclick=\"desfazer_aprovar("+codigoProposta+", " + codigoDocumento + ", this);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-fast-backward\"></i> Desfazer</a>");
		       		else 
		       			$("#op_" + codigoDocumento).html("<a class=\"label label-info\" onclick=\"aprova_arquivo("+codigoProposta+", " + codigoDocumento + ", this);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-check\"></i>Aprovar o Arquivo</a>");
	
		       			
		       		verifica_proposta_aprovada(codigoProposta);
		       		verifica_documentos_aprovados(codigoProposta);
		       		manipula_modal("modal_validade_" + codigoDocumento, 0);
		        },
		        complete: function() {
		        	$("#carregando_" + codigoDocumento).hide(); 
		        }
		    });				
		} else {
			$("#data_validade_" + codigoDocumento).css("border", "2px solid red");
		}	
	}
	function aprova_arquivo_sem_data_validade(codigoProposta, codigoDocumento, elemento) {
		console.log("sem data de validade");
		// if($("#data_validade_" + codigoDocumento).val() != "") {
		    $.ajax({
		        type: "POST",
		        url: "/portal/tipos_documentos/aprova_arquivo",
		        dataType: "json",
		        data: "codigo_proposta=" + codigoProposta + "&codigo_documento=" + codigoDocumento,
		        beforeSend: function() { 
		        	$("#carregando_" + codigoDocumento).show();
		        },
		        success: function(json) {
		        	if(json == "1")
		       			$("#op_" + codigoDocumento).html("<a class=\"label label-success\" style=\"float: left; cursor:default;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-ok-sign\"></i> Validado</a><a class=\"label label-important\" onclick=\"desfazer_aprovar("+codigoProposta+", " + codigoDocumento + ", this);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-fast-backward\"></i> Desfazer</a>");
		       		else 
		       			$("#op_" + codigoDocumento).html("<a class=\"label label-info\" onclick=\"aprova_arquivo("+codigoProposta+", " + codigoDocumento + ", this);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-check\"></i>Aprovar o Arquivo</a>");
	
		       			
		       		verifica_proposta_aprovada(codigoProposta);
		       		verifica_documentos_aprovados(codigoProposta);
		       		manipula_modal("modal_validade_" + codigoDocumento, 0);
		        },
		        complete: function() {
		        	$("#carregando_" + codigoDocumento).hide(); 
		        }
		    });				
		// } else {
		// 	$("#data_validade_" + codigoDocumento).css("border", "2px solid red");
		// }	
	}
	
	function verifica_documentos_aprovados(proposta) {
	
	    $.ajax({
	        type: "POST",
	        url: "/portal/tipos_documentos/verifica_documentos_aprovados",
	        dataType: "json",
	        data: "codigo_proposta=" + proposta,
	        beforeSend: function() { },
	        success: function(json) {	        
	        	if(json == "1") {
	       			$("#solicita_contrato").show();
	        	} else {
	       			$("#solicita_contrato").hide();
	        	} 
	        },
	        complete: function() { }
	    });
	}
	
	function verifica_proposta_aprovada(proposta) {
	    $.ajax({
	        type: "POST",
	        url: "/portal/tipos_documentos/verifica_proposta_aprovada",
	        dataType: "json",
	        data: "codigo_proposta=" + proposta,
	        beforeSend: function() { },
	        success: function(json) {
	        	if(json == "1") {
	        		$("#solicita_documento").show();
	        	} else {
	        		$("#solicita_documento").hide();
	        	}
	        },
	        complete: function() { }
	    });
	}	
	
	function desfazer_aprovar(codigoProposta, codigoDocumento, elemento) {
		console.log("entrei");
	    $.ajax({
	        type: "POST",
	        url: "/portal/tipos_documentos/desfazer_aprovar",
	        dataType: "json",
	        data: "codigo_proposta=" + codigoProposta + "&codigo_documento=" + codigoDocumento,
	        beforeSend: function() { 
	        	$(elemento).parents("td").html( $("#carregando").clone().show() );
	        },
	        success: function(json) {
	        	if(json == "1")
	       			$("#op_" + codigoDocumento).html("<a class=\"label label-info\" onclick=\"manipula_modal(\'modal_validade_"+codigoDocumento+"\', 1);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-check\"></i>Aprovar o Arquivo</a>");
	       		else 
	       			$("#op_" + codigoDocumento).html("<a class=\"label label-success\" style=\"float: left; cursor:default;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-ok-sign\"></i> Validado</a><a class=\"label label-important\" onclick=\"desfazer_aprovar("+codigoProposta+", " + codigoDocumento + ", this);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-fast-backward\"></i> Desfazer</a>");
	       		
	       		verifica_documentos_aprovados(codigoProposta);
	        },
	        complete: function() { }
	    });		
	}
'); ?>
