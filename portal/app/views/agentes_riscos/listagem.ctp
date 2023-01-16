<?php //debug($agentes_riscos)?>

<?php if (!empty($agentes_riscos)):?>
<?php //debug($agentes_riscos);echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-medium">Códigos</th>
            <th class="input-xxlarge">Riscos/impactos</th>
            <th class="input-xlarge">Perigos/aspectos</th>
            <th class="input-xlarge">Riscos tipo</th>
            <th class="input-xlarge">Código Cliente</th>
            <th class="acoes" style="width:75px">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($agentes_riscos as $dados): ?>
        <tr>
            <td class="input-mini"><?php echo $dados['AgentesRiscosClientes']['codigo_arrtpa_ri'] ?>
            </td>
            <td class="input-xxlarge"><?php echo $dados['RiscosImpactos']['descricao']; ?>
            </td>
            <td class="input-xlarge"><?php echo $dados['PerigosAspectos']['descricao']; ?>
            </td>
            <td class="input-xlarge"><?php echo $dados['RiscosTipo']['descricao']; ?>
            </td>
            <td class="input-xlarge"><?php echo $dados['AgentesRiscosClientes']['codigo_cliente']; ?>
            </td>
            <td>
                <a href="#agenteRiscoModal" role="button" data-toggle="modal" id="<?= $dados['AgentesRiscosClientes']['codigo_arrtpa_ri']?>" onclick="carregaModal(id)">
                    <span class="icon-eye-open" title="Desativado" style="margin-right: 5px"></span>
                    <input type="hidden" id="riscosTipo<?= $dados['AgentesRiscosClientes']['codigo_arrtpa_ri']?>" value="<?php echo $dados['RiscosTipo']['descricao']; ?>">
                    <input type="hidden" id="perigosAspectos<?= $dados['AgentesRiscosClientes']['codigo_arrtpa_ri']?>" value="<?php echo $dados['PerigosAspectos']['descricao']; ?>">
                    <input type="hidden" id="riscosImpactos<?= $dados['AgentesRiscosClientes']['codigo_arrtpa_ri']?>" value="<?php echo $dados['RiscosImpactos']['descricao']; ?>">
                </a>

            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['AgentesRiscosClientes']['count']; ?>
            </td>
        </tr>
    </tfoot>
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

<?php else:?>
<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>


<?php
echo $this->Js->writeBuffer();

echo $this->Javascript->codeBlock("
    function atualizaListaRiscosTipo() {   
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'agentes_riscos/listagem/' + Math.random());
    }
    
    function atualizaStatusRiscosTipo(codigo, descricao, cor, icone, status)
    {

        $.ajax({
            type: 'POST',
            url: baseUrl + 'agentes_riscos/editar_status/' + codigo + '/' + descricao + '/' + cor + '/' + icone + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){           
                if(data == 1){
                    atualizaListaRiscosTipo();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaRiscosTipo();
                    $('div.lista').unblock();
                    viewMensagem(0,'Não foi possível mudar o status!');
                }
            },
            error: function(erro){
                $('div.lista').unblock();
                viewMensagem(0,'Não foi possível mudar o status!');
            }
        });
    }
    
    function fecharMsg()
    {
        setInterval(
            function(){
                $('div.message.container').css({ 'opacity': '0', 'display': 'none' });
            },
            4000
        );     
    }
    
    function gerarMensagem(css, mens)
    {
        $('div.message.container').css({ 'opacity': '1', 'display': 'block' });
        $('div.message.container').html('<div class=\"alert alert-'+css+'\"><p>'+mens+'</p></div>');
        fecharMsg();
    }
    
    function viewMensagem(tipo, mensagem){
        switch(tipo){
            case 1:
                gerarMensagem('success',mensagem);
                break;
            case 2:
                gerarMensagem('success',mensagem);
                break;
            default:
                gerarMensagem('error',mensagem);
                break;
        }    
    }
");
?>

