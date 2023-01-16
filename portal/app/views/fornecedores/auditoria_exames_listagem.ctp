<style type="text/css">
.badge {
    display: inline-block;
    min-width: 0px;
    padding: 3px 7px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    background-color: #777;
    border-radius: 10px;
}

.badge-success {
    color: #fff;
    background-color: #28a745;
}
.badge-semi-success {
    color: #fff;
    background-color: #b1ff00;
}
.badge-danger {
    color: #fff;
    background-color: #dc3545;
}

.badge-info {
    color: #fff;
    background-color: #17a2b8;
}
.badge-warning {
    color: #fff;
    background-color: #ffff00;
}


</style>

<?php if(!empty($dados)):?>

    <div class="well">
        <div class='actionbar-right'>
            <?= $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => 'fornecedores', 'action' => 'auditoria_exames_listagem', true), array('escape' => false, 'title' =>'Imprimir', 'target' => '_blank'));?>
        </div>
    </div>

    <?php  echo $paginator->options(array('update' => 'div.lista')); ?>

    <table class="table table-striped" >
        <thead>
            <!-- > Os campos em hidden são exigência da PC-2708 Moderação de imagem. (Matheus Brum)<-->
            <tr>
                <th>Status</th>
                <th>Anexo Exame</th>
                <th>Anexo Ficha Clinica</th>
                <th>Ações</th>
                <th>Código Credenciado</th>                
                <th class="hidden" >CNPJ Prestador</th>
                <th>Nome Fantasia</th>
                <th>Nota Fiscal</th>
                <th>Nome Fantasia Cliente</th>
                <th class="hidden">CNPJ Cliente</th>
                <th>Pedido de Exame</th>
                <th>Exame</th>
                <th class="hidden">Tipo Exame</th>
                <th>Data Realização Exame</th>
                <th>Data da Inclusao do Anexo</th>
                <th class="hidden">Usuário Baixa</th>
                <th class="hidden">Tipo Usuário</th>
                <th class="hidden">CPF Funcionário</th>
                <th>Nome Funcionário</th>
                <th class="hidden">Usuário Auditoria</th>
                <th class="hidden">Data Auditoria</th>
                <th class="hidden">Prestador Qualificado</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados as $key => $dado): ?>
                
                <tr>
                    <td <?php if($dado[0]['codigo_status_imagem'] == 2): ?> title="<?php echo $dado[0]['motivo']; ?>" <?php endif; ?> >
                        <?php
                        $badge_color = "badge-info";
                        if($dado[0]['codigo_status_imagem'] == 2) {
                            $badge_color = "badge-danger";
                        }
                        elseif($dado[0]['codigo_status_imagem'] == 3) {
                            $badge_color = "badge-success";
                        }elseif($dado[0]['codigo_status_imagem'] == 4){
                            $badge_color = "badge-warning";
                        }elseif($dado[0]['codigo_status_imagem'] == 6){
                            $badge_color = "badge-semi-success";
                        }

                        ?>
                        <div class="badge <?php echo $badge_color; ?>">&nbsp;</div>
                        <span style="font-size: 11px;" >
                            <?php echo $dado[0]['status'] ?>
                        </span>
                                
                    </td>
                    <td>
                        <?php if($dado[0]['codigo_anexo_exame'] != ''):  ?>
                           <?php 
                            $caminho_arquivo = '/files/anexos_exames/'.$dado[0]['caminho_arquivo_exame'];
                            //quando tiver no fileserver
                            if(strstr($dado[0]['caminho_arquivo_exame'],'https://api.rhhealth.com.br')) {
                                $caminho_arquivo = $dado[0]['caminho_arquivo_exame'];
                            }

                            echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $caminho_arquivo, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar')); 
                           ?>
                           <?php if($dado[0]['anexo_aprovado_aud']):?>
                                <?php echo $this->Html->image('icon-check.png')?>
                           <?php endif; ?>
                        <?php endif;  ?>
                    </td>
                    <td>
                        <?php 
                        $Configuracao = &ClassRegistry::init('Configuracao');
                        if(($dado[0]['codigo_exame'] == $Configuracao->getChave('INSERE_EXAME_CLINICO')) && !empty($dado[0]['caminho_arquivo_ficha_clinica'])):  ?>
                            <?php 
                                $caminho_arquivo = '/files/anexos_exames/'.$dado[0]['caminho_arquivo_ficha_clinica'];
                                //quando tiver no fileserver
                                if(strstr($dado[0]['caminho_arquivo_ficha_clinica'],'https://api.rhhealth.com.br')) {
                                    $caminho_arquivo = $dado[0]['caminho_arquivo_ficha_clinica'];
                                }

                                echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $caminho_arquivo, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar')) ?>
                                <?php if($dado[0]['ficha_aprovado_aud']):?>
                                    <?php echo $this->Html->image('icon-check.png')?>
                                <?php endif; ?>
                        <?php endif;  ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="modal-open" data-modalname="modal_data" data-codigo="<?= $dado[0]['codigo_item_pedido_exame']; ?>" data-exame="<?= $dado[0]['codigo_exame']; ?>"><i class="icon-wrench" title="Auditar"></i></a>

                        <a href="javascript:void(0);" onclick="log_anexos(<?= $dado[0]['codigo_item_pedido_exame']; ?>)"><i class="icon-eye-open" title="Log do anexo do exame"></i></a>

        			</td>
                    <td><?= $dado[0]['codigo_fornecedor'] ?></td>
                    <td class="hidden"><?= Comum::formatarDocumento($dado[0]['fornecedor_cnpj']) ?></td>
                    <td><?= $dado[0]['fornecedor_nome'] ?></td>               
                    <td><?= $dado[0]['nota_fiscal'] ?></td>
                    <td><?= $dado[0]['cliente_nome'] ?></td>
                    <td class="hidden"><?= Comum::formatarDocumento($dado[0]['cliente_cnpj']) ?></td>
                    <td><?= $dado[0]['codigo_pedido_exame'] ?></td>
                    <td><?= $dado[0]['exame'] ?></td>
                    <td class="hidden"><?= $dado[0]['tipo_exame'] ?></td>
                    <td><?= $dado[0]['data_realizacao_exame'] ?></td>                    
                    <td><?= Comum::formataData($dado[0]['data_inclusao_anexo'],'mssql','dmyhms') ?></td>
                    <td class="hidden"><?= $dado[0]['usuario_baixa'] ?></td>
                    <td class="hidden"><?= $dado[0]['tipo_usuario'] ?></td>
                    <td class="hidden"><?= Comum::formatarDocumento($dado[0]['funcionario_cpf']) ?></td>
                    <td><?= $dado[0]['funcionario_nome'] ?></td>
                    <td class="hidden"><?php if($dado[0]['codigo_status_auditoria'] == 2 || $dado[0]['codigo_status_auditoria'] == 3) { echo $dado[0]['auditoria_usuario_nome']; }?></td>
                    <td class="hidden"><?php if($dado[0]['codigo_status_auditoria'] == 2 || $dado[0]['codigo_status_auditoria'] == 3) { echo Comum::formataData($dado[0]['auditoria_data'],'mssql','dmy');  } ?></td>
                    
                    <td class="hidden"><?php 
                        $prestadorQualificado = '';
                        
                        if(!empty($filtros["prestador_qualificado"])){
                            switch ($filtros["prestador_qualificado"]) {
                                case '1':
                                    $prestadorQualificado = 'Sim';
                                break;
                                case '0':
                                    $prestadorQualificado = 'Não';
                                break;
                            }
                        }
                        
                        echo $prestadorQualificado;

                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>        
        </tbody>
    </table>

    <div class='row-fluid'>
    	<div class='numbers span4'>
    		<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
    		<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    	</div>
    	<div class='counter span4'>
    		<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    	</div>
    	<div class='counter span4'>
            <td colspan = "15"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ItemPedidoExame']['count']; ?></td>
    	</div>

    </div>
    <?php echo $this->Js->writeBuffer(); ?>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 


