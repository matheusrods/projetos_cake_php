
<?php echo $this->BForm->create('ItemPedidoExame',array('url' => array('controller' => 'fornecedores', 'action' => 'upload_ficha_clinica', $codigo_item_pedido,$pedido['FichaClinica']['codigo']), 'enctype' => 'multipart/form-data', 'target'=>'cancel')); ?>
    <iframe style="display:none;" name="cancel"></iframe>    

    <b>Upload da Ficha Clínica</b>
    <?= $this->BForm->hidden('ficha_codigo', array('value'=> $pedido['FichaClinica']['codigo'])) ?>
    <div class='row-fluid inline'>
        <?php $arquivo = end(glob(DIR_ANEXOS.$codigo_item_pedido.DS.'anexo_ficha_clinica_'.$codigo_item_pedido.'.*')); ?>
        
        <div>
            <?php if(!empty($arquivo)): ?>

                <div style="display: inline-flex;">
                    
                    <?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-ficha visualiza_ficha')), '/files/anexos_exames/'.$codigo_item_pedido.'/'.basename($arquivo), array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo da ficha clinica')) ?>
                    
                    <?= $this->BForm->hidden('ficha_aprovada_auditoria', array('value' => $pedido['AnexoFichaClinica']['aprovado_auditoria'])) ?>
                    <?php if($pedido['AnexoFichaClinica']['aprovado_auditoria']):?>
                        <p><?php echo $this->Html->image('icon-check.png')?> *Esta ficha já foi auditada anteriormente</p>
                    <?php endif; ?>

                </div>

            <?php endif; ?>
        </div>

        <div class="control-group input clear">

            <label for="CheckboxFichaClinica" class="switch">
                <?php
                    echo $this->BForm->checkbox('checkbox_ficha',
                        array(
                            'type'=>'checkbox',
                            'class'=>'input-large',
                            'id'=>'CheckboxFichaClinica',
                        ));
                    ?>
                <span class="slider round"></span>
            </label>
                
        </div>

        <?php echo $this->BForm->input('ficha_clinica', array('type'=>'file', 'label' => false)); ?>
        <?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoFicha', 'class' => 'btn btn-ficha')); ?>

    </div>
<?php echo $this->BForm->end(); ?>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 45px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #168D14;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(20px);
        -ms-transform: translateX(20px);
        transform: translateX(20px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>


<script>

    jQuery(document).ready(function(){
        $('#ItemPedidoExameFichaClinica').prop("disabled",true);
        $('#LimparArquivoFicha').prop("disabled",true);
    });

    $("#LimparArquivoFicha").click(function(){
        $("#ItemPedidoExameFichaClinica").val("");                
    });

    function valida_salvar_ficha_clinica(codigo_item_pedido) {
        if( $("#ItemPedidoExameFichaClinica").val() == "" || $("#ItemPedidoExameFichaClinica").val() == undefined ){
            swal({
                type: "warning",
                title: "Atenção",
                text: "Favor selecione um arquivo para salvar",
            });
            return false;
        } else {
            return true;
        }
    }

    $("#CheckboxFichaClinica").change(function(){
        if($('#CheckboxFichaClinica').is(':checked')){
            $('#ItemPedidoExameFichaClinica').prop("disabled",false);
            $('#LimparArquivoFicha').prop("disabled",false);
        }else{
            $('#ItemPedidoExameFichaClinica').prop("disabled",true);
            $('#LimparArquivoFicha').prop("disabled",true);
            $("#ItemPedidoExameFichaClinica").val("");
        }
    })

    
    function salvar_ficha_clinica(codigo_item_pedido,codigo_verificador){
        $("#ItemPedidoExameUploadFichaClinicaForm").append("<input name='codigo_verificador' type='hidden' value='" + codigo_verificador + "'  />");
        $("#ItemPedidoExameUploadFichaClinicaForm").submit();
    }

</script>