<!-- Modal -->
<div id="agenteRiscoModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="agenteRiscoModalLabel" aria-hidden="true" style="z-index: 1500">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="agenteRiscoModalLabel" style="text-decoration: none">Agente de Risco</h3>
    </div>
    <div class="modal-body">
        <div class="row-fluid" >
            <div class="span12" style="border-bottom: 1px solid #e8e5e5; padding-bottom: 7px">
                <h5 id="modalRiscosImpactos" style="margin: 0;padding-top: 0;"></h5>
                <small id="ModalMedidasControle" style="color: rgba(0,0,0,0.49)"></small>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12" style="border-bottom: 1px solid #e8e5e5;">
                <h5 style="margin-bottom: 5px">Tipo de Perigo</h5>
                <p id="modalRiscosTipo"></p>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12" style="border-bottom: 1px solid #e8e5e5;">
                <h5 style="margin-bottom: 0px">Perigos / Aspectos</h5>
                <p id="modalPerigosAspectos"></p>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12" >
                <h5 style="margin-bottom: 0px">Riscos / Impactos</h5>
                <p id="modalRiscosImpactos2"></p>
            </div>

            <div id="riscoAnexo" class="span12" style="border-bottom: 1px solid #e8e5e5;
            padding-bottom: 7px; margin-left: 0 !important;"></div>

        </div>

        <div class="row-fluid">
            <div class="span12" >
                <h5 style="margin-bottom: 0px">Medidas de Controle</h5>
                <p id="modalMedidasControle2"></p>
            </div>

            <div id="ModalMedidasControleAnexos" class="span12" style="padding-bottom: 7px; margin-left: 0 !important;">
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
    </div>
</div>

<script>
    function carregaModal(id){

        $("#riscoAnexo, #ModalMedidasControleAnexos").text('');

        $.ajax({
            type: 'POST',
            url: baseUrl + 'agentes_riscos/carregar_agente_risco/' + id,

            success: function(data){

                $("#modalRiscosTipo").text($( "#riscosTipo"+id).val());
                $("#modalPerigosAspectos").text($( "#perigosAspectos"+id).val());
                $("#modalRiscosImpactos, #modalRiscosImpactos2").text($( "#riscosImpactos"+id).val());

                let objJson = JSON.parse(data);
                let qtd = objJson.length;

                let titulo = '';
                let riscoAnexo = ''
                let medidaControleAnexo = ''

                for (var i=0;i < qtd;i++) {

                    //Defini Medidas de controle
                    if (objJson[i].MedidasControle) {
                        if (i !== (qtd - 1)) {
                            titulo += objJson[i].MedidasControle.titulo + ', ';
                        } else {
                            titulo += objJson[i].MedidasControle.titulo;
                        }

                        //Verifica quantidade de fotos para medidas de controle
                        let qtdMca = objJson[i].MedidasControle.MedidasControleAnexos.length;

                        if (qtdMca > 0) {

                            for (var m=0;m < qtdMca;m++) {

                                medidaControleAnexo += `<img data-src="holder.js/64x64" alt="614x64" src="${objJson[i].MedidasControle.MedidasControleAnexos[m].arquivo_url}" style="width: 64px; height: 64px;padding-right: 4px">`;
                            }
                        }
                    }

                    //Defini Fotos de agentes de riscos
                    if (objJson[i].RiscosImpactosAnexos) {

                        if (objJson[i].RiscosImpactosAnexos.arquivo_url) {
                            riscoAnexo += `<img data-src="holder.js/64x64" alt="64x64" src="${objJson[i].RiscosImpactosAnexos.arquivo_url}" style="width: 64px; height: 64px;padding-right: 4px">`;
                        }
                    }
                }

                $("#riscoAnexo").append(riscoAnexo);
                $("#ModalMedidasControleAnexos").append(medidaControleAnexo);

                $("#ModalMedidasControle, #modalMedidasControle2").text(titulo);

            },
            error: function(erro){

                viewMensagem(0,'Não foi possível carregar os dados!');
            }
        });


    }
</script>