<div id="modal_data" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 700px;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Auditar Exame</h3>
    </div>
    
    <div class="modal-body" style="max-height: 560px;">

        <div class="row-fluid inline" style="margin-bottom:5px;">
            <div class="span4">
                <?= $this->BForm->hidden('codigo_exame') ?>
                <?= $this->BForm->hidden('status_nfs') ?>
                <?= $this->BForm->input('codigo_pedido_exame', array('readonly' => true,  'class' => 'input-mini', 'label' => 'Cód. Pedido', 'style' => '')); ?>
                <?= $this->BForm->input('codigo_item_pedido_exame', array('readonly' => true,  'class' => 'input-mini', 'label' => 'Cód. Item', 'style' => '')); ?>        
            </div>
            <div class="span4">
                <?= $this->BForm->input('notas_fiscais', array('label' => false, 'class' => 'input-medium', 'options' => array(), 'type' => 'select', 'required' => false,'label' => 'Nota fiscal', 'value' => null)) ?>
            </div>
            <div class="span4 teste_fisico">
                <label>Recebimento Físico:</label> <!-- Perguntar se físico é baseado na ficha clinica -->
                <?= $this->BForm->input('recebimento_fisico', array('value'=> null, 'type' => 'radio', 'options' => array(0 => 'Não', 1 => 'Sim'), 'legend' => false, 'title' => 'Físico', 'label' => array('value'=>"Físico", 'class' => 'radio inline input-xsmall '))) ?>
            </div>
        </div>
        <div class="row-fluid inline" style="margin-bottom:5px;">
            <div class="span8">
                <?= $this->Form->input('exame', array('type' => 'text', 'readonly' => true, 'class' => 'input-xlarge',  'label' => 'Exame')); ?>
            </div>
            <div class="liberar_anexo_exame hidden">
                <div class="span4">
                    <label>Liberar o anexo Exame:</label> <!-- Perguntar se o anexo estará liberado mesmo quando for reprovado na auditoria -->
                    <?= $this->BForm->input('libera_anexo_exame', array('value'=> null, 'type' => 'radio', 'options' => array(0 => 'Não', 1 => 'Sim'), 'legend' => false, 'title' => 'Libera Exame', 'label' => array('value'=>"Libera Exame", 'class' => 'radio inline input-xsmall '))) ?>
                </div>
            </div>
        </div>

        <div class="row-fluid inline" style="margin-bottom:5px;">
            <div class="span4">
                <?= $this->BForm->input('codigo_status_auditoria_exames', array('label' => 'Status', 'style' => 'width:100%;', 'options' => array(), 'type' => 'select', 'default' => 1, 'value' => null)) ?>
            </div>
            <div class="span4">
                <?= $this->Form->input('data_baixa', array('type' => 'text', 'readonly' => true,  'style' => 'width:100%;', 'label' => 'Data Baixa', 'style' => 'width:100%')); ?>
            </div>
            <div class="liberar_anexo_ficha hidden">
                <div class="span4">
                    <label>Liberar o anexo da Ficha Clinica:</label> <!-- Perguntar se o anexo estará liberado mesmo quando for reprovado na auditoria -->
                    <?= $this->BForm->input('libera_anexo_ficha', array('value'=> null, 'type' => 'radio', 'options' => array(0 => 'Não', 1 => 'Sim'), 'legend' => false, 'title' => 'Libera Ficha Clínica', 'label' => array('value'=>"Libera Ficha Clínica", 'class' => 'radio inline input-xsmall '))) ?>
                </div>
            </div>
        </div>

        
        <div class="row-fluid inline">
            <div class="motivo-obrigatoria hidden">
                <div class="span3">
                    <?= $this->BForm->input('codigo_tipo_glosa', array('value' => null, 'label' => 'Tipos de Glosas', 'class' => 'input-small', 'default' => '','options' => array(), 'empty' => 'Selecione um tipo', 'type' => 'select')); ?>
                </div>
                <div class="span6">
                    <span style="font-size: 1.2em;" id="motivo_obrigatoria" >
                        <b>Observações</b>
                        <?php echo $this->Form->input('Glosa.motivo', array('type' => 'textarea', 'class' => 'input-small', 'label' => false, 'style' => 'height: 60px; width: 220px; font-size: 11px;', 'value' => null)); ?>
                    </span>				
                </div>
            </div>
            <div class="motivo-auditoria">
                <div class="span6">
                    <span style="font-size: 1.2em;" id="motivo_obrigatoria" >
                        <b>Observações</b>
                        <?php echo $this->Form->input('AuditoriaExames.motivo', array('type' => 'textarea', 'class' => 'input-small', 'label' => false, 'style' => 'height: 60px; width: 220px; font-size: 11px;', 'value' => null)); ?>
                    </span>				
                </div>
            </div>
        </div>	

        <div class="upload-exame hidden">
            <div class="row-fluid inline">
                <div class="span6">
                    <?= $this->BForm->input('motivo_aprovado_ajuste', array('value' => null, 'label' => 'Motivo do ajuste', 'style' => 'width:100%;', 'class' => 'input-small','options' => array(),'type' => 'select')); ?>
                </div>
            </div>
            <div class="row-fluid inline">
                <div id="upload-exame-images"></div>
                <div id="upload-ficha-images"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger modal_data_fechar" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <button class="btn btn-success modal_data_salvar">Salvar</button>
    </div>
