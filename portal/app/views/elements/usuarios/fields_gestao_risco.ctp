
<h4 id="gestaoH4" class="hidden">Perfil (Gestão de Risco)*</h4>
<div class="row-fluid inline">

    <?php echo $this->BForm->input('codigo_uperfil', array('empty' => 'Selecione ' , 'class' => 'input-large', 'label' => 'Perfil', 'options' => $perfis)) ?>

    <div id="pos" class="hidden inserir_select">
        <?php
        if ($permissoes_de_empresa == 1) {

            if (isset($codigo_usuario)) {
                $default = $subperfil_selecionados;
            } else {
                $default = 1;
            }

            echo $this->BForm->input('interno', array('class' => 'input-large', 'id' => 'interno', 'options' => array('1' => 'Interno', '0' => 'Externo'), 'label' => 'Tipo usuário',));
            echo $this->BForm->input('codigo_subperfil', array('multiple' => true, 'id'=> 'multi_select_subperfil', 'class' => 'input-large', 'label' => 'Permissões', 'options' => $subperfil, 'default' => $default));
        }
        ?>
    </div>

    <div id="gestao_risco" class="hidden">
        <?php echo $this->BForm->input('codigo_funcao_tipo', array('empty' => 'Selecione ' , 'class' => 'input-large', 'label' => 'Tipo de Função', 'options' => $funcao_tipo, 'default' => ' ')) ?>

        <?php echo $this->BForm->input('codigo_gestor', array('empty' => 'Selecione ' , 'class' => 'input-large', 'label' => 'Gestor de Operações', 'options' => $gestor_operacoes)) ?>
    </div>
</div>
<hr/>
<script>

    jQuery(function(){
        habilitaCampo = function(){
            var disabled = jQuery('#UsuarioCodigoUperfil option:selected').val();
            // console.log(disabled);
            if (disabled == 43) {
                jQuery('#gestao_risco').removeClass('hidden');
                jQuery('#gestaoH4').removeClass('hidden');
                jQuery('#UsuarioCodigoFuncaoTipo').val("");
                jQuery('#UsuarioCodigoGestor').val("");


            } else {
                jQuery('#gestao_risco').addClass('hidden');
                jQuery('#gestaoH4').addClass('hidden');
            }

            if (disabled == 50) {
                jQuery('#pos').removeClass('hidden');
                jQuery('#UsuarioCodigoFuncaoTipo').val("");
                jQuery('#UsuarioCodigoGestor').val("");
                $("#interno").val(1);
            } else {
                jQuery('#pos').addClass('hidden');

                $("#interno").val(1);

                //Pega numero atual de quantas vezes foram criados novos selects para subperfils
                var input_count = $("#input_count").val();
                $("#input_count").attr("value", (parseInt(input_count) + 1));
                var input_val2 = $("#input_count").val();

                var interno = $("#interno").val();
                var codigo_cliente = "<?= isset($codigo_cliente) ? $codigo_cliente : null ?>";

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
            }

            if (disabled != 43 && disabled != 50) {
                jQuery('#pos').addClass('hidden');
                jQuery('#gestao_risco').addClass('hidden');
                jQuery('#gestaoH4').addClass('hidden');
            }
        }
        $('#UsuarioCodigoUperfil').on('change', function(){
            habilitaCampo();
        });
        habilitaCampo();
    })
</script>
