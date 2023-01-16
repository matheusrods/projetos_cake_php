<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('numero', array('readonly' => true, 'type'=>'text', 'label' => 'Nº contrato', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('data_contrato', array('type' => 'text', 'label' => 'Data contrato', 'class' => 'data input-small')); ?>
    <?php echo $this->BForm->input('data_envio', array('type' => 'text', 'label' => 'Data envio', 'class' => 'data input-small')); ?>
    <?php echo $this->BForm->input('data_vigencia', array('type' => 'text', 'label' => 'Data vencimento', 'class' => 'data input-small')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $buonny->combo_booleano('rf', array('label' => 'Reconhecimento de firma', 'class' => 'input-medium')); ?>
    <?php echo $buonny->combo_booleano('cs', array('label' => 'Contrato social', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('codigo_motivo_bloqueio', array('label' => 'Status', 'options' => $motivos_bloqueio, 'empty'=>'Selecione', 'class' => 'input-medium')); ?>
</div>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('observacao', array('label' => 'Observação', 'type'  => 'textarea', 'class' => 'input-xxlarge')); ?>
</div>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('arquivo_contrato', array('type'=>'file', 'label' => 'Upload do contrato')); ?>
    <?php echo $this->BForm->button('Limpar campo do contrato', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoContrato', 'class' => 'btn btn-contratos')); ?>
    <?php $url = DIR_CONTRATOS_PRODUTOS; ?>
    <?php
        $arquivo = end(glob($url .$codigo_cliente.DS. $codigo_cliente . '_' . $cliente_produto['Produto']['codigo'] . '_arquivo_contrato.*')); ?>
        <?php if (!empty($arquivo))
            echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-contratos visualiza_contrato')),
                '/files/contratos/' . $codigo_cliente . '/' . basename($arquivo) ,array('escape' => false, 'target' => '_blank'))?>
    <?php
        if (!empty($arquivo)) {
            $arquivo = basename($arquivo);
            echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "excluir_contrato('{$arquivo}','contrato','{$codigo_cliente}')", 'class' => 'icon-trash btn-contratos lixeira_contrato', 'title' => 'Excluir contrato'));
        }
    ?>
</div>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('arquivo_contrato_social', array('type'=>'file', 'label' => 'Upload do contrato social')); ?>
    <?php echo $this->BForm->button('Limpar campo do contrato social', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoContratoSocial', 'class' => 'btn btn-contratos')); ?>
    <?php
        $arquivo = end(glob($url .DS.$codigo_cliente.DS. $codigo_cliente . '_' . $cliente_produto['Produto']['codigo'] . '_arquivo_contrato_social.*'));
        if (!empty($arquivo))
            echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-contratos visualiza_contrato')),
                '/files/contratos/' . $codigo_cliente . '/' . basename($arquivo) ,array('escape' => false, 'target' => '_blank'))
    ?>
    <?php
        if (!empty($arquivo)) {
            $arquivo = basename($arquivo);
            echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "excluir_contrato('{$arquivo}','social','{$codigo_cliente}')", 'class' => 'icon-trash btn-contratos lixeira_social', 'title' => 'Excluir contrato social'));
        }
    ?>
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $this->Html->link('Voltar', array('action' => 'gerenciar', $codigo_cliente), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php
    echo $this->Javascript->codeBlock('
        $(document).ready(function() {
            setup_mascaras();
            setup_datepicker();
        });
            
        $("#LimparArquivoContrato").click(function(){
            $("#ClienteProdutoContratoArquivoContrato").val("");                
        });

        $("#LimparArquivoContratoSocial").click(function(){
            $("#ClienteProdutoContratoArquivoContratoSocial").val("");
        });

        function excluir_contrato(arquivo, tipo, codigo_cliente){
            $.ajax({
               type: "POST",
               url: "/portal/clientes_produtos_contratos/excluir",
               datatype : "json",
               data : {
                    arquivo : arquivo,
                    codigo_cliente : codigo_cliente,
                },               
                success: function( data ){
                    if( data == 1 ){          
                        if (tipo == "contrato") {
                            $(".visualiza_contrato").css("display", "none");
                            $(".lixeira_contrato").css("display", "none");
                        } else {
                            $(".visualiza_social").css("display", "none");
                            $(".lixeira_social").css("display", "none");
                        }
                    document.location.reload();
                    }
                }
            });
        }
      
    ');
?>