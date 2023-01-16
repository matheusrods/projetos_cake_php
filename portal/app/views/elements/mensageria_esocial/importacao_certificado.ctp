
<br>
<div class='row-fluid inline' style="min-height: 60px;">
    <b>Escolha o certificado .pfx para upload</b>
	<?php echo $this->BForm->input('certificado', array('type'=>'file', 'label' => false,'class'=>'input-xxlarge','required')); ?>
	<?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoExame', 'class' => 'btn btn-anexos')); ?>

	<?php $arquivo = end(glob($dir_certificados.'certificado_'.$codigo_int_esocial_certificado.'*')); ?>
</div>

<?php if(!empty($dados_certificado['IntEsocialCertificado']['codigo'])): ?>
    <div style="padding-left: 1%">
        <i class="icon-file" ></i><?= $dados_certificado['IntEsocialCertificado']['nome_arquivo']; ?>
        <input type="hidden" name="data[MensageriaEsocial][nome_arquivo]" id="nome_arquivo" value="<?= $dados_certificado['IntEsocialCertificado']['nome_arquivo'];?>" >
        <?php 
        // echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')).' Ver Arquivo', base64_encode($dados_certificado['IntEsocialCertificado']['caminho_arquivo']), array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do item'));
        // echo " | "; 
        // echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-remove')).' Remover Arquivo', array('controller' => 'mensageria_esocial', 'action' => 'excluir_certificado', $dados_certificado['IntEsocialCertificado']['codigo']), array('escape' => false, 'title' => 'Excluir Certificado'),'Confirma exclusão do certificado?');
        ?>
    </div>
    <br />
<?php endif; ?> 

<div class='row-fluid inline'>
    <label>Senha do certificado:</label>
    <input type="password" class=" input-xlarge" name="data[MensageriaEsocial][senha_certificado]" id="senha_certificado" placeholder="Digite a Senha do certificado" value="<?php echo $senha_certificado; ?>" required > &nbsp;
    <a href="javascript:void(0);" onclick="mostra_pass();"><i class="icon-eye-open" id="icon_senha_certificado" title="Senha certificado"></i></a>
</div>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('ambiente_esocial', array('value' => !empty($dados_certificado['IntEsocialCertificado']['ambiente_esocial']) ? $dados_certificado['IntEsocialCertificado']['ambiente_esocial'] : '', 'class' => 'input-medium', 'options' => $ambiente_esocial, 'label' => 'Ambiente Esocial:')); ?>
</div>

<div class='row-fluid inline'>
    <label>Email responsável:</label>
    <input type="text" class=" input-xxlarge" name="data[MensageriaEsocial][email_responsavel]" placeholder="Email do responsável" value="<?php echo $email_responsavel; ?>" required > &nbsp;&nbsp;
    <label>Razão Social do certificado:</label>
    <input type="text" class=" input-xxlarge" name="data[MensageriaEsocial][razao_social]" placeholder="Razão Social do certificado" value="<?php echo $razao_social; ?>"  required > &nbsp;&nbsp;
</div>


    <input type="hidden" name="data[MensageriaEsocial][ip_usuario_aceite_termo]" id="ip_usuario_aceite_termo" value="<?php (isset($dados_certificado['IntEsocialCertificado']['ip_usuario_aceite_termo'])) ? $dados_certificado['IntEsocialCertificado']['ip_usuario_aceite_termo'] : '';?>" >
    <input type="hidden" name="data[MensageriaEsocial][fuso_horario]" id="fuso_horario" value="<?php isset($dados_certificado['IntEsocialCertificado']['fuso_horario']) ? $dados_certificado['IntEsocialCertificado']['fuso_horario'] : '';?>" >

<?php if(empty($codigo_int_esocial_certificado)): ?>
    <div class='row-fluid inline'>
        
        Necessário aceitar os <a href="javascript:void(0);" onclick="termo_uso(1);" >Termos de uso</a>.<br />
        <input type="checkbox" name="data[MensageriaEsocial][aceite_termo_responsabilidade]" id="aceite_termo_responsabilidade" value="1" > Aceitar o termo de uso.
    </div>

    <div class="modal fade" id="modal_termo_certificado" data-backdrop="static" style="width: 850px;left:40%;"></div>

<?php else: ?>
    <input type="hidden" name="data[MensageriaEsocial][aceite_termo_responsabilidade]" id="aceite_termo_responsabilidade" value="<?= $dados_certificado['IntEsocialCertificado']['aceite_termo_responsabilidade'];?>" >
<?php endif;?>

<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'id'=>'btn_salvar', 'disabled' => 'disabled')); ?>
    <?php echo $html->link('Voltar', array('controller' => 'mensageria_esocial', 'action' => 'index_certificado'), array('class' => 'btn')); ?>
</div>


<?php
echo $this->Javascript->codeBlock('
    $("#LimparArquivoExame").click(function(){
        $("#MensageriaEsocialCertificado").val("");                
    });

    function termo_uso(mostra) {
        if(mostra) {
            
            var div = jQuery("div#modal_termo_certificado");
            bloquearDiv(div);
            div.load(baseUrl + "mensageria_esocial/get_termo_certificado/" + Math.random());
    
            $("#modal_termo_certificado").css("z-index", "1050");
            $("#modal_termo_certificado").modal("show");

        } else {
            $(".modal").css("z-index", "-1");
            $("#modal_termo_certificado").modal("hide");
        }
    }

    $("#aceite_termo_responsabilidade").on("change",function(i){
        set_btn_salvar();
    });

    // btn_salvar
    function set_btn_salvar() {            
        var tipo = $("#aceite_termo_responsabilidade").attr("type");
        if(tipo === "hidden") {
            $("#btn_salvar").prop("disabled",false);
        }
        else if(tipo === "checkbox") {
            if($("#aceite_termo_responsabilidade").prop("checked")){
                $("#btn_salvar").prop("disabled",false);
            }
            else {
                $("#btn_salvar").prop("disabled",true);
            }
        }
    }

    set_btn_salvar();

    function mostra_pass() {
        // var x = document.getElementById("senha_certificado");
        var x = $("#senha_certificado");
        
        if (x.attr("type") === "password") {
            $("#icon_senha_certificado").attr("class","icon-eye-close");
            x.attr("type","text");
        } else {
            $("#icon_senha_certificado").attr("class","icon-eye-open");
            x.attr("type","password");
        }
    }

    $(function () {
        $.getJSON("https://api.ipify.org?format=json", function (data) {

            if($("#ip_usuario_aceite_termo").val() == "") {
                $("#ip_usuario_aceite_termo").val(data.ip);
                // console.log(data.ip);
            }

        });

        if($("#fuso_horario").val() == "") {
            $("#fuso_horario").val(Intl.DateTimeFormat().resolvedOptions().timeZone);
            // console.log(Intl.DateTimeFormat().resolvedOptions().timeZone)
        }
    });
');
?>