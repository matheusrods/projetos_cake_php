<?php //debug($processos)?>

<?php if (!empty($processos)):?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-medium">Código</th>
            <th class="input-xlarge">Titulo</th>
            <th class="input-xlarge">Tipo</th>
            <th class="acoes" style="width:75px">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($processos as $dados): ?>
        <?php $razop = $dados['Processo']['codigo_processo_tipo'] == 1;?>
        <tr>
            <td class="input-mini"><?php echo $dados['Processo']['codigo'] ?>
            </td>
            <td class="input-xlarge"><?php echo $dados['Processo']['titulo'] ?>
            </td>
            <td class="input-xlarge"><?php echo $dados['ProcessoTipo']['descricao'] ?>
            </td>
            <td class="input-xlarge">
                <?php if (isset($dados['Processo']['ProcessoAnexos']) && !empty($dados['Processo']['ProcessoAnexos'])): ?>
                    <button type='button' class='btn btn-primary' onclick='modal_anexos("<?php echo $dados["Processo"]["codigo"] ?>")'>Anexos</button>
                <?php endif; ?>

                <button type="button" class="btn btn-default" onclick="modal_processos(<?php echo $dados['Processo']['codigo'] . ', ' . $razop; ?>)"><?= ($razop)? 'Hazops' : 'Etapas' ?></button>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Processo']['count']; ?>
            </td>
        </tr>
    </tfoot>
</table>

<div id="modal_processos" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_processos"
    aria-hidden="true" style="width: 800px; margin-left: -400px;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modal_processos_titulo"></h3>
    </div>
    <div class="modal-body">
        <table id="modal_processos_table" class="table table-striped">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Fechar</button>
    </div>
</div>

<div id="modal_razopsnos" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_razopsnos"
    aria-hidden="true" style="width: 800px; margin-left: -400px;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Hazops Nos</h3>
    </div>
    <div class="modal-body">
        <table id="modal_razopsnos_table" class="table table-striped">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Posição</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Fechar</button>
    </div>
</div>

<div id="modal_anexos" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_anexos"
    aria-hidden="true" style="width: 800px; margin-left: -400px;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Anexos do Processo</h3>
    </div>
    <div class="modal-body">
        <table id="modal_anexos_table" class="table table-striped">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Anexo</th>
                    <th>Data Cadastro</th>
                    <th>Data Remoção</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Fechar</button>
    </div>
</div>

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

<?php else:?>
<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php
echo $this->Js->writeBuffer();

echo $this->Javascript->codeBlock("
    function atualizaListaChamados() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'chamados/listagem/' + Math.random());
    }
");
?>

<script type="text/javascript">
    var dados;

    function modal_processos(codigo, razop) {
        jQuery.get('/portal/processos/modal_processos', {
                codigo: codigo,
                razop: razop
            }, function(data) {
                dados = data;

                if(razop){
                    razopProcesso(data);
                }else{
                    etapasProcesso(data);
                }

                jQuery("#modal_processos").css("z-index", "2000").modal('show');

            }, "json")
            .fail(function(jqXHR, textStatus, errorThrow) {
                swal("Atenção", textStatus, 'Erro ao consultar as etapas e hazops do processo');
            });
    }

    function etapasProcesso(data) {
        // Adicionando o titulo da modal
        jQuery("#modal_processos_titulo").html("Etapas do Processo");

        // populando o thead
        jQuery("#modal_processos_table thead").html("");
        var row = "<tr>" +
            "<th>Código</th>" +
            "<th>Descrição</th>" +
            "<th>Posição</th>" +
            "</tr>";
        jQuery("#modal_processos_table thead").append(row);

        // populando o tbody
        jQuery("#modal_processos_table tbody").html("");
        data.forEach(element => {
            var row = "<tr>" +
                "<td>" + element.ProcessoFerramenta.codigo + "</td>" +
                "<td>" + element.ProcessoFerramenta.descricao + "</td>" +
                "<td>" + element.ProcessoFerramenta.posicao + "</td>" +
                "</tr>";

            jQuery("#modal_processos_table tbody").append(row);
        });
    }

    function razopProcesso(data) {
        // Adicionando o titulo da modal
        jQuery("#modal_processos_titulo").html("Hazops do Processo");

        // populando o thead
        jQuery("#modal_processos_table thead").html("");
        var row = "<tr>" +
            "<th>Código</th>" +
            "<th>Descrição</th>" +
            "<th>Equipamento</th>" +
            "<th>Finalidade</th>" +
            "</tr>";
        jQuery("#modal_processos_table thead").append(row);

        // populando o tbody
        jQuery("#modal_processos_table tbody").html("");
        data.forEach(function(element, i) {
            var equipamentos = (element.ProcessoFerramenta.equipamentos != null) ? element.ProcessoFerramenta.equipamentos : "";
            var finalidades = (element.ProcessoFerramenta.finalidades != null) ? element.ProcessoFerramenta.finalidades : "";

            var row = "<tr>" +
                "<td>" + element.ProcessoFerramenta.codigo + "</td>" +
                "<td>" + element.ProcessoFerramenta.descricao + "</td>" +
                "<td>" + equipamentos + "</td>" +
                "<td>" + finalidades + "</td>" +
            "</tr>";

            jQuery("#modal_processos_table tbody").append(row);
        });
    }

    function modal_anexos(codigo){
        jQuery.get('/portal/processos/modal_anexos', {
            codigo_processo: codigo
        }, function(data) {
            jQuery("#modal_anexos_table tbody").html("");
            
            data.forEach(element => {
                var data_remocao = (element.ProcessoAnexo.data_remocao != null) ? element.ProcessoAnexo.data_remocao : "";

                var row = "<tr>" +
                    "<td>" + element.ProcessoAnexo.codigo + "</td>" +
                    "<td>" +
                        "<a href='" + element.ProcessoAnexo.arquivo_url + "' target='_blank'>" +
                            "<img width='50px' src='" + element.ProcessoAnexo.arquivo_url + "'>" +
                        "</a>" +
                    "</td>" +
                    "<td>" + element.ProcessoAnexo.data_inclusao + "</td>" +
                    "<td>" + data_remocao + "</td>" +
                    "</tr>";

                jQuery("#modal_anexos_table tbody").append(row);
            });
            
            jQuery("#modal_anexos").css("z-index", "2000").modal('show');

        }, "json")
        .fail(function(jqXHR, textStatus, errorThrow) {
            swal("Atenção", textStatus, 'Erro ao consultar os anexos do processo');
        });
    }

</script>