<?php
$url_current = (isset($url_retorno) && !empty($url_retorno)) ? $url_retorno :$this->Html->url( str_replace('/portal', '', $this->here), false ); 
echo $this->BForm->create('Fotos', array('type' => 'file' ,'url' => array('controller'=>'fotos_fornecedor', 'action' => 'enviar'))); ?>
	<?php echo $this->BForm->hidden('codigo_fornecedor', array('value'=>$codigo_fornecedor)) ?>
	
	<?php echo $this->BForm->hidden('url_redirect', array('value'=>$url_current)) ?>
	
	<h3>Envio de Fotos do Estabelecimento</h3>
	
	<?php 
		if(empty($codigo_fornecedor)){
			return;
		}
		if($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) : ?>
		<div class="alert alert-info">
		  <strong>Atenção!</strong> Enviar as fotos do Estabelecimento (Fachada, Recepção, Salas, Consultórios, etc...)
		  <a href="#" data-dismiss="alert" aria-label="close" title="close" class="close">×</a>
		</div>		
	<?php endif; ?>
	<?php if(isset($url_retorno) && !empty($url_retorno)) : ?>
	<div class='actionbar-right' style="margin:10px;">
        <?php echo $this->Html->link('Voltar',$url_retorno, 
            array('escape' => false, 'class' => 'btn', 'title' =>'Voltar'));
        ?>
	</div>
	<?php endif; ?>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th style="width: 35%;">Descrição</th>
	            <th style="width: 65%;" colspan="2">Foto</th>
	        </tr>
	    </thead>
	    <tbody id="campos">
	    	<?php if(count($lista_enviadas)) : ?>
		        <?php foreach($lista_enviadas as $key => $foto): ?>
			        <tr id="arquivo_<?php echo $foto['FornecedorFoto']["codigo"]; ?>">
			            <td><?php echo $foto['FornecedorFoto']['descricao'] ?></td>
			            <td>
						<div style="width:80%;height:80%; border:1px solid #ccc; padding:5px;">
						<?php if($foto['FornecedorFoto']['caminho_arquivo'] && !empty($foto['FornecedorFoto']['caminho_arquivo'])) : ?>
							<img src="https://api.rhhealth.com.br<?php echo $foto['FornecedorFoto']['caminho_arquivo']; ?>" />
						<?php else: ?>		
							<p style="margin:30px">Imagem não encontrada.</p>
						<?php endif; ?>
						</div>
		            		<?php //php echo $this->Html->link($foto['FornecedorFoto']['caminho_arquivo'], '/files/fotos/' . $codigo_fornecedor . '/' . $foto['FornecedorFoto']['caminho_arquivo'], array('target'=>'_blank')); ?>
			            </td>
			            <td style="width: 200px;" id="op_<?php echo $foto['FornecedorFoto']["codigo"]; ?>">
		            		<a href="javascript:void(0);" style="float: right;" class="label label-important" onclick="remove_foto(<?php echo $foto['FornecedorFoto']["codigo"]; ?>, this);"><i class="icon-white icon-trash"></i> Remover</a>
			            </td>
			        </tr>
		        <?php endforeach; ?>    	
	    	<?php else : ?>
		        <tr id="arquivo_1">
		            <td><?php echo $this->BForm->input('Fotos.1.descricao', array('id' => 'text_1', 'class' => 'form-control', 'placeholder' => 'Digite uma Descrição', 'onchange' => 'enviarAutomatico("text", this);', 'label' => false, 'style' => 'float: left; width: 220px;', 'multiple')); ?></td>
		            <td>
		            	<?php echo $this->BForm->input('Fotos.1.caminho_arquivo', array('id' => 'file_1', 'type'=>'file', 'onchange' => 'enviarAutomatico("file", this);', 'label' => false)); ?>
		            </td>
		            <td id="op_0">
		            </td>
		        </tr>	    	
	    	<?php endif; ?>
	    </tbody>
	</table>
	
	<a href="javascript:void(0);" onclick="addCampo();" class="btn btn-info btn-sm right" id="mais_foto">
		<i class="icon-plus icon-white"></i> Incluir Mais Foto
	</a>	
	
<?php echo $this->BForm->end(); ?>
<div id="carregando" style="display:none; text-align: center;">
	<img src="/portal/img/ajax-loader.gif"/>
</div>
	
<div id="modelos">
	<div id="modelo_campo" style="display: none;">
		<table>
	        <tr id="arquivo_X">
	            <td><?php echo $this->BForm->input('Fotos.X.descricao', array('id' => 'text_X', 'class' => 'form-control', 'placeholder' => 'Digite uma Descrição', 'onchange' => 'enviarAutomatico("text", this);', 'label' => false, 'style' => 'float: left; width: 220px;', 'multiple')); ?></td>
	            <td>
	            	<?php echo $this->BForm->input('Fotos.X.caminho_arquivo', array('id' => 'file_X', 'type' => 'file', 'label' => false, 'onchange' => 'enviarAutomatico("file", this);')); ?>
	            </td>
	            <td id="op_X">
	            </td>
	        </tr>
        </table>
	</div>
</div>
<div style="clear:both;"></div>
<?php echo $this->Javascript->codeBlock('
	function enviarAutomatico(campo, elemento) {
		var key = $(elemento).attr("id").replace(/[^0-9]/g,"");
		
		if(campo == "text") {
			if($("input[name=\"data[Fotos][" + key + "][caminho_arquivo]\"]").val() != "") {
				$("#mais_foto").hide();
				$("input[name=\"data[Fotos][" + key + "][caminho_arquivo]\"]").hide();
				$("input[name=\"data[Fotos][" + key + "][caminho_arquivo]\"]").parent().append($("#carregando").html());
				$("#FotosListagemForm").submit();
			}
		} else {
			if($("input[name=\"data[Fotos][" + key + "][descricao]\"]").val() != "") {
				$("#mais_foto").hide();
				$("input[name=\"data[Fotos][" + key + "][caminho_arquivo]\"]").hide();
				$("input[name=\"data[Fotos][" + key + "][caminho_arquivo]\"]").parent().append($("#carregando").html());
				$("#FotosListagemForm").submit();
			} else {
				$("input[name=\"data[Fotos][" + key + "][descricao]\"]").css("border", "2px solid #953B39");
			}
		}
	}
	
	function addCampo() {
		var key = parseInt($("tbody#campos tr").last().attr("id").replace(/[^0-9]/g,"")) + 1;
		
		$("#modelos #modelo_campo tr").clone().appendTo("tbody#campos").show().find("input, file").each(function(index, element){
			$(element).attr("name", $(element).attr("name").replace("X", key));
	    });	
	    
	    $("#text_X").attr("id", "text_" + key);
	    $("#file_X").attr("id", "file_" + key);
	    $("#arquivo_X").attr("id", "arquivo_" + key);
	    $("#op_X").attr("id", "op_" + key);
	}
	function remove_foto(codigo, elemento) {
	    $.ajax({
	        type: "POST",
	        url: "/portal/fotos_fornecedor/remove_arquivo",
	        dataType: "json",
	        data: "codigo=" + codigo,
	        beforeSend: function() {
	        	$("#op_" + codigo).html($("#carregando").clone().css("float", "right").show());
	        },
	        success: function(json) {
	        	if(json == "1")
	        		$("#arquivo_" + codigo).remove();
	        },
	        complete: function() {
	        	$("#arquivo_" + codigo).remove();
	        }
	    });			
	}
'); ?>