</div>

<?= $this->Buonny->link_css('sweetalert'); ?>
<?= $this->Buonny->link_js('sweetalert.min'); ?>
<?php $Configuracao = &ClassRegistry::init('Configuracao'); ?>
<script type="text/javascript">

    var mensagem = function(mensagem, tipo, titulo){
        
        var alerta = swal;

        this.tipo = tipo || "warning"
        this.titulo = titulo || "Atenção"

            alerta({
                type: this.tipo,
                title: this.titulo,
                text: mensagem
            });
    }

    jQuery(document).ready(function() {
        setup_mascaras(); 
        setup_time(); 
        setup_datepicker();

        $(".modal-open").on("click",function(){
        
            var _this = $(this);                      // recupera info do objeto que esta sendo clicado
            var modalName = _this.data("modalname");  // recupera o nome da modal que será aberta
            var codigo = _this.data("codigo");        // recupera o codigo_item_pedido_exame usado para buscar infos
            var exame = _this.data("exame")           // recupera o exame para validar o anexo de ficha clínica no status de ajuste

            var modalObject = $("#"+modalName+"");
    
            carregarItemPedido(codigo, exame).done(function(response){

                

                var codigoStatus = $("#codigo_status_auditoria_exames");

                selecaoPagamentoBloqueado(codigoStatus.val() == 2);
                selecaoAprovadoComAjustes(codigoStatus.val() == 4 || codigoStatus.val() == 6);

                codigoStatus.change(function(){
                    
                    selecaoPagamentoBloqueado(codigoStatus.val() == 2);
                    selecaoAprovadoComAjustes(codigoStatus.val() == 4 || codigoStatus.val() == 6);


                });

                modalObject.css("z-index", "1050");
                modalObject.modal("show");
            });
            
        });
    });

    $(".modal_data_fechar").on("click",function(){
            
    });

    
    $(".modal_data_salvar").on("click",function(e){
        e.preventDefault();
        var retorno = true;
        var retornoSwal = false;
        var exame_auditado = false;
        var ficha_auditada = false;
        var codigo_pedido_exame      = $("#codigo_pedido_exame").val();
        var codigo_item_pedido_exame = $("#codigo_item_pedido_exame").val();   
        var codigo_tipo_glosa	     = $("#codigo_tipo_glosa").val(); 
        var status		             = $("#codigo_status_auditoria_exames").val();    
        var recebimento_fisico_nao   = $('#RecebimentoFisico0').is(':checked');
        var recebimento_fisico_sim   = $('#RecebimentoFisico1').is(':checked');
        var codigo_exame             = $('#codigo_exame').val();
        var status_nfs               = $('#status_nfs').val();
        var checkado_exame           = $('#CheckboxExame').is(':checked');          //recuperando valor da checkbox para validação
        var motivo_aprovado_ajuste   = $("#motivo_aprovado_ajuste").val();
        var libera_anexo_exame       = $("input[name='data[libera_anexo_exame]']:checked").val();
        var libera_anexo_ficha       = $("input[name='data[libera_anexo_ficha]']:checked").val();


        if(status_nfs == 5){
            swal('Erro!', 'Este exame pertence a uma nota fiscal já finalizada. Solicite a reabertura da nota para editar o exame.', 'warning');
            retorno = false;
        }

        if(codigo_exame == <?=$Configuracao->getChave('INSERE_EXAME_CLINICO')?>){
            var checkado_ficha_clinica   = $('#CheckboxFichaClinica').is(':checked');   //recuperando valor da checkbox para validação
        }

        var fisico = null;



        if(recebimento_fisico_sim){
            var fisico = 1;
        }else if(recebimento_fisico_nao){
            var fisico = 0;
        }

        if(!status){
            swal('Erro!', 'Selecione um status', 'warning');
            retorno = false;
        }
        
        //Validação para tipo de glosa obrigatório em caso de status bloqueado
        if(!codigo_tipo_glosa && status == 2){
            swal('Erro!', 'Selecione um tipo de glosa', 'warning');
            retorno = false;
        }

        if(fisico === null){
            swal('Erro!', 'Campo Recebimento Físico é obrigatório', 'warning');
            retorno = false;
        }    

        

        //Validações para status "Aprovado com ajuste" e "Aprovado parcial"
        if(status == 4 || status == 6){

            if(!motivo_aprovado_ajuste){
                swal('Erro!', 'Selecione um motivo de ajuste', 'warning');
                retorno = false;
            }            

            //Validações exclusivas para o exame 52
            if(codigo_exame == <?=$Configuracao->getChave('INSERE_EXAME_CLINICO')?>){
                
                if(checkado_ficha_clinica){
                    var retorno_ficha_clinica = true;
                    retorno_ficha_clinica = valida_salvar_ficha_clinica(codigo_item_pedido_exame);
                    if(retorno_ficha_clinica){
                        ficha_auditada = true;
                    }else{
                        retorno = false;
                    }
                }

                if(checkado_exame){
                    var retorno_exame = true;
                    retorno_exame = valida_salvar_exame(codigo_item_pedido_exame);
                    if(retorno_exame){
                        var exame_auditado = true;
                    }else{
                        retorno = false;
                    }
                }

                
                if(!exame_auditado && !ficha_auditada){
                    var exame_aprovado_auditoria             = $('#ItemPedidoExameExameAprovadoAuditoria').val();
                    var ficha_aprovada_auditoria             = $('#ItemPedidoExameFichaAprovadaAuditoria').val();

                    if(!exame_aprovado_auditoria && !ficha_aprovada_auditoria){
                        swal('Atenção!', 'Insira um anexo para continuar', 'warning');
                        retorno = false;
                    }

                }
            }else{
                if(checkado_exame){
                    var retorno_exame = true;
                    retorno_exame = valida_salvar_exame(codigo_item_pedido_exame);
                    if(retorno_exame){
                        var exame_auditado = true;
                    }else{
                        retorno = false;
                    }
                }else{
                    var exame_aprovado_auditoria             = $('#ItemPedidoExameExameAprovadoAuditoria').val();
                    if(!exame_aprovado_auditoria){
                        swal('Atenção!', 'Insira um anexo para continuar', 'warning');
                        retorno = false;
                    }
                }

            }
        }

        if (status == 2) { //se o status for "Reprovada"
            if (!Boolean(libera_anexo_exame)) { //verifica se o radio de liberacao do exame esta marcado
                swal('Atenção!', 'Voce deve definir se os anexos devem ser liberados', 'warning');
                retorno = false;
            }
            if(codigo_exame == <?=$Configuracao->getChave('INSERE_EXAME_CLINICO')?> && !Boolean(libera_anexo_ficha)){ // se houver ficha clinica, verifica se foi marcado a liberação ou nao
                swal('Atenção!', 'Voce deve definir se os anexos devem ser liberados', 'warning');
                retorno = false;
            }
        }

        if(retorno == true && retornoSwal == false){
            const str = Math.floor(Math.random() * Math.pow(16, 8)).toString(16);
            var codigo_verificador = "0".repeat(8 - str.length) + str;

            if(exame_auditado){
                salvar_exame(codigo_item_pedido_exame,codigo_verificador);
            }
            if(ficha_auditada){
                salvar_ficha_clinica(codigo_item_pedido_exame,codigo_verificador);
            }
                    
            salvar_realizacao(codigo_item_pedido_exame, codigo_pedido_exame,fisico,status,exame_auditado,ficha_auditada,codigo_exame,codigo_verificador,libera_anexo_exame,libera_anexo_ficha);
            
        }  
    });



