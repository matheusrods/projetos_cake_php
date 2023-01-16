<?php if (!empty($acoes_melhorias)):?>
    
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <?php echo $this->BForm->hidden('codigo_is_admin', array('value' => $is_admin)); ?>
    
    <div id="tabela_select_responsavel_container" style="overflow-x: auto;">
        <table class="table table-striped tabela_select_responsavel" style="max-width: 200% !important; width: 200% !important;">
            <thead>
            <tr>
                <th><input type="checkbox" class="responsavel_select_all"></th>
                <th style="width: 70px">ID da ação</th>
                <th>Razão Social</th>
                <th style="width: 100px">Nome Fantasia</th>
                <th style="width: 95px">Status da ação</th>
                <th style="width: 95px">Tipo da ação</th>
                <th style="width: 95px">Criticidade</th>
                <th style="width: 100px">Registrado em</th>
                <th style="width: 100px">Identificado por</th>
                <th style="width: 130px">Local da observação</th>
                <th>Origem</th>
                <th>Descrição do desvio</th>
                <th>Descrição da ação</th>
                <th>Local da ação</th>
                <th>Responsável</th>
                <th>Prazo</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($acoes_melhorias as $cliente) :?>
                <tr>
                    <th><input type="checkbox" class="selecionado" data-codigo="<?= $cliente["AcoesMelhorias"]["codigo"] ?>"
                            <?php
                            if ($cliente[0]["solicitacoes_status"] == 1 || $cliente["AcoesMelhorias"]["codigo_acoes_melhorias_status"] > 4) {
                                echo "disabled='disabled'";
                            }
                            ?>
                        ></th>
                    <td><?= $cliente["AcoesMelhorias"]["codigo"] ?></td>
                    <td><?= $cliente["Cliente"]["razao_social"] ?></td>
                    <td><?= $cliente["Cliente"]["nome_fantasia"] ?></td>
                    <td><?= $cliente["AcoesMelhoriasStatus"]["descricao"] ?></td>
                    <td><?= $cliente["AcoesMelhoriasTipo"]["descricao"] ?></td>
                    <td><?= $cliente["PosCriticidade"]["descricao"] ?></td>
                    <td><?= $cliente["AcoesMelhorias"]["data_inclusao"] ?></td>
                    <td><?= $cliente["IdentificadoPor"]["nome"] ?></td>
                    <td><?= $cliente["Cliente"]["nome_fantasia"] ?></td>
                    <td><?= $cliente["OrigemFerramenta"]["descricao"] ?></td>
                    <td><?= $cliente["AcoesMelhorias"]["descricao_desvio"] ?></td>
                    <td><?= $cliente["AcoesMelhorias"]["descricao_acao"] ?></td>
                    <td><?= $cliente["Cliente"]["razao_social"] ?></td>
                    <td><?php echo !empty($cliente["Responsavel"]["nome"]) ? $cliente["Responsavel"]["nome"] : $cliente["UsuarioSolicitacao"]["nome"]; ?></td>
                    <td><?= $cliente["AcoesMelhorias"]["prazo"] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "16"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['AcoesMelhorias']['count']; ?></td>
                </tr>
            </tfoot>
        </table>

        <div class='row-fluid'>
            <div class='numbers span12'>
                <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
                <?php echo $this->Paginator->numbers(); ?>
                <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
            </div>
            <div class='counter span6'>
                <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            </div>
        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<style>
    h3 {
        text-decoration: none;
    }
</style>


<script>
    $( function() {

        function atualizaListaRiscosImpactos()
        {   
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'riscos_impactos/listagem/' + Math.random());
        }
        
        function atualizaStatusRiscosImpactos(codigo, codigo_perigo_aspecto, descricao, status, codigo_cliente, codigo_metodo_tipo, codigo_risco_impacto_tipo)
        {
    
            $.ajax({
                type: 'POST',
                url: baseUrl + 'riscos_impactos/editar_status/' + codigo + '/' + codigo_perigo_aspecto + '/' + descricao + '/' + status + '/' + codigo_cliente + '/' + codigo_metodo_tipo + '/' + codigo_risco_impacto_tipo + '/' + Math.random(),
                beforeSend: function(){
                    bloquearDivSemImg($('div.lista'));  
                },
                success: function(data){
                
                    if(data == 1){
                        atualizaListaRiscosImpactos();
                        $('div.lista').unblock();
                        viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                    } else {
                        atualizaListaRiscosImpactos();
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
    });
</script>
