<?php if(!empty($codigo_cliente)): ?>
    <?php 
    $codigo_cliente = (int)$codigo_cliente; 
    if(is_int($codigo_cliente)):
    ?>
        <div class='actionbar-right'>
            <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'importacao_certificado',$codigo_cliente), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Certificado'));?>
        </div>
    <?php endif; ?>    
<?php endif; ?>

<?php if(isset($listagem) && count($listagem)) : ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th >Codigo Cliente</th>
                <th >Nome Arquivo</th>
                <th >Ambiente</th>
                <th >Data Integração</th>
                <th >Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($listagem as $key => $linha): 
                $codigo_cliente = $linha['IntEsocialCertificado']['codigo_cliente'];
            ?>
                <tr>
                    <td ><?= $linha['IntEsocialCertificado']['codigo_cliente']; ?></td>
                    <td><?= $linha['IntEsocialCertificado']['nome_arquivo']; ?></td>
                    <td><?= $ambiente_esocial[$linha['IntEsocialCertificado']['ambiente_esocial']]; ?></td>
                    <td><?= $linha['IntEsocialCertificado']['data_integracao']; ?></td>
                    <td>
                        <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$linha['IntEsocialCertificado']['codigo']}','{$linha['IntEsocialCertificado']['ativo']}', '{$codigo_cliente}')"));?>

                        <?php if($linha['IntEsocialCertificado']['ativo']== 0): ?>
                            <span class="badge-empty badge badge-important" title="Desativado"></span>
                        <?php elseif($linha['IntEsocialCertificado']['ativo']== 1): ?>
                            <span class="badge-empty badge badge-success" title="Ativo"></span>
                        <?php endif; ?>
                        
                        <?php echo $this->Html->link('', array('action' => 'importacao_certificado', $linha['IntEsocialCertificado']['codigo_cliente'], $linha['IntEsocialCertificado']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>

                        <a id="certificado_unidades_relacionadas" href="javascript:void(0);" onclick="relacionar_unidades_certo(1,<?php echo $linha['IntEsocialCertificado']['codigo_cliente'] ?>, <?php echo $linha['IntEsocialCertificado']['codigo'] ?>);" ><i class="icon-wrench" title="Relacionar os certificados as unidades"></i></a>

                        <?php // if($linha['IntEsocialCertificado']['ativo'] == 1 && is_null($linha['IntEsocialCertificado']['data_integracao'])): ?>
                            <!-- <a id="certificado_integracao" href="javascript:void(0);" onclick="integrar_certificado(<?php echo $linha['IntEsocialCertificado']['codigo_cliente']; ?>,<?php echo $linha['IntEsocialCertificado']['codigo'] ?>);" ><i class="icon-retweet" title="Integrar Certificado"></i></a> -->
                        <?php // endif;?>
                    </td>
                </tr>
            <?php endforeach; ?>        
        </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['IntEsocialCertificado']['count']; ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="modal fade" id="modal_unidades_relacionadas" style="width: 65%; left: 16%; top: 15%; margin: 0 auto;"></div>

    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>


    <!-- Dialog para visualizar os clientes-->
    <div id="dialogClienteUnidadeVisualizar" title="Unidades"></div>

<?php else: ?>
    <div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>


<script>
$(function (){
    $( "#dialogClienteUnidadeVisualizar" ).dialog({
        autoOpen: false,
        width: 1270,
        resizable: false,
        height: "auto",
        modal: true,
    });

    $( ".dialogClienteUnidadeVisualizar" ).on( "click", function() {

        $("#codigo_cliente_input").val($(this).attr("data-codigo"))

        $( "#dialogClienteUnidadeVisualizar" ).dialog( "open" );

        setup_time();
        setup_mascaras();
        setup_datepicker();

        var codigo_cliente = $("#codigo_cliente_input").val();

        atualizaListaFiltro(codigo_cliente);
        return;
    });

    function atualizaListaFiltro(codigo_cliente) {
        var div = jQuery("#dialogClienteUnidadeVisualizar");
        console.log('atualiza lista filtro 1')
        bloquearDiv(div);
        div.load(baseUrl + "usuarios/buscar_usuario_cliente/" + codigo_cliente + "/" + Math.random());
    }


    relacionar_unidades_certo = function (mostra, codigo_cliente, codigo_certificado) {
        
        if(mostra) {
            
            var div = jQuery("div#modal_unidades_relacionadas");
            bloquearDiv(div);
            div.load(baseUrl + "mensageria_esocial/rel_cliente_certificado/" + codigo_cliente + "/" + codigo_certificado + "/" + Math.random());
    
            $("#modal_unidades_relacionadas").css("z-index", "1050");
            $("#modal_unidades_relacionadas").modal("show");

        } else {
            $(".modal").css("z-index", "-1");
            $("#modal_unidades_relacionadas").modal("hide");
        }

    }

    //integra o certificado digital
    integrar_certificado = function(codigo_cliente,codigo_certificado) {

         $.ajax({
            type: "POST",
            url: baseUrl + "mensageria_esocial/integracao_certificado/" + codigo_cliente + "/" + codigo_certificado + "/" + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($("div.lista"));  
            },
            success: function(data){
                
                if(data == 1){

                    //swall
                    swal("Atenção", "Certificado integrado com sucesso.", "success");

                    atualizaLista();
                    $("div.lista").unblock();
                } else {

                    //swall
                    swal("Atenção", "Error ao integrar certificado!", "error");

                    atualizaLista();
                    $("div.lista").unblock();
                }
            },
            error: function(erro){
                swal("Atenção", "Error ao integrar!", "error");
                $("div.lista").unblock();
            }
        });

    }// fim chamada para integracao do certificado digital


});
</script>
<?php echo $this->Javascript->codeBlock('
    function mostra_botao(element) {
        if($(element).val()) {
            $("#botao").show();
        } else {
            $("#botao").hide();
        }
    }

    function atualizaLista() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "mensageria_esocial/listagem_certificado/" + Math.random());
    }


    function atualizaStatus(codigo, status, codigo_cliente){
        $.ajax({
            type: "POST",
            url: baseUrl + "mensageria_esocial/atualiza_status/" + codigo + "/" + status + "/" + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($("div.lista"));  
            },
            success: function(data){
                
                if(data == 1){
                    atualizaLista();
                    $("div.lista").unblock();
                } else {
                    atualizaLista();
                    $("div.lista").unblock();
                }
            },
            error: function(erro){
                $("div.lista").unblock();
            }
        });
    }

'); ?>
