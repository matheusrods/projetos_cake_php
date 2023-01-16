<?php
    $thisData = $this->data;

   
    $arrElementConfig = array();
?> 
<div class='well'>
	<h5><?= $this->Html->link('Exibir Filtros', 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?= $bajax->form('AuditoriaExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AuditoriaExame', 'element_name' => 'auditoria_exames'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">            

                <div class="row-fluid inline">
                    <?= $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'AuditoriaExame') ?>
                </div>
                <div class="row-fluid inline">
                    <?= $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor','AuditoriaExame');?>
                </div>

                <div class="row-fluid inline">
                    <?= $this->BForm->input('nome_funcionario', array('label' => 'Nome Funcionário', 'class' => 'input-medium', 'type' => 'text')); ?>
                    <?= $this->BForm->input('cpf', array('label' => 'CPF Funcionário', 'class' => 'input-medium cpf', 'type' => 'text')); ?>
                    <?= $this->BForm->input('status', array('label' => 'Status', 'class' => 'input-small', 'options' => $status, 'selected' => 1, 'empty' => 'Todos')); ?>
                    <?= $this->BForm->input('codigo_pedido_exame', array('maxlength'=>10,'class' => 'input-medium', 'label' => 'Pedido de Exame')); ?>
                    <?= $this->BForm->input('nota_fiscal', array('class' => 'input-medium', 'label' => 'Nota fiscal')); ?>
                </div>
                <div class="row-fluid inline">
                    <?= $this->BForm->input('nome_usuario_baixa', array('label' => 'Usuário Baixa', 'class' => 'input-medium', 'type' => 'text')); ?>
                    <?= $this->BForm->input('tipo_usuario', array('class' => 'input-medium', 'label' => 'Tipo de Usuário', 'options' => array('I' => 'Interno', 'E' => 'Externo'), 'empty' => 'Todos')); ?>
                    <?= $this->BForm->input('tipo_exame', array('options' => $tipos_exames, 'label' => 'Tipo de Exame', 'empty' => 'Todos', 'class' => 'input-medium')); ?>
                    <?= $this->BForm->input('prestador_qualificado', array('class' => 'input-medium', 'label' => 'Prestador Qualificado', 'options' => array('S' => 'Sim', 'N' => 'Não'), 'empty' => 'Todos')); ?>
                </div>
                <div class="row-fluid inline">
                    <span class="label label-info">Período por:</span>
                    <div id='agrupamento'>
                        
                        <?php echo $this->BForm->input('tipo_periodo', array('type' => 'radio', 'options' => $tipo_periodo, 'default' => null, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
                    </div>
                    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
                    <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
                </div>
            </div>


            <?= $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn enviar')) ?>
            <?= $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?= $this->BForm->end() ?>
    </div>
</div>



<?= $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        setup_time();
        setup_mascaras();
        setup_datepicker();

        atualizaLista()

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AuditoriaExame/element_name:auditoria_exames/" + Math.random())
        });

        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "fornecedores/auditoria_exames_listagem/" + Math.random());
            jQuery("div#filtros").slideToggle("slow");
        }

        function select2CallbackOnChange(responseData){
            
        }

        jQuery("#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });





        $(document).on(\'blur\', \'#AuditoriaExameCodigoFornecedor\', function() { 
            var codigo_fornecedor = $(\'#AuditoriaExameCodigoFornecedor\').val();
            if (codigo_fornecedor) {
                preenche_name_fornecedor(codigo_fornecedor, "codigo");
            } else {
                $("#AuditoriaExameRazaoSocialFornecedor").val("");
                $("#AuditoriaExameNomeFantasiaFornecedor").val("");
                $("#AuditoriaExameCodigoDocumentoFornecedor").val("");
                $("#AuditoriaExameCodigoFornecedorCodigo").val("");
            }
        });
        
        $(document).on(\'blur\', \'#AuditoriaExameCodigoCliente\', function() { 
            var codigo_cliente = $(\'#AuditoriaExameCodigoCliente\').val();
            if (codigo_cliente) {
                preenche_documento_cliente(codigo_cliente, "codigo_cliente");
            } else {
                $("#AuditoriaExameRazaoSocialCliente").val("");
                $("#AuditoriaExameNomeFantasiaCliente").val("");
                $("#AuditoriaExameCodigoDocumentoCliente").val("");
                $("#AuditoriaExameCodigoClienteName").val("");
            }
        });

        $(document).on(\'change\', \'#AuditoriaExameCodigoFornecedor\', function() { 

            var codigo = $(\'#AuditoriaExameCodigoFornecedor\').val();

            if (codigo) {
                preenche_documento_fornecedor(codigo, "codigo_credenciado");
            }

        });

        $(document).on(\'blur\', \'#AuditoriaExameCodigoDocumentoFornecedor\', function() { 
            var codigo_documento = $(\'#AuditoriaExameCodigoDocumentoFornecedor\').val();
            if (codigo_documento) {
                preenche_documento_fornecedor(codigo_documento, "codigo_documento");
            } else {
                $("#AuditoriaExameRazaoSocialFornecedor").val("");
                $("#AuditoriaExameNomeFantasiaFornecedor").val("");
                // $("#AuditoriaExameCodigoDocumentoFornecedor").val("");
                $("#AuditoriaExameCodigoFornecedorCodigo").val("");
            }
        });
        
        $(document).on(\'blur\', \'#AuditoriaExameCodigoDocumentoCliente\', function() { 
            var codigo_documento = $(\'#AuditoriaExameCodigoDocumentoCliente\').val();
            if (codigo_documento) {
                preenche_documento_cliente(codigo_documento, "codigo_documento");
            } else {
                $("#AuditoriaExameRazaoSocialCliente").val("");
                $("#AuditoriaExameNomeFantasiaCliente").val("");
                // $("#AuditoriaExameCodigoDocumentoCliente").val("");
                $("#AuditoriaExameCodigoClienteName").val("");
            }
        });

        $(document).on(\'blur\', \'#AuditoriaExameCodigoDocumentoFornecedor\', function() { 
            var codigo_documento = $(\'#AuditoriaExameCodigoDocumentoFornecedor\').val();
            if (codigo_documento) {
                preenche_documento_fornecedor(codigo_documento, "codigo_documento");
            } else {
                $("#AuditoriaExameRazaoSocialFornecedor").val("");
                $("#AuditoriaExameNomeFantasiaFornecedor").val("");
                // $("#AuditoriaExameCodigoDocumentoFornecedor").val("");
                $("#AuditoriaExameCodigoFornecedorCodigo").val("");
            }
        });


        function preenche_documento_fornecedor(codigo, tipo)
        {
            if(tipo == "codigo_documento" && !validarCNPJ(codigo)){
                $("#AuditoriaExameRazaoSocialFornecedor").val("");
                $("#AuditoriaExameNomeFantasiaFornecedor").val("");
                $("#AuditoriaExameCodigoFornecedor").val("");
                $("#AuditoriaExameCodigoFornecedorCodigo").val("");

                return false;
            }

            var input = $("#AuditoriaExameCodigoDocumentoFornecedor");

            $.ajax({
                url:baseUrl + "ithealth_helper/obter_credenciado?"+tipo+"=" + codigo,
                dataType: "json",
                beforeSend: function() {
                    bloquearDiv(input.parent());
                },
                success: function(data) {
                    
                    if (data.pagination.total == 1) {

                        var dados = data.data[0];

                        if(tipo =="codigo_documento"){
                            $("#AuditoriaExameRazaoSocialFornecedor").val(dados.razao_social);
                            $("#AuditoriaExameNomeFantasiaFornecedor").val(dados.nome);
                            $("#AuditoriaExameCodigoFornecedor").val(dados.codigo);
                            $("#AuditoriaExameCodigoFornecedorCodigo").val(dados.nome);
                        }

                        if(tipo =="codigo_credenciado"){
                            $("#AuditoriaExameRazaoSocialFornecedor").val(dados.razao_social);
                            $("#AuditoriaExameNomeFantasiaFornecedor").val(dados.nome);
                            $("#AuditoriaExameCodigoDocumentoFornecedor").val(dados.codigo_documento);
                        }
                        
                    } else {
                        // swal("ATENÇÃO!", "Prestador Não encontrado", "warning");

                        $("#AuditoriaExameRazaoSocialFornecedor").val("");
                        $("#AuditoriaExameNomeFantasiaFornecedor").val("");
                        //$("#AuditoriaExameCodigoDocumentoFornecedor").val("");
                        $("#AuditoriaExameCodigoFornecedorCodigo").val("");

                    }
                },
                complete: function() {
                    input.parent().unblock();
                }
            });
        }

        function preenche_documento_cliente(codigo, tipo)
        {
            if(tipo =="codigo_documento" && !validarCNPJ(codigo)){
                $("#AuditoriaExameRazaoSocialCliente").val("");
                $("#AuditoriaExameNomeFantasiaCliente").val("");
                $("#AuditoriaExameCodigoCliente").val("");
                $("#AuditoriaExameCodigoClienteName").val("");

                return false;
            }

            if(tipo =="codigo_cliente" && !codigo){
                $("#AuditoriaExameRazaoSocialCliente").val("");
                $("#AuditoriaExameNomeFantasiaCliente").val("");
                $("#AuditoriaExameCodigoDocumentoCliente").val("");
                $("#AuditoriaExameCodigoClienteName").val("");

                return false;
            }

            var input = $("#AuditoriaExameCodigoDocumentoCliente");

            $.ajax({
                url:baseUrl + "ithealth_helper/obter_cliente?"+tipo+"=" + codigo,
                dataType: "json",
                beforeSend: function() {
                    bloquearDiv(input.parent());
                },
                success: function(data) {
                    
                    if (data.pagination.total == 1) {

                        var dados = data.data[0];

                        if(tipo =="codigo_documento"){
                            $("#AuditoriaExameRazaoSocialCliente").val(dados.razao_social);
                            $("#AuditoriaExameNomeFantasiaCliente").val(dados.nome);
                            $("#AuditoriaExameCodigoCliente").val(dados.codigo);
                            $("#AuditoriaExameCodigoClienteName").val(dados.nome);
                        }

                        if(tipo =="codigo_cliente"){
                            $("#AuditoriaExameRazaoSocialCliente").val(dados.razao_social);
                            $("#AuditoriaExameNomeFantasiaCliente").val(dados.nome);
                            $("#AuditoriaExameCodigoDocumentoCliente").val(dados.codigo_documento);
                        }
                        
                    } else {
                        // swal("ATENÇÃO!", "Prestador Não encontrado", "warning");

                        $("#AuditoriaExameRazaoSocialCliente").val("");
                        $("#AuditoriaExameNomeFantasiaCliente").val("");
                        //$("#AuditoriaExameCodigoDocumentoCliente").val("");
                        $("#AuditoriaExameCodigoFornecedorCodigo").val("");

                    }
                },
                complete: function() {
                    input.parent().unblock();
                }
            });
        }

        function preenche_name_fornecedor(codigo_fornecedor, tipo)
        {
            var input = $("#AuditoriaExameCodigoFornecedor");

            $.ajax({
                url:baseUrl + "consultas/get_fornecedores/" + codigo_fornecedor + "/" + Math.random(),
                dataType: "json",
                beforeSend: function() {
                    bloquearDiv(input.parent());
                },
                success: function(data) {

                    if (data.sucesso) {
                        
                        if(tipo =="codigo"){
                            $("#AuditoriaExameRazaoSocialFornecedor").val(data.dados.razao_social);
                            $("#AuditoriaExameNomeFantasiaFornecedor").val(data.dados.nome);
                            $("#AuditoriaExameCodigoDocumentoFornecedor").val(data.dados.codigo_documento);
                            $("#AuditoriaExameCodigoFornecedorCodigo").val(data.dados.nome);
                            
                        }
                        
                    } else {
                        // swal("ATENÇÃO!", "Prestador Não encontrado", "warning");

                        $("#AuditoriaExameRazaoSocialFornecedor").val("");
                        $("#AuditoriaExameNomeFantasiaFornecedor").val("");
                        $("#AuditoriaExameCodigoDocumentoFornecedor").val("");
                        $("#AuditoriaExameCodigoFornecedorCodigo").val("");

                    }
                },
                complete: function() {
                    input.parent().unblock();
                }
            });
        }

        function preenche_name_cliente(codigo_cliente, tipo)
        {
            var input = $("#AuditoriaExameCodigoCliente");

            $.ajax({
                url:baseUrl + "clientes/buscar/" + codigo_cliente + "/" + Math.random(),
                dataType: "json",
                beforeSend: function() {
                    bloquearDiv(input.parent());
                },
                success: function(data) {

                    if (data.sucesso) {
                        
                        if(tipo =="codigo"){
                            $("#AuditoriaExameRazaoSocialCliente").val(data.dados.razao_social);
                            $("#AuditoriaExameNomeFantasiaCliente").val(data.dados.nome_fantasia);
                            $("#AuditoriaExameCodigoDocumentoCliente").val(data.dados.codigo_documento);
                            $("#AuditoriaExameCodigoClienteName").val(data.dados.nome_fantasia);
                            
                        }
                        
                    } else {
                        swal("ATENÇÃO!", "Prestador Não encontrado", "warning");

                        $("#AuditoriaExameRazaoSocialCliente").val("");
                        $("#AuditoriaExameNomeFantasiaCliente").val("");
                        $("#AuditoriaExameCodigoDocumentoCliente").val("");
                        $("#AuditoriaExameCodigoClienteName").val("");

                    }
                },
                complete: function() {
                    input.parent().unblock();
                }
            });
        }

        
        





    });    
    
', false);

?>
<?php //= $ithealth->loadHelperJs(); ?>
