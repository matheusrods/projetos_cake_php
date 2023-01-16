<div class="usuarios_fields">
    <?php
    //        pr($codigo_usuario);
    ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('codigo'); ?>
        <?php echo $this->BForm->hidden('codigo_documento'); ?>
        <?php echo $this->BForm->hidden('codigo_departamento'); ?>
        <?php echo $this->BForm->hidden('codigo_cliente'); ?>
        <?php echo $this->BForm->input('apelido', array('class' => 'input-medium', 'label' => 'Login')); ?>
        <?php echo $this->BForm->input('nome', array('class' => 'input-large', 'label' => 'Nome')); ?>
        <?php if ($this->action == 'incluir_por_cliente'): ?>
            <?php echo $this->BForm->input('senha', array('class' => 'input-small', 'label' => 'Senha', 'readonly' => true)); ?>
        <?php else: ?>
            <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Status', 'options' => array('inativo', 'ativo'))); ?>
            <?php echo $this->BForm->input('senha', array('class' => 'input-small', 'readonly' => true, 'value'=>'', 'label' => 'Gerar Senha', 'style'=>'cursor:pointer;position:relative;')); ?>
            <div class='control-group input text'>
                <label>&nbsp;</label>
                <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-refresh novaSenha', 'title' => 'Alterar Senha', 'style' => 'margin-top:7px;')); ?>
            </div>
        <?php endif ?>
    </div>

    <div class="row-fluid inline">
        <?php echo $this->BForm->input('email', array('class' => 'input-large', 'label' => 'E-mail')); ?>
        <?php echo $this->BForm->input('celular', array('class' => 'input-large celular','maxlength'=>false, 'label' => 'Celular')); ?>
        <label>&nbsp;</label>
    </div>

    <div class="row-fluid inline">
        <?php if ($this->action == 'incluir_por_cliente'): ?>
            <?php echo $this->BForm->input('token', array('class' => 'input-xxlarge hidden', 'label' => false, 'readonly' => true, 'style' =>'display:none;')); ?>
        <?php else: ?>
            <?php echo $this->BForm->input('token', array('class' => 'input-xxlarge hidden', 'readonly' => true, 'label' => false, 'style'=>'cursor:pointer;position:relative;display:none;',
                'after'=>$html->link('', 'javascript:void(0)', array('class' => 'icon-refresh novoToken hidden', 'title' => 'Alterar Token', 'style' => 'position: relative;top: -5px;right: -5px;display:none;')))); ?>
        <?php endif ?>
    </div>

    <div class="row-fluid inline">
        <?php echo $this->BForm->input('admin', array('type' => 'checkbox', 'label' => 'Usuário Admin')); ?>
        <!--        --><?php //echo $this->BForm->input('restringe_base_cnpj', array('type' => 'checkbox', 'label' => 'Restringir acesso base cnpj')); ?>
    </div>
</div>

<div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('usuario_reposavel', array('type' => 'checkbox', 'label' => 'Usuário responsável por área', 'class' => 'checkbox-responsavel')); ?>
    </div>
    <div class="row-fluid inline area_atuacao hidden">
        <?php
        if ($permissoes_de_empresa == 1) {

            if (isset($codigo_usuario)) {
                $defaultArea = $area_atuacao_selecionados;
            } else {
                $defaultArea = '';
            }
            echo $this->BForm->input('area_atuacao', array('multiple' => true, 'class' => 'input-large', 'label' => 'Área de atuação', 'options' => $combo_area_atuacao, 'default' => $defaultArea));
        }?>
    </div>
</div>


