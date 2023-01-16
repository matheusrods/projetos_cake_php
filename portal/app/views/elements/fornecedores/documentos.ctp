<h3>Documentos</h3>

<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'fornecedores_documentos', 'action' => 'incluir', $codigo_fornecedor), array('escape' => false, 'class' => 'btn btn-success dialog_documentos', 'title' =>'Cadastrar Novos Documentos'));?>
</div>
<div id="fornecedor-documentos-enviados" class="fieldset" style="display: block;">
    
</div>
<div id="fornecedor-documentos-pendentes" class="fieldset" style="display: block;">
    
</div>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaFornecedorDocumentosEnviado();
        atualizaFornecedorDocumentosPendente();

    });

    function excluirFornecedorDocumento(codigo){
        if (confirm('Deseja realmente excluir ?')){
            $.ajax({
                type: 'POST',        
                url: baseUrl + 'fornecedores_documentos/excluir/' + codigo +  '/' + Math.random(),        
                dataType : 'json',
                success : function(data){ 
                    atualizaFornecedorDocumentosEnviado(); 
                    atualizaFornecedorDocumentosPendente();
                },
                error : function(error){
                    console.log(error);
                }
            }); 
        }
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

")
?>