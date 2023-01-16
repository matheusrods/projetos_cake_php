<?php echo $this->BForm->create('PreFaturamento',array('url' => array('controller' => 'pre_faturamento', 'action' => 'modal_analise', $codigo), 'enctype' => 'multipart/form-data')); ?>
<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content">
		<div class="modal-header" style="text-align: center;">
			<h3>Análise</h3>
		</div>

		<div class="modal-body" id="modal-body" style="min-height: 150px;">

			<span style="font-size: 1.2em">
                <b>Status:</b>
                <?php
                echo $this->BForm->input('status', 
                array(
                    'type' => 'radio', 
                    'options' => $status, 
                    'legend' => false, 
                    'label' => array('class' => 'radio inline input-xsmall')
                )
                );
                ?>                
            </span>
            
            <div>
                <span style="font-size: 1.2em">
                    <b>Descritivo:</b>
                    <?php echo $this->BForm->textarea('analise_descritivo', array('style' => 'min-height:150px; min-width:450px')) ?>
                </span>
            </div>
            <br />

            <hr>

            <b>Anexar evidência:</b>
            <div class='row-fluid inline'>
            
    			<?php echo $this->BForm->input('PreFaturamentoAnexos.anexo', array('type'=>'file', 'label' => false)); ?>
    			<?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoExame', 'class' => 'btn btn-anexos')); ?>

    			<?php 
                /*$arquivo = '';
                $arquivo = end(glob(DIR_ANEXOS.$codigo.DS.'anexo_evidencia_'.$codigo.'.*')); 
                
                $arquivo_app = '';
                
                if(strstr($pedido['AnexoExame']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
                    $arquivo_app = $pedido['AnexoExame']['caminho_arquivo'];
                }
                ?>

                <?php if(!empty($arquivo_app)): ?>

                    <?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $arquivo_app, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) ?>

                    <?php if( in_array( $_SESSION['Auth']['Usuario']['codigo_uperfil'], $permissoes_acoes['deletar_anexo'][0] ) ): ?>
                        <?php echo $this->Html->link('','javascript:void(0)', array('onclick' => "excluir_anexo_exame_file_server('{$codigo}')", 'class' => 'icon-trash btn-anexos lixeira_anexo', 'title' => 'Excluir anexo do item')); ?>
                    <?php endif; ?>

                <?php else: ?>

        			<?php if(!empty($arquivo)): ?>

        				<?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), '/files/anexos_exames/'.$codigo.'/'.basename($arquivo), array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) ?>

                        <?php if( in_array( $_SESSION['Auth']['Usuario']['codigo_uperfil'], $permissoes_acoes['deletar_anexo'][0] ) ): ?>
        				    <?php echo $this->Html->link('','javascript:void(0)', array('onclick' => "excluir_anexo('{$codigo}')", 'class' => 'icon-trash btn-anexos lixeira_anexo', 'title' => 'Excluir anexo do item')); ?>
                        <?php endif; ?>
                    <?php endif; ?>

    			<?php endif; ?>
                */
                ?>
			</div>

			<div class="modal-footer">
	    		<div class="right">
					<a href="javascript:void(0);" onclick="alterar(<?php echo $codigo;?>, 'Não Aprovado');" class="btn btn-danger">FECHAR</a>
    				<!--<a href="javascript:void(0);" onclick="salvar(<?php echo $codigo;?>);" class="btn btn-success">SALVAR</a>-->			
                    <button id="salvar" type="submit" class="btn btn-success">SALVAR</button>
                </div>
			</div>

		</div>
	</div>
</div>
<?php echo $this->BForm->end(); ?>
<?php
    echo $this->Javascript->codeBlock('
        
        alterar("'.$codigo.'", "Em Análise");

        function alterar(codigo, status) {
            $.ajax({
                type: "POST",
                url: baseUrl + "pre_faturamento/alterar",
                //datatype : "json",
                data : {
                    codigo : codigo,
                    status : status
                },               
                success: function( data ){
                    if(status == "Não Aprovado"){
                        document.location.reload();
                    }
                }
            });
        }
       
    /*
        $("#LimparArquivoExame").click(function(){
        	$("#ItemPedidoExameAnexoExame").val("");                
        });

        function salvar(codigo) {
        	if( $("#ItemPedidoExameAnexoExame").val() == "" || $("#ItemPedidoExameAnexoExame").val() == undefined ){
        		swal({
					type: "warning",
					title: "Atenção",
					text: "Favor selecione um arquivo para salvar",
				});
        	} else {
                var div = jQuery("#modal-body");
                bloquearDiv(div);
        		$("#ItemPedidoExameModalAnexoExamesForm").submit();
        	}
        }        

        function excluir_anexo(codigo_item_pedido){
            var div = jQuery("#modal-body");
            bloquearDiv(div);
            $.ajax({
                type: "POST",
                url: "/portal/consultas_agendas/excluir_anexo_exame",
                datatype : "json",
                data : {
                    codigo_item_pedido : codigo_item_pedido,
                },               
                success: function( data ){
                    if( data == 1 ){          
                        $(".visualiza_anexo").css("display", "none");
                        $(".lixeira_anexo").css("display", "none");
                    }
                    document.location.reload();
                }
            });
        }

        function excluir_anexo_exame_file_server(codigo_item_pedido){
            var div = jQuery("#modal-body");
            bloquearDiv(div);
            $.ajax({
                type: "POST",
                url: "/portal/consultas_agendas/excluir_anexo_exame_file_server",
                datatype : "json",
                data : {
                    codigo_item_pedido : codigo_item_pedido,
                },               
                success: function( data ){
                    if( data == 1 ){          
                        $(".visualiza_anexo").css("display", "none");
                        $(".lixeira_anexo").css("display", "none");
                    }
                    document.location.reload();
                }
            });
        }
    */   
    ');
?>

<script>
    $("#salvar").click(function(){	
        var check=0;
        var radios = $("input[name='data[PreFaturamento][status]'");
        for(var i=0; i < radios.length; i++){
            if(radios[i].checked){
                check++;
            }
        }
 
        if( check == 0){ 
            $('label.radio').css('color', 'red');   
            return false; 
        }
    });
</script>