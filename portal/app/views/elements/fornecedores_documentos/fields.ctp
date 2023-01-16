<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('FornecedorDocumento.codigo_fornecedor', array('value' => $codigo_fornecedor)); ?>
    <?php echo $this->BForm->input('FornecedorDocumento.codigo_tipo_documento', array('label' => 'Documento (*)', 'class' => 'input-xlarge', 'options' => $tipo_documento, 'empty' => 'Selecione' ,'default' => ''));?>
    
    <?php echo $this->BForm->input('FornecedorDocumento.data_validade', array('type' => 'text','label' => 'Data de Validade (*)', 'class' => 'data input-medium'));?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('FornecedorDocumento.caminho_arquivo', array('type'=>'file', 'label' => 'Arquivo (*)','class' => 'input-xxlarge'));?>
</div>

<div class='form-actions'>
	 <?php echo $html->link('Salvar', 'javascript:void(0)', array('id'=>'envia_documento' , 'div' => false, 'class' => 'btn btn-primary', 'style' => 'color:#FFF',  'onclick' => 'insere()')); ?>
	 <?php echo $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div> 
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
	    setup_time();
	    setup_mascaras();
	    setup_datepicker();
	});

	function insere(){

			var file_data = $('#FornecedorDocumentoCaminhoArquivo').prop('files')[0];   
			var form_FornDoc = document.querySelector('#FornecedorDocumentoIncluirForm');
			var form_data = new FormData(form_FornDoc); 
			form_data.append('caminho_arquivo', file_data);

	    $.ajax({
            url: baseUrl + 'fornecedores_documentos/incluir/".$codigo_fornecedor."/' + Math.random(),  
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            success: function(retorno){
            	console.log(retorno);
                if(retorno == 1){
                	close_dialog();
	        		atualizaFornecedorDocumentosEnviado();
	                atualizaFornecedorDocumentosPendente();
                }
                else{
                    $('#FornecedorDocumentoIncluirForm .help-block').remove();
                    $.each(retorno, function (index, value) {
                        
                        if(index== 'codigo_tipo_documento')
                        {
                            $('select[name=\"data[FornecedorDocumento]['+index+']\"]').parent().append( '<div class=\'help-block error-message\' style=\'color: #b94a48;\'>'+value+'</div>');
                        }
                        else{
				  		    $('input[name=\"data[FornecedorDocumento]['+index+']\"]').parent().append( '<div class=\'help-block error-message\' style=\'color: #b94a48;\'>'+value+'</div>');
                        }
					});
                }
            },
            error: function(erro){
            	console.log(erro);
                $('#FornecedorDocumentoIncluirForm').append( '<div class=\'help-block error-message\' style=\'color: #b94a48;\'>Erro ao inserir! Verifique o Documento!</div>');
            }
		});
    }


    function atualizaFornecedorDocumentosEnviado(){
        var div = jQuery('#fornecedor-documentos-enviados');
        bloquearDiv(div);
        div.load(baseUrl + 'fornecedores_documentos/listagem_documentos_enviados/".$codigo_fornecedor."/' + Math.random());
    }

    function atualizaFornecedorDocumentosPendente(){
        var div = jQuery('#fornecedor-documentos-pendentes');
        bloquearDiv(div);
        div.load(baseUrl + 'fornecedores_documentos/listagem_documentos_pendentes/".$codigo_fornecedor."/' + Math.random());
    }
	 
");?>

