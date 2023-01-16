<?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-small"><?= $this->Paginator->sort('COD. Cliente', 'codigo_cliente_unidade') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Nome Fantasia', 'nome_fantasia') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Setor', 'Setor.descricao') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Tipo Ação', 'TipoAcao.descricao') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Status', 'CronogramaGestaoPcmso.status') ?></th>
            <th style='width:75px'>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $d): ?>
        <tr>
            <td><?= $d[0]['codigo_cliente_unidade'] ?></td>
            <td><?= $d[0]['nome_fantasia'] ?></td>
            <td><?= $d[0]['setor'] ?></td>
            <td><?= $d[0]['tipo_acao'] ?></td>
            <td>
                <?php if($d[0]['status'] == 'CONCLUIDO'): ?>
                    <span class="badge badge-empty badge-success" title="Concluido"><?=ucfirst($d[0]['status'])?></span>
                <?php elseif($d[0]['status'] == 'CANCELADO'): ?>
                    <span class="badge badge-empty badge-important" title="Cancelado"><?=ucfirst($d[0]['status'])?></span>
                <?php elseif($d[0]['status'] == 'PENDENTE'): ?>
                    <span class="badge badge-empty badge-info" title="pendente"><?=ucfirst($d[0]['status'])?></span>
                <?php else: ?>
                    <span class="badge-empty badge badge-warning" title="DESCONHECIDO"><?=ucfirst($d[0]['status'])?></span>
                <?php endif; ?>
            </td> 
            <td>
                <?php if($d[0]['status'] == 'PENDENTE') : ?>
                    <a href="javascript:void(0);" onclick="gcp_modal_concluir_show('<?=$d[0]['codigo']?>', this);"><i class="icon-ok" title="CONCLUIR cronograma de ação"></i></a>
                    <a href="javascript:void(0);" onclick="gcp_modal_cancelar_show('<?=$d[0]['codigo']?>', this);"><i class="icon-remove" title="CANCELAR cronograma de ação"></i></a>
                <?php else : ?>
                    <a href="javascript:void(0);" onclick="gcp_modal_info_show(this, '<?=($d[0]['data_conclusao'] ? date("d/m/Y", strtotime($d[0]['data_conclusao'])) : '')?>', '<?=addslashes($d[0]['motivo_cancelamento'])?>', '<?=date("d/m/Y H:i:s", strtotime($d[0]['data_inclusao']))?>');"><i class="icon-eye-open" title="Visualizar Informações"></i></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
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

