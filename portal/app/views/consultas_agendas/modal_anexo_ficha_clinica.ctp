<?php echo $this->BForm->create('ItemPedidoExame',array('url' => array('controller' => 'consultas_agendas', 'action' => 'modal_anexo_ficha_clinica', $codigo_item_pedido,$pedido['FichaClinica']['codigo']), 'enctype' => 'multipart/form-data')); ?>
<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content">
		<div class="modal-header" style="text-align: center;">
			<h3>Anexos Ficha Clínica</h3>
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

			<div style="float:left;width: 200px;">
                <span style="font-size: 1.2em">
                    <b>Exame:</b><br />
                    <?php echo $pedido['Exame']['descricao']; ?>
                </span>
            </div>

            <div>
                <span style="font-size: 1.2em">
    				<b>Código Ficha Clínica:</b><br />
    				<?php echo $pedido['FichaClinica']['codigo']; ?>
    			</span>
			</div>

            <hr>

            <b>Upload da Ficha Clínica</b>
			<div class='row-fluid inline'>

    			<?php echo $this->BForm->input('ficha_clinica', array('type'=>'file', 'label' => false)); ?>
    			<?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoFicha', 'class' => 'btn btn-ficha')); ?>

    			<?php $arquivo = end(glob(DIR_ANEXOS.$codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido.'.*')); ?>

    			<?php if(!empty($arquivo)): ?>

    				<?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-ficha visualiza_ficha')), '/files/anexos_exames/'.$codigo_item_pedido.'/'.basename($arquivo), array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo da ficha clinica')) ?>

                    <?php if( in_array( $_SESSION['Auth']['Usuario']['codigo_uperfil'], $permissoes_acoes['deletar_anexo'][0] ) ): ?>
    				    <?php echo $this->Html->link('','javascript:void(0)', array('onclick' => "excluir_anexo('{$codigo_item_pedido}','{$codigo_ficha_clinica}')", 'class' => 'icon-trash btn-ficha lixeira_ficha', 'title' => 'Excluir anexo da ficha clinica')); ?>
                    <?php endif; ?>

    			<?php endif; ?>

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
    	$("#LimparArquivoFicha").click(function(){
        	$("#ItemPedidoExameFichaClinica").val("");                
        });

        function salvar_realizacao(codigo_item_pedido) {
        	if( $("#ItemPedidoExameFichaClinica").val() == "" || $("#ItemPedidoExameFichaClinica").val() == undefined ){
        		swal({
					type: "warning",
					title: "Atenção",
					text: "Favor selecione um arquivo para salvar",
				});
        	} else {
                var div = jQuery("#modal-body");
                bloquearDiv(div);
        		$("#ItemPedidoExameModalAnexoFichaClinicaForm").submit();
        	}
        }

        function excluir_anexo(codigo_item_pedido,codigo_ficha_clinica){
            var div = jQuery("#modal-body");
            bloquearDiv(div);
            $.ajax({
               type: "POST",
               url: "/portal/consultas_agendas/excluir_anexo_ficha_clinica",
               datatype : "json",
               data : {
                    codigo_item_pedido   : codigo_item_pedido,
                    codigo_ficha_clinica : codigo_ficha_clinica,
                },               
                success: function( data ){
                    if( data == 1 ){          
                        $(".visualiza_ficha").css("display", "none");
                        $(".lixeira_ficha").css("display", "none");
                    }
                    document.location.reload();
                }
            });
        }
    ');
?>