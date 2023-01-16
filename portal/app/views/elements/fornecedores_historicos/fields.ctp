<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('FornecedorHistorico.codigo_fornecedor', array('value' => $codigo_fornecedor)); ?>
    <?php echo $this->BForm->input('FornecedorHistorico.observacao', array('type' => 'textarea', 'class' => 'input-xxlarge')) ?>
</div>
<div class="row-fluid inline">
    <h6>Arquivo(*)</h6>
    <?php echo $this->BForm->input('FornecedorHistorico.caminho_arquivo', array('type'=>'file', 'label' => false,'class' => 'input-xxlarge'));?>
    <?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoExame', 'class' => 'btn btn-anexos')); ?>
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

            var div = jQuery('div#modal_dialog');
            bloquearDiv(div);

			var file_data = $('#FornecedorHistoricoCaminhoArquivo').prop('files')[0];   
			var form_FornDoc = document.querySelector('#FornecedorHistoricoIncluirForm');
			var form_data = new FormData(form_FornDoc); 
			form_data.append('caminho_arquivo', file_data);

	    $.ajax({
            url: baseUrl + 'fornecedores_historicos/incluir/".$codigo_fornecedor."/' + Math.random(),  
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
	        		atualizaFornecedorHistorico();
                }
                else{
                    // $('#FornecedorHistoricoIncluirForm .help-block').remove();
                    desbloquearDiv(div);
                    console.log(div);
                    $('#FornecedorHistoricoIncluirForm').append( '<div class=\'help-block error-message\' style=\'color: #b94a48;\'>'+retorno.error+'</div>');
                }
            },
            error: function(erro){
                desbloquearDiv(div);
                console.log(div);
                $('#FornecedorHistoricoIncluirForm').append( '<div class=\'help-block error-message\' style=\'color: #b94a48;\'>'+retorno.error+'</div>');
            }
		});
    }

    function atualizaFornecedorHistorico(){
        var div = jQuery('#fornecedor-historico-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'fornecedores_historicos/lista_historico/".$codigo_fornecedor."/' + Math.random());
    }	 
");?>

<script type="text/javascript">
    $(document).ready(function() {
        setup_mascaras();
        setup_datepicker();
        setup_time();
    });

    $("#LimparArquivoExame").click(function(){
       $("#FornecedorHistoricoCaminhoArquivo").val("");                
    });

    $('.btn-primary').click(function(event) {
        if($("#FornecedorHistoricoObservacao").val() == "" || $("#FornecedorHistoricoObservacao").val() == undefined ){
            swal({
                type: "warning",
                title: "Atenção",
                text: "Favor preencha a observaçao para salvar",
            });
        }
    });
</script>