<div>
    <h4>Tipos de recebimento de alertas</h4>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('alerta_portal', array('type' => 'checkbox', 'label' => 'Alertas no portal', 'class' => 'checkbox-alerta')); ?>
        <?php echo $this->BForm->input('alerta_email', array('type' => 'checkbox', 'label' => 'Alertas por email', 'class' => 'checkbox-alerta')); ?>
        <?php echo $this->BForm->input('alerta_sms', array('type' => 'checkbox', 'label' => 'Alertas por sms', 'class' => 'checkbox-alerta')); ?>
    </div>

    <div class="row-fluid inline alertas-tipos" style="display:none;">
        <h4>Tipos de alertas</h4>
        <span class='pull-right'>
            <?=$this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("alertas")')) ?>
            <?=$this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("alertas")')) ?>
        </span>
        <div class="row-fluid inline" id="alertas">
            <!-- Carregamento dos alertas via ajax-->
        </div>
    </div>
    <input type="hidden" id="input_count" value="0">

    <?php echo $this->element('usuarios/fields_gestao_risco'); ?>



    <?php
    if ($permissoes_de_empresa == 1) :
        if (!isset($this->data['Usuario']['codigo'])): ?>
            <div>
                <h4>Empresas liberadas para exibição:</h4>

                <?php echo $this->element('usuarios_multi_cliente/clientes_por_usuario_subperfil'); ?>
            </div>
        <?php
        endif;
    endif; ?>

    <div class="form-actions">
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'id' => 'salvarUsuario')); ?>
        <?= $html->link('Voltar', array('action' => 'por_cliente', $this->data['Usuario']['codigo_cliente']), array('class' => 'btn')); ?>
    </div>
</div>