function carregarItemPedido(codigo_item_pedido_exame, codigo_exame){
    var div = jQuery("body");
    var html = "";
	bloquearDiv(div);
    return $.ajax({
        url: baseUrl + "fornecedores/modal_auditar/" + codigo_item_pedido_exame + "/" + Math.random(),
        type: "GET",
        dataType: "json",
        beforeSend: function(data){ 
            jQuery("#upload-exame-images").empty();
            jQuery("#upload-exame-images").load(baseUrl + "fornecedores/upload_exame/" + codigo_item_pedido_exame + "/" + Math.random());
            jQuery("#upload-ficha-images").empty();
            if(codigo_exame == <?=$Configuracao->getChave('INSERE_EXAME_CLINICO')?>){
                jQuery("#upload-ficha-images").load(baseUrl + "fornecedores/upload_ficha_clinica/" + codigo_item_pedido_exame + "/" + 0 + "/" + Math.random());
            }
        },
    })
    .done(function(response) {
        if(response) {
            preencherCamposDaModal(response);
        }
    })
    .fail(function(error) {
        console.log(error); 
    })
    .always(function() {
        desbloquearDiv(div);
    });
}

function preencherCamposDaModal(dados) {

    var codigo_exame = $("#codigo_exame");
    var codigo_pedido_exame = $("#codigo_pedido_exame");
    var codigo_item_pedido_exame = $("#codigo_item_pedido_exame");
    var recebimento_fisico_nao = $('#RecebimentoFisico0');
    var recebimento_fisico_sim = $('#RecebimentoFisico1');
    var exame = $("#exame");
    var data_baixa = $("#data_baixa");
    var status  = $("#codigo_status_auditoria_exames");
    var codigo_tipo_glosa = $("#codigo_tipo_glosa");
    var motivo = $("#GlosaMotivo");
    var motivo_auditoria = $("#AuditoriaExamesMotivo");
    var nota_fiscal = $("#notas_fiscais");
    var status_nfs = $("#status_nfs");
    var motivo_aprovado_ajuste = $("#motivo_aprovado_ajuste");
    var libera_anexo_exame       = $("input[name='data[libera_anexo_exame]']");
    var libera_anexo_ficha       = $("input[name='data[libera_anexo_ficha]']");
    
    var newOptionsStatusAuditoria = dados.status_auditoria;

    if(status.prop) {
      var options = status.prop("options");
    }
    else {
      var options = status.attr("options");
    }

    $("option", status).remove();
    

    if(dados.dados[0].codigo_status_auditoria_imagem > 4){
        status.append($('<option />').val(null).text("Selecione"));
    }

    $.each(newOptionsStatusAuditoria, function(val, text) {
        options[options.length] = new Option(text, val);
    });

    var newOptionsTipoGlosas = dados.tipo_glosas;
  
    if(codigo_tipo_glosa.prop) {
      var options = codigo_tipo_glosa.prop("options");
    }
    else {
      var options = codigo_tipo_glosa.attr("options");
    }

    $("option", codigo_tipo_glosa).remove();

    codigo_tipo_glosa.append($('<option />').val(null).text("Selecione"));

    $.each(newOptionsTipoGlosas, function(val, text) {
        options[options.length] = new Option(text['TipoGlosas']['descricao'],text['TipoGlosas']['codigo']);
    });

    var newOptionsMotivoAprovadoAjuste = dados.motivo_aprovado_ajuste;
  
    if(motivo_aprovado_ajuste.prop) {
      var options = motivo_aprovado_ajuste.prop("options");
    }
    else {
      var options = motivo_aprovado_ajuste.attr("options");
    }

    $("option", motivo_aprovado_ajuste).remove();

    motivo_aprovado_ajuste.append($('<option />').val(null).text("Selecione"));

    $.each(newOptionsMotivoAprovadoAjuste, function(val, text) {
        options[options.length] = new Option(text['MotivosAprovadoAjuste']['descricao'],text['MotivosAprovadoAjuste']['codigo']);
    });


    var newOptionsNotaFiscal = dados.notas_fiscais;
  
    if(nota_fiscal.prop) {
      var options = nota_fiscal.prop("options");
    }
    else {
      var options = nota_fiscal.attr("options");
    }

    $("option", nota_fiscal).remove();
        
    nota_fiscal.append($('<option />').val(null).text("Selecione uma nota"));
    
    //Se o exame já possui vinculo com uma nota, mesmo que finalizada essa condição irá trazer ela
    if(dados.dados[0].nota_fiscal && dados.dados[0].codigo_nota_fiscal){
        options[options.length] = new Option(dados.dados[0].nota_fiscal,dados.dados[0].codigo_nota_fiscal);

        if(dados.dados[0].nota_fiscal_status === 5){
            if(nota_fiscal.prop) {
                $(nota_fiscal).prop('disabled', true);
            }
            else {
                $(nota_fiscal).attr('disabled','disabled');
            }
        }else{
            if(nota_fiscal.prop) {
                $(nota_fiscal).prop('disabled', false);
            }
            else {
                $("input").removeAttr('disabled');
            }
        }
    }

    
    
    $.each(newOptionsNotaFiscal, function(val, text) {
        options[options.length] = new Option(text, val);
    });

    

    if(status.prop) {
      var options = status.prop("options");
    }
    else {
      var options = status.attr("options");
    }

    //Zerando as radios de recebimento fisico
    if(recebimento_fisico_sim.prop) {
        recebimento_fisico_sim.prop("checked",false);
        recebimento_fisico_nao.prop("checked",false);
    }else{
        recebimento_fisico_sim.attr('checked','checked');
        recebimento_fisico_nao.attr("checked","checked");
    }

    //Zerando as radios de liberação de anexo
    if(libera_anexo_exame.prop) {
        libera_anexo_exame.prop("checked",false);
        libera_anexo_ficha.prop("checked",false);
    }else{
        libera_anexo_exame.attr('checked','checked');
        libera_anexo_ficha.attr("checked","checked");
    }


    //Setando o valor, caso o exame já tenha sido auditado
    if(dados.dados[0].libera_anexo_exame == 1){
        $("#LiberaAnexoExame1").attr("checked",true);
        if ($("#LiberaAnexoExame1").prop) {
            $("#LiberaAnexoExame1").prop('checked',true);
        }
    }else if(dados.dados[0].libera_anexo_exame == 0){
        $("#LiberaAnexoExame0").attr("checked",true);
        if ($("#LiberaAnexoExame0").prop) {
            $("#LiberaAnexoExame0").prop('checked',true);
        }
    }

    
    if(dados.dados[0].libera_anexo_ficha === 1){
        $("#LiberaAnexoFicha1").attr("checked",true);
        if ($("#LiberaAnexoFicha1").prop) {
            $("#LiberaAnexoFicha1").prop('checked',true);
        }
    }else if(dados.dados[0].libera_anexo_ficha === 0){
        $("#LiberaAnexoFicha0").attr("checked",true);
        if ($("#LiberaAnexoFicha0").prop) {
            $("#LiberaAnexoFicha0").prop('checked',true);
        }
    }
    

    if(dados.dados[0].recebimento_fisico === 1){
        recebimento_fisico_sim.attr("checked");
        if(recebimento_fisico_sim.prop) {
            recebimento_fisico_sim.prop("checked",true);
        }
        else {
            recebimento_fisico_sim.attr('checked','checked');
        }
    }else if (dados.dados[0].recebimento_fisico === 0){
        if(recebimento_fisico_nao.prop) {
            recebimento_fisico_nao.prop("checked",true);
        }
        else {
            recebimento_fisico_nao.attr("checked","checked");
        }
    }



    codigo_pedido_exame.val(dados.dados[0].codigo_pedido_exame);
    codigo_item_pedido_exame.val(dados.codigo_item_pedido_exame);
    exame.val(dados.dados[0].exame);
    data_baixa.val(dados.dados[0].data_baixa);
    nota_fiscal.val(dados.dados[0].codigo_nota_fiscal);
    codigo_exame.val(dados.dados[0].codigo_exame);
    motivo_aprovado_ajuste.val(dados.dados[0].codigo_motivos_aprovado_ajuste);
    motivo_auditoria.val(dados.dados[0].auditoria_motivo);
    status_nfs.val(dados.dados[0].nota_fiscal_status);

    if(dados.dados[0].codigo_status_auditoria_imagem > 4){
        status.val(null);
    }else{
        status.val(dados.dados[0].codigo_status_auditoria_imagem);
    }

    if(dados.glosas){
        // data_glosa.val(dados.glosas.data_glosa);
        codigo_tipo_glosa.val(dados.glosas.codigo_tipo_glosa);
        // data_vencimento.val(dados.glosas.data_vencimento);
        // data_pagamento.val(dados.glosas.data_pagamento);
        motivo.val(dados.glosas.motivo_glosa);
        // valor_glosa.val(dados.glosas.valor);
    }

}




