<style>
	legend {font-size: 13px; margin-bottom: 0;}
	.control-group {padding:0; margin: 0}
	.nav-tabs > .active { background: 3E75A5; }
</style>

<ul class="nav nav-tabs">
	<li class="<?php echo (($aba == 'documentacao') || is_null($aba)) ? 'active' : '' ?>"><a href="#documentacao" data-toggle="tab">Documentação Solicitada</a></li>
	<?php if($tem_contra_proposta && (($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) || ($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::PRESTADOR))) : ?>
		<li class="<?php echo (($aba == 'valores_exames') || is_null($aba)) ? 'active' : '' ?>"><a href="#valores_exames" data-toggle="tab">Valores e Exames</a></li>
	<?php endif; ?>
	<li class="<?php echo (($aba == 'fotos') || is_null($aba)) ? 'active' : '' ?>"><a href="#fotos" data-toggle="tab">Envio de Fotos</a></li>
</ul>
		
<div class="tab-content">
    <div class="tab-pane <?php echo (($aba == 'documentacao') || is_null($aba)) ? 'active' : '' ?>" id="documentacao">
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
			            <th>Documento</th>
			            <th colspan="2">Arquivo</th>
			        </tr>
			    </thead>
			    <tbody>
			        <?php foreach($tipos_documentos as $key => $documento): ?>
				        <tr>
				            <td><?php echo $documento['TipoDocumento']['descricao'] ?><?php echo ($documento['TipoDocumento']['obrigatorio']) ? ' *' : ''; ?></td>
				            <td id="arquivo_<?php echo $documento['TipoDocumento']['codigo']; ?>">
				            	<?php if(count($documento['PropostaCredDocumento'])) : ?>
				            		<?php echo $this->Html->link($documento['PropostaCredDocumento']['caminho_arquivo'], '/files/documentacao/' . $codigo_proposta_credenciamento . '/' . $documento['PropostaCredDocumento']['caminho_arquivo'], array('target'=>'_blank')); ?>
				            	<?php else : ?>
				            		<?php echo $this->BForm->input('filename_' . $documento['TipoDocumento']['codigo'], array('type'=>'file', 'label' => false)); ?>
				            	<?php endif; ?>
				            </td>
				            <td id="op_<?php echo $documento['TipoDocumento']['codigo']; ?>" style="width: 200px;">
					            <?php if(count($documento['PropostaCredDocumento'])) : ?>
									<?php if(is_null($documento['PropostaCredDocumento']['data_validade'])) : ?>
				            			<?php if(($authUsuario['Usuario']['codigo_uperfil'] != Uperfil::CREDENCIANDO) && ($authUsuario['Usuario']['codigo_uperfil'] != Uperfil::PRESTADOR) ) : ?>
					            			<a href="javascript:void(0);" style="float: right;" class="label label-info" onclick="aprova_arquivo(<?php echo $codigo_proposta_credenciamento; ?>, <?php echo $documento["TipoDocumento"]["codigo"]; ?>, this);"><i class="icon-white icon-check"></i>Aprovar o Arquivo</a>
				            			<?php else : ?>
					            			<?php if(($proposta['codigo_status_proposta_credenciamento'] == '2') || ($proposta['codigo_status_proposta_credenciamento'] == '1')) : ?>
							            		<a href="javascript:void(0);" style="float: right;" class="label label-important" onclick="remove_arquivo(<?php echo $codigo_proposta_credenciamento; ?>, <?php echo $documento["TipoDocumento"]["codigo"]; ?>, this);"><i class="icon-white icon-random"></i> Quero Reenviar</a>
							            	<?php endif; ?>		            			
				            			<?php endif; ?>
					            	<?php else : ?>
					            		
					            		<?php if(($authUsuario['Usuario']['codigo_uperfil'] != Uperfil::CREDENCIANDO) && ($authUsuario['Usuario']['codigo_uperfil'] != Uperfil::PRESTADOR) ) : ?>
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
		
		
			<?php if($falta_arquivo) : ?>
				<div class="row" style="margin-left:0;">
					<div class="form-actions right">
						<button class="btn btn-success" title="Enviar para Análise">
							<i class="icon-plus icon-white"></i>
							Enviar para Análise
						</button>
					</div>
				</div>
			<?php endif; ?>
		<?php echo $this->BForm->end(); ?>		 
    </div>
    <?php if($tem_contra_proposta && (($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) || ($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::PRESTADOR))) : ?>
    	<div class="tab-pane <?php echo (($aba == 'valores_exames') || is_null($aba)) ? 'active' : '' ?>" id="valores_exames">
			<?php echo $this->requestAction('/propostas_credenciamento/contraproposta/' . $codigo_proposta_credenciamento, array('return')); ?>
	    </div>
	<?php endif; ?>
	
    <div class="tab-pane <?php echo (($aba == 'fotos') || is_null($aba)) ? 'active' : '' ?>" id="fotos">
	    <?php echo $this->requestAction('fotos/listagem', array('return')); ?>
    </div>
</div>

<div id="carregando" style="display:none; text-align: center;">
	<img src="/portal/img/ajax-loader-medio.gif"/>
</div>
		
<div style="clear:both;"></div>

<?php echo $this->Javascript->codeBlock('
	function remove_arquivo(codigoProposta, codigoDocumento, elemento) {
	
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
	        }
	    });			
	}
	
	function aprova_arquivo(codigoProposta, codigoDocumento, elemento) {
	    $.ajax({
	        type: "POST",
	        url: "/portal/tipos_documentos/aprova_arquivo",
	        dataType: "json",
	        data: "codigo_proposta=" + codigoProposta + "&codigo_documento=" + codigoDocumento,
	        beforeSend: function() { 
	        	$(elemento).parents("td").html( $("#carregando").clone().show() );
	        },
	        success: function(json) {
	        	if(json == "1")
	       			$("#op_" + codigoDocumento).html("<a class=\"label label-success\" style=\"float: left; cursor:default;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-ok-sign\"></i> Validado</a><a class=\"label label-important\" onclick=\"desfazer_aprovar("+codigoProposta+", " + codigoDocumento + ", this);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-fast-backward\"></i> Desfazer</a>");
	       		else 
	       			$("#op_" + codigoDocumento).html("<a class=\"label label-info\" onclick=\"aprova_arquivo("+codigoProposta+", " + codigoDocumento + ", this);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-check\"></i>Aprovar o Arquivo</a>");
	        },
	        complete: function() { }
	    });			
	}	
	
	function desfazer_aprovar(codigoProposta, codigoDocumento, elemento) {
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
	       			$("#op_" + codigoDocumento).html("<a class=\"label label-info\" onclick=\"aprova_arquivo("+codigoProposta+", " + codigoDocumento + ", this);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-check\"></i>Aprovar o Arquivo</a>");
	       		else 
	       			$("#op_" + codigoDocumento).html("<a class=\"label label-success\" style=\"float: left; cursor:default;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-ok-sign\"></i> Validado</a><a class=\"label label-important\" onclick=\"desfazer_aprovar("+codigoProposta+", " + codigoDocumento + ", this);\" style=\"float: right;\" href=\"javascript:void(0);\"><i class=\"icon-white icon-fast-backward\"></i> Desfazer</a>");
	        },
	        complete: function() { }
	    });		
	}
'); ?>