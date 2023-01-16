<?php if (!empty($aplicacao_exame)) : ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-xlarge">Setor</th>
                <th class="input-xlarge">Cargo</th>
                <th class="input-xlarge">Funcionário</th>
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($aplicacao_exame as $dados) : ?>
                <tr>
                    <td class="input-xlarge"><?php echo $dados['Setor']['descricao']; ?></td>
                    <td class="input-xlarge"><?php echo $dados['Cargo']['descricao']; ?></td>
                    <td class="input-medium"><?php echo empty($dados['Funcionario']['nome']) ? '' : $dados['Funcionario']['nome']; ?></td>
                    <td>
                        <?php $view_codigo_funcionario = ($dados['AplicacaoExame']['codigo_funcionario'] ? $dados['AplicacaoExame']['codigo_funcionario'] : 'null'); ?>
                        <?php $view_codigo_ghe = ($dados['AplicacaoExame']['codigo_grupo_homogeneo_exame'] ? $dados['AplicacaoExame']['codigo_grupo_homogeneo_exame'] : 'null'); ?>

                        <?php echo $this->Html->link('', array('action' => 'editar', $dados['AplicacaoExame']['codigo_cliente_alocacao'], $dados['AplicacaoExame']['codigo_setor'], $dados['AplicacaoExame']['codigo_cargo'], $view_codigo_funcionario, $view_codigo_ghe), array('class' => 'icon-wrench', 'title' => 'Vincular Exames')); ?>&nbsp;&nbsp;

                        <?php echo $this->Html->link('', '#', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirGrupoExposicaoRisco(' . $dados['AplicacaoExame']['codigo_cliente_alocacao'] . ', ' . $dados['AplicacaoExame']['codigo_setor'] . ', ' . $dados['AplicacaoExame']['codigo_cargo'] . ', ' . (!empty($dados['AplicacaoExame']['codigo_funcionario']) ? $dados['AplicacaoExame']['codigo_funcionario'] : 0) . ', this)')); ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10"><strong>Total: </strong> <span class="js-total"><?php echo count($aplicacao_exame); ?></span></td>
            </tr>
        </tfoot>
    </table>

    <?php if ($this->Paginator->params['paging']['AplicacaoExame']['pageCount'] > 1) : ?>
        <div class='row-fluid'>
            <div class='numbers span6'>
                <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
                <?php echo $this->Paginator->numbers(); ?>
                <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
            </div>
            <div class='counter span7'>
                <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>

            </div>
        </div>
    <?php endif; ?>

<?php else : ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>

<div class='form-actions well'>
    <?php if (!is_null($ordemServico) && isset($ordemServico['OrdemServico']['status_ordem_servico']) && $ordemServico['OrdemServico']['status_ordem_servico'] != 5) { ?>

        <?php echo $html->link('Concluir', '#', array('data-codigo' => $dados_cliente['Unidade']['codigo'], 'class' => 'js-concluir btn btn-primary')); ?>

    <?php } elseif (!is_null($ordemServico) && isset($ordemServico['OrdemServico']['status_ordem_servico']) && $ordemServico['OrdemServico']['status_ordem_servico'] == 5) { ?>

        <?php echo $html->link(
            'Desfazer',
            array(
                'controller' => 'clientes_implantacao',
                'action' => 'desfazer_status_pcmso',
                $dados_cliente['Unidade']['codigo'],
                1
            ),
            array(
                'class' => 'btn btn-danger pull-left margin-right-10',
                'data-toggle' => 'tooltip',
                'title' => 'Cancela o status de concluído.'
            )
        );
        ?>

        <?php //echo $this->BForm->submit('Concluído', array('type' => 'button', 'class' => 'btn btn-primary pull-left margin-right-10', 'data-toggle' => 'tooltip', 'title' => 'O processo já está concluído', 'disabled' => true)); 
        ?>

    <?php } else { ?>

        <?php echo $this->Html->link('Localizar Credenciado', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado', $codigo_cliente_alocacao, 2340), array('class' => 'btn')); ?>
    <?php } ?>

    <?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_pcmso', $dados_cliente['Matriz']['codigo']), array('class' => 'btn')); ?>

    <?php if (!is_null($ordemServico) && isset($ordemServico['OrdemServico']['status_ordem_servico']) && $ordemServico['OrdemServico']['status_ordem_servico'] == 5) { ?>

        <?php
        echo $html->link('Finalizar Processo', '#', array('data-codigo' => $dados_cliente['Unidade']['codigo'], 'class' => 'js-finalizar-processo btn btn-success'));

        echo $html->link(
            'Preencher com exame clínico',
            '#',
            array(
                'data-href' => Router::url(
                    array(
                        'controller' => 'aplicacao_exames',
                        'action' => 'preenche_com_exame_clinico',
                        $codigo_cliente_alocacao
                    )
                ),
                'class' => 'btn btn-warning pull-right submit-load',
                'escape' => false,
                'data-toggle' => 'tooltip',
                'title' => 'Aplica automaticamente o Exame Clinico para os setores e cargos que não possuem PCMSO definido.'
            )
        );
        ?>

    <?php } ?>
</div>
<div id="myModal" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Concluir PCMSO</h3>
    </div>
    <div class="modal-body">
        <label for="inicio_vigencia_pcmso">Insira a data de início de vigência:</label>
        <input type="text" id="inicio_vigencia_pcmso" class="data input-small" name="inicio_vigencia_pcmso">
        <br>
        <label for="vigencia_em_meses">Selecione a vigência do contrato (em meses):</label>
        <select id="vigencia_em_meses" name="vigencia_em_meses" class="input-small">
            <option value="">Selecione</option>
            <option value="3">3</option>
            <option value="6">6</option>
            <option value="9">9</option>
            <option value="12">12</option>
            <option value="24">24</option>
        </select>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <a href="#" class="js-salvar btn btn-primary">Salvar</a>
    </div>
</div>

<!-- array('controller' => 'clientes_implantacao','action' => 'atualiza_status_pcmso',  $dados_cliente['Unidade']['codigo'], 3) -->

<script type="text/javascript">
    $(document).ready(function() {
        setup_datepicker();
        $('.js-finalizar-processo').click(function(event) {
            $('input#codigo').val($(this).attr('data-codigo'));
            $('#myModal').modal('show');

            $('.js-salvar').click(function(event) {
                var execute = true;
                console.log($('#inicio_vigencia_pcmso').val().replace(/\//g, '-'));
                $(this).parents('#myModal').find('input, select').each(function(index, val) {
                    if (val.value == '') {
                        $(this).css({
                            borderColor: 'red'
                        });
                        execute = false;
                    } else {
                        $(this).removeAttr('style');
                    }
                });
                if (execute) {
                    window.location = baseUrl + 'clientes_implantacao/atualiza_status_pcmso/<?= $dados_cliente['Unidade']['codigo'] ?>/3/' + $('#inicio_vigencia_pcmso').val().replace(/\//g, '-') + '/' + $('#vigencia_em_meses').val() + '/<?= $dados['AplicacaoExame']['codigo_cliente_alocacao'] ?>';
                }
            });
        }); //FINAL CLICK js-finalizar-processo

        $('.js-concluir').click(function() {

            var data = <?= $dados_cliente['Unidade']['codigo'] ?>;

            $.post(baseUrl + 'aplicacao_exames/concluir/' + data, function(r) {
                if (r.ret === 'ok') {

                    swal({
                        type: 'success',
                        title: 'Sucesso',
                        text: r.msg
                    }, function() {
                        window.location = baseUrl + 'aplicacao_exames/index/<?= $dados_cliente['Unidade']['codigo'] ?>/<?= $dados['AplicacaoExame']['codigo_cliente_alocacao'] ?>';

                    });
                } else if (r.ret === 'error') {
                    swal({
                        type: 'danger',
                        title: 'Error',
                        text: r.msg
                    });
                    return false;
                }
            }, 'json');
        });
    });
</script>

<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function() {
        $('[data-toggle=\"tooltip\"]').tooltip();
        $('.submit-load').click(function(event) {
            var este = $(this);
            var width = este.width();
            var link = este.attr('data-href');
            swal({
                type: 'warning',
                title: 'Atenção',
                text: 'Tem certeza que deseja aplicar o Exame Clínico a todos os setores e cargos que não possuem PCMSO definido?',
                showCancelButton: true,
                confirmButtonText: 'Sim',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false,
                confirmButtonColor: '#5783db',
                showLoaderOnConfirm: true
            }, function() {
                este.css({'width': width, cursor: 'wait'}).removeAttr('onclick').html($('<img>', {src: baseUrl + 'img/loading.gif'}));
                window.location.href = link;
            });
        });
    });

    function atualizaStatus(codigo, status, codigo_cliente){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'grupos_exposicao/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaLista(codigo_cliente);
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaLista(codigo_cliente);
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
    
    function fecharMsg(){
        setInterval(
        function(){
            $('div.message.container').css({ 'opacity': '0', 'display': 'none' });
        },
        4000
        );     
    }
    
    function gerarMensagem(css, mens){
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

    function atualizaLista(codigo_cliente) {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'grupos_exposicao/listagem/'+ codigo_cliente +'/' + Math.random());
    }
    
    function excluirGrupoExposicaoRisco(codigo_cliente_alocacao, codigo_setor, codigo_cargo, codigo_funcionario, elemento){  
        swal({
            type: 'warning',
            title: 'Atenção',
            text: 'Atenção: a exclusão deste dado implica na exclusão de todas as aplicações de exames vinculadas a este setor/cargo. Tem certeza que deseja continuar?',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function() {

            $.ajax({
                type: 'POST',        
                url: baseUrl + 'aplicacao_exames/excluir_exame_por_ajax',
                data: {codigo_cliente_alocacao: codigo_cliente_alocacao, codigo_setor: codigo_setor, codigo_cargo: codigo_cargo, codigo_funcionario: codigo_funcionario},
                dataType : 'json',
                success : function(data){ 
                    console.log(data);
                    if(data){
                        swal({
                            type: 'success',
                            title: 'Sucesso',
                            text: 'Os dados foram excluídos com sucesso.'
                        });
                        $(elemento).parents('tr').remove();
                        $('.js-total').text( parseInt($('.js-total').text()) - 1 );
                    }
                    else{
                        swal({
                            type: 'warning',
                            title: 'Atenção',
                            text: 'Os dados não puderam ser excluídos. Tente novamente'
                        });
                    }
                },
                error : function(){
                    swal({
                        type: 'warning',
                        title: 'Atenção',
                        text: 'Os dados não puderam ser excluídos. Tente novamente'
                    });
                }
            });
        });
    }
    ");
?>


<?php if (!empty($visualizar_gae) && $visualizar_gae) : ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery(".icon-trash, .js-concluir").remove();
            jQuery(".icon-wrench").attr("data-original-title", "Visualizar");
            jQuery(".icon-wrench").addClass("icon-eye-open").removeClass("icon-wrench");
        });
    </script>
<?php endif; ?>