<?php echo $this->BForm->end(); ?>
<?php if( !empty($this->data['Usuario']['codigo']) && ($authUsuario['Usuario']['codigo_uperfil'] === Uperfil::ADMIN || $authUsuario['Usuario']['admin'] === 1 ) ){
    echo $this->element('usuarios_ips/incluir');
}?>
<?php echo $this->Javascript->codeBlock('
    //Verifica alertas por Perfil
    jQuery(document).ready(function(){
        setup_mascaras();
        showAlertasTipos();
        $(".checkbox-alerta").change(function(){
            showAlertasTipos();
        });
        function showAlertasTipos(){
            var checked = false;
            $(".checkbox-alerta").each(function(){
                if($(this).is(":checked")){
                    checked = true;
                }
            })
            if(checked){
                $(".alertas-tipos").show();
            }else{
                $(".alertas-tipos").hide();
            }
        }
    });', false);?>
<?php echo $javascript->codeBlock("
        var number=Math.floor((Math.random()*99999999)+1);
        $('.novaSenha').click(function(){document.getElementById('UsuarioSenha').value=number});            
        $('.novoToken').click(function(){
            $.ajax({
                url: baseUrl + 'usuarios/gerar_token',
                dataType: 'json',
                success: function(data){
                    $('#UsuarioToken').val(data);
                    $('#UsuarioToken').parent().append('<span style=\"color: #b94a48;margin-left: 10px;\">O token deve ser atualizado no sistema de integração do cliente</span>');
                }
            });
        });
    ");
?>

<script src="/portal/js/multi-select/jquery.multi-select.js"></script>
<script>
    $(function(){

        $('.celular').mask('(99)99999-9999').addClass('format-phone');

        $('#multi_select_subperfil').multiSelect();
        $('#UsuarioAreaAtuacao').multiSelect();

        $("#interno").on("change", function(e){

            //Pega numero atual de quantas vezes foram criados novos selects para subperfils
            var input_count = $("#input_count").val();
            $("#input_count").attr("value", (parseInt(input_count) + 1));
            var input_val2 = $("#input_count").val();

            var interno = $(this).val();
            var codigo_cliente = <?= !empty($this->data['Usuario']['codigo_cliente']) || isset($this->data['Usuario']['codigo_cliente']) ? $this->data['Usuario']['codigo_cliente'] : 'null'; ?>

            //Add disable ao select de subperfil
            $("#multi_select_subperfil").closest(".control-group.input.select").append('<div class="block-div"></div>');
            $("#multi_select_"+(parseInt(input_val2) - 1)+"").closest(".control-group.input.select").append('<div class="block-div"></div>');

            $.ajax({
                type: 'POST',
                url: baseUrl + 'usuarios/subperfil_ajax/' + codigo_cliente + '/' + interno + '/' + Math.random(),
                success: function(data){
                    var options = '';
                    var obj = JSON.parse(data);

                    for (var key in obj) {
                        if (obj.hasOwnProperty(key)) {
                            options += '<option value="'+key+'">'+ obj[key] +'</option>';
                        }
                    }

                    //Remove os selects anteriores
                    $("#multi_select_subperfil").closest(".control-group.input.select").remove();
                    $("#multi_select_"+(parseInt(input_val2) - 1)+"").closest(".control-group.input.select").remove();

                    //Inseri o novo select
                    var select = "<div class='control-group input select'><label>Permissões</label><select name='data[Usuario][codigo_subperfil][]' multiple id='multi_select_"+input_val2+"'></select></div>";
                    $(".inserir_select").append(select);

                    $("#multi_select_"+input_val2+"").append(options);
                    $("#multi_select_"+input_val2+"").multiSelect();
                },
                error: function(erro){
                    console.log(erro)
                }
            });
        });

        $(".checkbox-responsavel").on("change", function(){

            if ($(this).is(":checked")) {
                $(".area_atuacao").removeClass("hidden");
            } else {
                $(".area_atuacao").addClass("hidden");
                $(".area_atuacao select").val('');
            }
        });

        if ($(".checkbox-responsavel").is(":checked")) {
            $(".area_atuacao").removeClass("hidden");
        }

        $("#salvarUsuario").on("click", function(e){

            e.preventDefault();

            var codigo_uperfil = $("#UsuarioCodigoUperfil").val();

            if (codigo_uperfil == 43) {

                if ($("#UsuarioCodigoFuncaoTipo").val() == "" && $("#UsuarioCodigoGestor").val() == "") {
                    alert("O campo Tipo de Função deve ser preenchido!\nO campo Gestor de Operações deve ser preenchido!")
                } else if ($("#UsuarioCodigoFuncaoTipo").val() == "") {
                    alert("O campo Tipo de Função deve ser preenchido!")
                } else if ($("#UsuarioCodigoGestor").val() == "") {
                    alert("O campo Gestor de Operações deve ser preenchido!")
                } else {
                    console.log("vou salvar")
                    $("#UsuarioEditarPorClienteForm").submit();
                    $("#UsuarioIncluirPorClienteForm").submit();
                }
            } else if (codigo_uperfil == 50) {

                console.log($("select[name='data[Usuario][codigo_subperfil][]']").val())

                if ($("select[name='data[Usuario][codigo_subperfil][]']").val() == null && $("#interno").val() == "") {
                    alert("O campo Interno deve ser preenchido!\nO campo Permissões deve ser preenchido!")
                } else if ($("#interno").val() == "") {
                    alert("O campo Interno deve ser preenchido!")
                } else if ($("select[name='data[Usuario][codigo_subperfil][]']").val() == null) {
                    alert("O campo Permissões deve ser preenchido!")
                } else {
                    console.log("vou salvar")
                    $("#UsuarioEditarPorClienteForm").submit();
                    $("#UsuarioIncluirPorClienteForm").submit();
                }
            } else {
                $("#UsuarioEditarPorClienteForm").submit();
                $("#UsuarioIncluirPorClienteForm").submit();
            }
        })
    })
</script>

<style>
    .block-div{
        width: 219px;
        height: 55px;
        position: relative;
        background-color: #f1f1f12e;
        z-index: 999;top: -50px;
    }

    .multi-select-container {
        display: inline-block;
        position: relative;
    }

    .multi-select-menu {
        position: absolute;
        left: 0;
        top: 0.8em;
        float: left;
        min-width: 100%;
        background: #fff;
        margin: 1em 0;
        padding: 0.4em 0;
        border: 1px solid #aaa;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: none;
        z-index: 500;
        padding-left: 10px;
    }

    .multi-select-menu input {
        margin-right: 0.3em;
        vertical-align: 1px;
    }

    .multi-select-button {
        display: inline-block;
        font-size: 1em;
        padding: 0.3em 0.6em;
        max-width: 20em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: -0.5em;
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        min-width: 200px;
    }

    .multi-select-button:after {
        content: "";
        display: inline-block;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0.4em 0.4em 0 0.4em;
        border-color: #999 transparent transparent transparent;
        margin: 5px;
        vertical-align: 0.1em;
        position: absolute;
        right: 0 !important;
        margin-left: 10px;
    }

    .multi-select-container--open .multi-select-menu { display: block; }

    .multi-select-container--open .multi-select-button:after {
        border-width: 0 0.4em 0.4em 0.4em;
        border-color: transparent transparent #999 transparent;
    }

</style>