function selecaoPagamentoBloqueado( situacao )
{
    let codigo_exame = $('#codigo_exame').val();
    var classFieldArr = $(".motivo-obrigatoria, .liberar_anexos");
        if(situacao){
            if (codigo_exame==<?=$Configuracao->getChave('INSERE_EXAME_CLINICO')?>) {
                $(".liberar_anexo_ficha").removeClass("hidden");
            }
            $(".motivo-obrigatoria, .liberar_anexo_exame").removeClass("hidden");
            $(".motivo-auditoria").addClass("hidden");
        } else {
            $(".motivo-obrigatoria, .liberar_anexo_exame, .liberar_anexo_ficha").addClass("hidden");
            $(".motivo-auditoria").removeClass("hidden");
        }
    
}

function selecaoAprovadoComAjustes( situacao )
{
    
    var classFieldArr = $(".upload-exame");
    
    $.each(classFieldArr, function(index, field){ 
        if(situacao){
            $(field).removeClass("hidden");
        } else {
            $(field).addClass("hidden");
        }
    });
}


function atualizaLista() {
    var div = jQuery("div.lista");
    bloquearDiv(div);
    div.load(baseUrl + "fornecedores/auditoria_exames_listagem/" + Math.random());
}


function salvar_realizacao(codigo_item_pedido, codigo_pedido_exame, fisico, status_auditoria,exame_auditado,ficha_auditada,codigo_exame,codigo_verificador,libera_anexo_exame,libera_anexo_ficha) {

    //pega a data
    var retorno = true;
    var div = jQuery("#modal_data");

    var status		                  = ((status_auditoria != null) ? status_auditoria : $("#codigo_status_auditoria_exames").val());
    var motivo		                  = $("#GlosaMotivo").val();
    var motivo_auditoria		      = $("#AuditoriaExamesMotivo").val();
    var codigo_tipo_glosa	          = $("#codigo_tipo_glosa").val();
    var codigo_nota_fiscal_servico	  = $("#notas_fiscais").val();
	var numero_nota_fiscal	  		  = $("#notas_fiscais option:selected").text();	
    var motivo_aprovado_ajuste        = $("#motivo_aprovado_ajuste").val();   

    if(codigo_exame == <?=$Configuracao->getChave('INSERE_EXAME_CLINICO')?>){
        var codigo_ficha_clinica          = $("#ItemPedidoExameFichaCodigo").val();
    }
     
    if(!codigo_nota_fiscal_servico){
        numero_nota_fiscal = null;
    }

    bloquearDiv(div);
    if(retorno == true){
        //envia via ajax a data de realizacao
        $.ajax({
            url: baseUrl + "fornecedores/salvar_auditoria",
            type: "POST",
            dataType: "json",
            data: {
                "codigo_item_pedido"                : codigo_item_pedido,
                "codigo_pedido_exame"               : codigo_pedido_exame,
                "status"			                : status,
                "motivo"			                : motivo,
                "motivo_auditoria"			        : motivo_auditoria,
                "codigo_tipo_glosa"                 : codigo_tipo_glosa,
                "fisico"                            : fisico,
                "codigo_nota_fiscal_servico" 		: codigo_nota_fiscal_servico,
                "numero_nota_fiscal"        		: numero_nota_fiscal,
                "exame_auditado"                    : exame_auditado,
                "ficha_auditada"                    : ficha_auditada,
                "libera_anexo_exame"                : libera_anexo_exame,
                "libera_anexo_ficha"                : libera_anexo_ficha,
                "codigo_ficha_clinica"        		: codigo_ficha_clinica,
                "motivo_aprovado_ajuste"        	: motivo_aprovado_ajuste,
            }

        })
        .done(function(data) {

            desbloquearDiv(div);

            if(data.retorno == false) {

                mensagem(data.mensagem);
                
            } else {
                
                mensagem(data.mensagem, "success", "Sucesso");

                atualizarTela(codigo_pedido_exame,codigo_item_pedido,codigo_verificador,ficha_auditada,exame_auditado);
                
            }

        })
        .fail(function() {
            desbloquearDiv(div);
            mensagem("Houve uma falha no processo, por favor tente novamente.", "error", "Erro");
        })
        

   

    }

}//fim function salvar_realizacao

