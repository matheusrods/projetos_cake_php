<div class="form-procurar">
    <?= $this->element('/filtros/acoes_cadastradas') ?>
</div>

<div class="actionbar-right" style="margin-bottom: 10px;">
    <div style="display: inline-flex;">
        <div class="btn btn-success dialog_responsavel">Transferir ações em lote</div>
        <a title="Exportar ações de melhorias" class="dialog_responsavel_export" style="margin-left: 10px;cursor: pointer;">
            <i class="cus-page-white-excel"></i>
        </a>
    </div>
</div>

<!-- Dialog para vincular usuário responsável a cliente-->
<div id="dialogResponsavel" title="Usuário responsável"></div>

<div class='lista'></div>

<script>
    jQuery(document).ready(function(){
        $( "#dialogResponsavel" ).dialog({//dialog do formulário de cadastro
            autoOpen: false,//nao abre automaticamente
            width: 1270,//largura
            resizable: false,//se pode redimensionar
            height: "auto",//altura
            modal: true,//se é modal
        });

        $(".responsavel_select_all").on("change", function(){//seleciona todos os checkboxs

            if ($(this).is(":checked")) {//selecionou todos
                $('.tabela_select_responsavel tbody tr input:checkbox').prop('checked','checked');//check all checkboxes
            } else {//desselecionou todos
                $('.tabela_select_responsavel tbody tr input:checkbox').removeProp('checked');//uncheck all checkboxes
            }
        });

        $( ".dialog_responsavel" ).on( "click", function() {
            setup_time();//inicializa o campo de data e hora
            setup_mascaras();//inicializa as mascaras
            setup_datepicker();//inicializa o datepicker
            
            <?php if ($this->Buonny->seUsuarioForMulticliente() && isset($codigo_cliente_vinculado)) :?>//seleciona o cliente

                var codigo_cliente = $("#ClienteCodigoClienteVinculado").val();//pega o codigo do cliente

                codigo_cliente = codigo_cliente.split(",");//transforma em array
                var codigo_cliente = JSON.stringify(codigo_cliente);//transforma em json

                if(codigo_cliente != ''){//seleciona o cliente
                    
                    $( "#dialogResponsavel" ).dialog( "open" );//abre o dialog
                    atualizaListaFiltro(codigo_cliente);//atualiza a lista de responsaveis
                    return;//para a execução do script
                } else {
                    swal({//mostra mensagem de confirmação
                        type: 'warning',
                        title: 'Atenção',
                        text: 'Necessário filtrar um dos clientes.'
                    });
                    return;    
                }
                
                swal({//mostra mensagem de confirmação
                    type: 'warning',
                    title: 'Atenção',
                    text: 'Necessário filtrar um dos clientes.'
                });
                return;//para a execução do script
            <?php else: ?>
                
                var codigo_cliente = $("#ClienteCodigoCliente").val();//pega o codigo do cliente
                
                if(codigo_cliente != ''){
                    $( "#dialogResponsavel" ).dialog( "open" );//abre o dialog
                    atualizaListaFiltro(codigo_cliente);//atualiza a lista de responsaveis
                    return;//para a execução do script
                } else {
                    swal({//mostra mensagem de confirmação
                        type: 'warning',
                        title: 'Atenção',
                        text: 'Necessário filtrar um dos clientes.'
                    });
                    return;//para a execução do script
                }
            <?php endif; ?>//fim do if
        });

        $( ".dialog_responsavel_export" ).on( "click", function() {//exporta o arquivo
            var is_admin = <?= $is_admin?>;//verifica se é admin
            location.href = baseUrl + "clientes/listagem_acoes_cadastradas_visualizar/" + is_admin + "/export";
            return;
        });

        function atualizaListaFiltro(codigo_cliente) {//atualiza a lista de responsaveis
            var div = jQuery("#dialogResponsavel");//div do dialog
            bloquearDiv(div);//bloqueia a div
            div.load(baseUrl + "usuarios/buscar_usuario_cliente_acao/" + codigo_cliente + '/' + Math.random());//carrega a div com o conteúdo da função
        }

        $("#limpar-filtro-usuario").click(function(){
            bloquearDiv($(".form-procurar-user"));
            <?php if (!empty($codigo_cliente) && is_array($codigo_cliente)) :?>
                var codigo_clientes = $(".ajax-multiclientes option:selected");//pega os clientes selecionados
                var selecionados = codigo_clientes.val();//pega o valor do cliente selecionado
            <?php else: ?>
                var codigo_cliente = $("#ClienteCodigoCliente").val();
                var selecionados = codigo_cliente;
            <?php endif; ?>
            $(".form-procurar-user").load(baseUrl + "filtros/limpar/model:Usuario/element_name:buscar_usuario_cliente/"+selecionados+"/")
        });
    });
</script>