<div class="modal fade" id="modal_gcp_conluir" data-backdrop="static">
    <div class="modal-dialog modal-sm" style="position: static;">
        <div class="modal-content" id="modal_data">
            <div class="modal-header" style="text-align: center;">
                <h3>CONCLUIR cronograma de ação</h3>
            </div>

            <div class="modal-body" style="min-height: 150px;">

                <div style="float: left;width: 200px;">
                    <span style="font-size: 1.2em">
                        <b>Setor:</b> <span class="setor"></span>
                    </span>
                </div>

                <div>
                    <span style="font-size: 1.2em">
                        <b>Tipo Ação:</b> <span class="tipo_acao"></span>
                    </span>
                </div>
                <br /><br />
                <span style="font-size: 1.2em">
                    <b>Data Conclusão:</b>
                    <?php echo $this->BForm->input('CronogramaGestaoPcmso.codigo_cronograma_acao', array('type' => 'hidden')); ?>
                    <?php echo $this->BForm->input('CronogramaGestaoPcmso.data_conclusao', array('type' => 'text', 'label' => '', 'class' => 'data input-small')) ?>
			    </span>

            </div>

            <div class="modal-footer">
                <div class="right">
                    <a href="javascript:void(0);" onclick="jQuery('#modal_gcp_conluir').modal('hide')" class="btn btn-danger">Fechar</a>
                    <a href="javascript:void(0);" onclick="return gcp_modal_concluir_submit(this)" class="btn btn-success">CONCLUIR</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_gcp_cancelar" data-backdrop="static">
    <div class="modal-dialog modal-sm" style="position: static;">
        <div class="modal-content" id="modal_data">
            <div class="modal-header" style="text-align: center;">
                <h3>CANCELAR cronograma de ação</h3>
            </div>

            <div class="modal-body" style="min-height: 150px;">

                <div style="float: left;width: 200px;">
                    <span style="font-size: 1.2em">
                        <b>Setor:</b> <span class="setor"></span>
                    </span>
                </div>

                <div>
                    <span style="font-size: 1.2em">
                        <b>Tipo Ação:</b> <span class="tipo_acao"></span>
                    </span>
                </div>
                <br /><br />
                <span style="font-size: 1.2em">
                    <b>Motivo Cancelamento:</b>
                    <?php echo $this->BForm->input('CronogramaGestaoPcmso.codigo_cronograma_acao', array('type' => 'hidden')); ?>
                    <?php echo $this->BForm->input('CronogramaGestaoPcmso.motivo_cancelamento', array('type' => 'textarea', 'label' => '')) ?>
			    </span>

            </div>

            <div class="modal-footer">
                <div class="right">
                    <a href="javascript:void(0);" onclick="jQuery('#modal_gcp_cancelar').modal('hide')" class="btn btn-danger">Fechar</a>
                    <a href="javascript:void(0);" onclick="return gcp_modal_cancelar_submit(this)" class="btn btn-success">CONCLUIR</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_gcp_info" data-backdrop="static">
    <div class="modal-dialog modal-sm" style="position: static;">
        <div class="modal-content" id="modal_data">
            <div class="modal-header" style="text-align: center;">
                <h3>Informações</h3>
            </div>

            <div class="modal-body" style="min-height: 150px;">

                <table class="table">
                    <tr>
                        <td colspan="3"><b>Data Inclusão:</b> <span class="data_inclusao"></span></td>
                    </tr>
                    <tr>
                        <td><b>Setor:</b> <span class="setor"></span></td>
                        <td><b>Tipo Ação:</b> <span class="tipo_acao"></span></td>
                        <td><b>Data Conclusão:</b> <span class="data_conclusao"></span></td>
                    </tr>
                    <tr>
                        <td><b>Motivo Cancelamento:</b></td>
                        <td colspan="2"><span class="motivo_cancelamento"></span></td>
                    </tr>
                </table>

            </div>

            <div class="modal-footer">
                <div class="right">
                    <a href="javascript:void(0);" onclick="jQuery('#modal_gcp_info').modal('hide')" class="btn btn-danger">Fechar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() { setup_datepicker(); });
    function gcp_modal_concluir_show(codigo_cronograma_acao, a_element){
        let setor = jQuery(a_element).parent().parent().find('td:eq(2)').text();
        let ta = jQuery(a_element).parent().parent().find('td:eq(3)').text();

        jQuery("#modal_gcp_conluir span.setor").empty().text(setor);
        jQuery("#modal_gcp_conluir span.tipo_acao").empty().text(ta);
        jQuery("#modal_gcp_conluir #CronogramaGestaoPcmsoCodigoCronogramaAcao").val(codigo_cronograma_acao);


        jQuery("#modal_gcp_conluir").modal("show");
    }
    function gcp_modal_cancelar_show(codigo_cronograma_acao, a_element){
        let setor = jQuery(a_element).parent().parent().find('td:eq(2)').text();
        let ta = jQuery(a_element).parent().parent().find('td:eq(3)').text();

        jQuery("#modal_gcp_cancelar span.setor").empty().text(setor);
        jQuery("#modal_gcp_cancelar span.tipo_acao").empty().text(ta);
        jQuery("#modal_gcp_cancelar #CronogramaGestaoPcmsoCodigoCronogramaAcao").val(codigo_cronograma_acao);

        jQuery("#modal_gcp_cancelar").modal("show");
    }
    function gcp_modal_concluir_submit(a_element){
        bloquearDiv(jQuery(a_element).parent());
        let modal_id = "modal_gcp_conluir";
        let data_object = {
            acao: 'concluir',
            codigo_cronograma_acao: jQuery("#"+modal_id+" #CronogramaGestaoPcmsoCodigoCronogramaAcao").val(),
            data_conclusao: jQuery("#"+modal_id+" #CronogramaGestaoPcmsoDataConclusao").val(),
        };
        $.ajax({
            url: baseUrl + 'clientes_implantacao/gestao_cronograma_pcmso_store',
            type: 'POST',
            dataType: 'json',
            data: data_object,
        })
        .done(function(data){
            swal({type: data.status,title: 'Atenção',text: data.message});
        })
        .fail(function(){
            swal({type: 'error',title: 'Atenção',text: 'ERROR - Não foi possivel concluir o cronograma de ação PCMSO!'});
        })
        .always(function(){
            atualizaListaGestaoCronogramaPcmso();
            jQuery('#modal_gcp_conluir').modal('hide');
        });
    }
    function gcp_modal_cancelar_submit(a_element){
        bloquearDiv(jQuery(a_element).parent());
        let modal_id = "modal_gcp_cancelar";
        let data_object = {
            acao: 'cancelar',
            codigo_cronograma_acao: jQuery("#"+modal_id+" #CronogramaGestaoPcmsoCodigoCronogramaAcao").val(),
            motivo_cancelamento: jQuery("#"+modal_id+" #CronogramaGestaoPcmsoMotivoCancelamento").val(),
        };
        $.ajax({
            url: baseUrl + 'clientes_implantacao/gestao_cronograma_pcmso_store',
            type: 'POST',
            dataType: 'json',
            data: data_object,
        })
        .done(function(data){
            swal({type: data.status, title: 'Atenção', text: data.message});
        })
        .fail(function(){
            swal({type: 'error', title: 'Atenção', text: 'ERROR - Não foi possivel cancelar o cronograma de ação PCMSO!'});
        })
        .always(function(){
            atualizaListaGestaoCronogramaPcmso();
            jQuery('#modal_gcp_cancelar').modal('hide');
        });
    }
    function gcp_modal_info_show(a_element, data_conclusao, motivo_cancelamento, data_inclusao){
        let modal_id = "modal_gcp_info";
        let setor = jQuery(a_element).parent().parent().find('td:eq(2)').text();
        let ta = jQuery(a_element).parent().parent().find('td:eq(3)').text();

        jQuery("#"+modal_id+" span.setor").empty().text(setor);
        jQuery("#"+modal_id+" span.tipo_acao").empty().text(ta);
        jQuery("#"+modal_id+" span.data_inclusao").empty().text(data_inclusao);
        jQuery("#"+modal_id+" span.data_conclusao").empty().text(data_conclusao);
        jQuery("#"+modal_id+" span.motivo_cancelamento").empty().text(motivo_cancelamento);

        jQuery("#modal_gcp_info").modal("show");
    }
</script>