<?php echo $this->BForm->create('ItemPedidoExame',array('url' => array('controller' => 'fornecedores', 'action' => 'upload_exame', $codigo_item_pedido), 'enctype' => 'multipart/form-data', 'target'=>'cancel2')); ?>
    <iframe style="display:none;" name="cancel2"></iframe>  
    <div class='row-fluid inline'>
        <b>Upload do Exame</b>
    </div>

    <div class='row-fluid inline'>
        <?php            
            $arquivo_app = '';
            if(strstr($pedido['AnexoExame']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
                $arquivo_app = $pedido['AnexoExame']['caminho_arquivo'];
            }
            else if(strstr($pedido['AnexoExame']['caminho_arquivo'],'http://api.rhhealth.com.br')) {
                $arquivo_app = $pedido['AnexoExame']['caminho_arquivo'];
            }
        ?>

        <div>
            <?php if(!empty($arquivo_app)): ?>

                <div style="display: inline-flex;">
                    <?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $arquivo_app, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item')) ?>

                    <?= $this->BForm->hidden('exame_aprovado_auditoria', array('value' => $pedido['AnexoExame']['aprovado_auditoria'])) ?>
                    <?php if($pedido['AnexoExame']['aprovado_auditoria']):?>
                        <p style="font-style: italic;"><?php echo $this->Html->image('icon-check.png')?> *Este exame já foi auditado anteriormente</p>
                    <?php endif; ?>
                </div>
            
            <?php endif; ?>
        </div>
        
        <div class="control-group input clear">

            <label for="CheckboxExame" class="switch">
                <?php
                    echo $this->BForm->checkbox('checkbox_exame',
                        array(
                            'type'=>'checkbox',
                            'class'=>'input-large',
                            'id'=>'CheckboxExame',
                        ));
                    ?>
                <span class="slider round"></span>
            </label>

        </div>

        <?php echo $this->BForm->input('anexo_exame', array('type'=>'file', 'label' => false)); ?>
        <?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoExame', 'class' => 'btn btn-anexos')); ?>

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
        $('#ItemPedidoExameAnexoExame').prop("disabled",true);
        $('#LimparArquivoExame').prop("disabled",true);
    });

    $("#LimparArquivoExame").click(function(){
    	$("#ItemPedidoExameAnexoExame").val("");                
    });


    function valida_salvar_exame(codigo_item_pedido) {


        if( $("#ItemPedidoExameAnexoExame").val() == "" || $("#ItemPedidoExameAnexoExame").val() == undefined ){
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

    function salvar_exame(codigo_item_pedido,codigo_verificador){
        $("#ItemPedidoExameUploadExameForm").append("<input name='codigo_verificador' type='hidden' value='" + codigo_verificador + "'  />");
        $("#ItemPedidoExameUploadExameForm").submit();
    }

    $("#CheckboxExame").change(function(){
        if($('#CheckboxExame').is(':checked')){
            $('#ItemPedidoExameAnexoExame').prop("disabled",false);
            $('#LimparArquivoExame').prop("disabled",false);
        }else{
            $('#ItemPedidoExameAnexoExame').prop("disabled",true);
            $('#LimparArquivoExame').prop("disabled",true);
            $("#ItemPedidoExameAnexoExame").val(""); 
        }
    })


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
            return false;
        }
    }); 
</script>

