<?php echo $this->BForm->create('ItemPedidoExame',array('url' => array('controller' => 'consultas_agendas', 'action' => 'modal_anexo_exames', $codigo_item_pedido), 'enctype' => 'multipart/form-data')); ?>
<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content">
		<div class="modal-header" style="text-align: center;">
			<h3>Anexos Exames</h3>
		</div>

		<div class="modal-body" id="modal-body" style="min-height: 150px;">

			<div style="float: left;width: 200px;">
                <span style="font-size: 1.2em">
                    <b>Código Pedido:</b>
                    <?php echo $pedido['PedidoExame']['codigo']; ?>
                </span>
            </div>
            
            <div>
                <span style="font-size: 1.2em">
                    <b>Código Item:</b>
                    <?php echo $pedido['ItemPedidoExame']['codigo']; ?>
                </span>
            </div>
            <br /><br />

            <div>
                <span style="font-size: 1.2em">
                    <b>Cliente:</b><br />
                    <?php echo $pedido['Cliente']['razao_social']; ?>
                </span>
            </div>
            <br />

			<div>
                <span style="font-size: 1.2em">
                    <b>Exame:</b><br />
                    <?php echo $pedido['Exame']['descricao']; ?>
                </span>
            </div>

            <hr>


            <b>Upload do Exame</b>
            <div class='row-fluid inline'>
            
    			<?php echo $this->BForm->input('anexo_exame', array('type'=>'file', 'label' => false)); ?>
    			<?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoExame', 'class' => 'btn btn-anexos')); ?>

    			<?php 
                $arquivo = '';
                $arquivo = end(glob(DIR_ANEXOS.$codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido.'.*')); 
                
                $arquivo_app = '';
                if(strstr($pedido['AnexoExame']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
                    $arquivo_app = $pedido['AnexoExame']['caminho_arquivo'];
                }
                else if(strstr($pedido['AnexoExame']['caminho_arquivo'],'http://api.rhhealth.com.br')) {
                    $arquivo_app = $pedido['AnexoExame']['caminho_arquivo'];
                }
                ?>

                <?php if(!empty($arquivo_app)): ?>

                    <?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $arquivo_app, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) ?>

                    <?php if( in_array( $_SESSION['Auth']['Usuario']['codigo_uperfil'], $permissoes_acoes['deletar_anexo'][0] ) ): ?>
                        <?php echo $this->Html->link('','javascript:void(0)', array('onclick' => "excluir_anexo_exame_file_server('{$codigo_item_pedido}')", 'class' => 'icon-trash btn-anexos lixeira_anexo', 'title' => 'Excluir anexo do item')); ?>
                    <?php endif; ?>

                <?php else: ?>

        			<?php if(!empty($arquivo)): ?>

        				<?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), '/files/anexos_exames/'.$codigo_item_pedido.'/'.basename($arquivo), array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) ?>

                        <?php if( in_array( $_SESSION['Auth']['Usuario']['codigo_uperfil'], $permissoes_acoes['deletar_anexo'][0] ) ): ?>
        				    <?php echo $this->Html->link('','javascript:void(0)', array('onclick' => "excluir_anexo('{$codigo_item_pedido}')", 'class' => 'icon-trash btn-anexos lixeira_anexo', 'title' => 'Excluir anexo do item')); ?>
                        <?php endif; ?>
                    <?php endif; ?>

    			<?php endif; ?>               

			</div>
            <div class="aviso hidden" style="font-style: italic;">
                <?php echo "*O arquivo enviado será auditado. Caso sejam identificadas inconsistências, solicitaremos via e-mail a correção."?>
            </div>

			<div class="modal-footer">
	    		<div class="right">
					<a href="javascript:void(0);" onclick="listagem_anexo_exames(<?php echo $codigo_item_pedido; ?>, 0);" class="btn btn-danger">FECHAR</a>
    				<a href="javascript:void(0);" onclick="salvar_realizacao(<?php echo $codigo_item_pedido; ?>);" class="btn btn-success">SALVAR</a>			
    			</div>
			</div>

		</div>
	</div>
</div>
<?php echo $this->BForm->end(); ?>
<?php
    echo $this->Javascript->codeBlock('
    	$("#LimparArquivoExame").click(function(){
        	$("#ItemPedidoExameAnexoExame").val(""); 
            $(".aviso").addClass("hidden");               
        });

        function salvar_realizacao(codigo_item_pedido) {
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

        $("#ItemPedidoExameAnexoExame").bind("change", function() {
            var filesize = this.files[0].size / 1024 / 1024; //obter o tamanho do arquivo
            if (filesize > 5) { //se arquivo for maior que 5MB, barrar
                swal("Importante","Tamanho máximo excedido! Só é permitido arquivos de até 5MB", "error");
                $("#ItemPedidoExameAnexoExame").val("");
                return false;
            }
        });

        var validos = /(\.jpg|\.png|\.jpeg|\.pdf)$/i;

        $("#ItemPedidoExameAnexoExame").change(function() {
            var fileInput = $(this);
            var nome = fileInput.get(0).files["0"].name;
            if (!validos.test(nome)) {
                swal("Importante","Arquivo inválido! É aceito extensões pdf, jpg, jpeg ou png. Por favor tente novamente.", "error");
                $("#ItemPedidoExameAnexoExame").val("");
                $(".aviso").addClass("hidden");
                return false;
            }else{
                $(".aviso").removeClass("hidden");
            }
        });  
    ');
?>