//funções criadas devido aos uploads demorarem mais tempo para serem executados do que o save de auditoria
async function atualizarTela(codigo_pedido_exame,codigo_item_pedido,codigo_verificador,ficha_auditada,exame_auditado){
    var retorno_ficha = false;
    var retorno_exame = false;



        if(ficha_auditada){
            retorno_ficha = await verificaUploadFicha(codigo_pedido_exame,codigo_verificador);

        }else{
            retorno_ficha = true;
        }

        if(exame_auditado){
            retorno_exame = await verificaUploadExame(codigo_item_pedido,codigo_verificador);

        }else{
            retorno_exame = true;
        }
        
    
        if(retorno_ficha && retorno_exame){
            $("#modal_data").modal("hide");
            atualizaLista();
        }
}

async function verificaUploadFicha(codigo_pedido_exame,codigo_verificador){
    var retorno = null;
    await $.ajax({
            url: baseUrl + "fornecedores/verificador_ficha/" + codigo_pedido_exame + "/" + codigo_verificador + "/" ,
            type: "GET",
            dataType: "json", 
    }).done(function(data) {
        if(data == 0){
            retorno = setTimeout(() => { verificaUploadFicha(codigo_pedido_exame,codigo_verificador); }, 5000);
        }else if (data == 1){
            retorno =  true;
        }
    })   
    return new Promise(resolve => {(resolve(retorno))});
}

async function verificaUploadExame(codigo_item_pedido,codigo_verificador){
    var retorno = null;
    await $.ajax({
            url: baseUrl + "fornecedores/verificador_exame/" + codigo_item_pedido + "/" + codigo_verificador + "/" ,
            type: "GET",
            dataType: "json", 
    }).done(function(data) {
        if(data == 0){
            retorno = setTimeout(() => { verificaUploadExame(codigo_item_pedido,codigo_verificador); }, 5000);
        }else if (data == 1){
            retorno = true;
        }
    })    
    return new Promise(resolve => {(resolve(retorno))});
}

function log_anexos(codigo_item_pedido_exame){
    var janela = window_sizes();
    window.open(baseUrl + "fornecedores/log_anexos/" + codigo_item_pedido_exame + "/AnexoExame/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
}

</script>
