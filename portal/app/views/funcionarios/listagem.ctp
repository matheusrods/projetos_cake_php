<div class='actionbar-right margin-bottom-10'>
    <?php // echo $html->link('<i class="cus-page-white-excel"></i> Importar', array('controller' => 'funcionarios', 'action' => 'importar', $this->data['Cliente']['codigo'], $referencia), array('escape' => false, 'class' => 'btn', 'title' =>'Importar Funcionários'))
    ?>

    <?php
    ###########################################################################
    ###########################################################################
    ####################TRATAMENTO PARA A HOLDING MULTICLIENTE#################
    ###########################################################################
    ###########################################################################
    //para não deixar incluir um funcionario quando não tiver um codigo matriz
    if (!empty($codigo_matriz)) {
        echo $html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'funcionarios', 'action' => 'incluir', $codigo_matriz, $referencia, $terceiros_implantacao), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir Funcionários'));
    }
    ?>

</div>
<?php if (!empty($funcionarios)) : ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class='table table-striped tablesorter'>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Nascimento</th>
                <th>RG</th>
                <th>CPF</th>
                <th>Matrícula</th>
                <th>Sexo</th>
                <th>Data admissao</th>
                <th>Unidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 0; ?>
            <?php foreach ($funcionarios as $func) : ?>

                <?php $funcionario = $func[0]; ?>
                <tr>
                    <td><?php echo $funcionario['codigo_funcionario'] ?></td>
                    <td><?php echo $funcionario['nome'] ?><?php echo !empty($funcionario['nome_social']) && $funcionario['flg_nome_social'] ? ' - ' . $funcionario['nome_social'] : '' ?></td>
                    <td><?php echo $funcionario['data_nascimento'] ?></td>
                    <td><?php echo $funcionario['rg'] . " - " . $funcionario['rg_orgao'] ?></td>
                    <td><?php echo Comum::formatarDocumento($funcionario['cpf']); ?></td>
                    <td><?php echo $funcionario['matricula'] ?></td>
                    <td><?php echo ($funcionario['sexo'] == 'M') ? 'Masculino' : 'Feminino'; ?></td>
                    <td><?php echo $funcionario['admissao'] ?></td>
                    <td><?php echo $funcionario['nome_fantasia'] ?></td>
                    <td style="min-width: 60px;">

                        <?php echo $html->link('', array('action' => 'editar', $funcionario['codigo_funcionario'], $funcionario['codigo_cliente_matricula'], $referencia, $terceiros_implantacao), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
                        <a href="javascript:void(0);" onclick="window_log('<?php echo $funcionario['codigo_funcionario']; ?>');"><i class="icon-eye-open" title="Log Funcionário"></i></a>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ClienteFuncionario']['count']; ?></td>
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
<?php else : ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>

<?php if ($referencia != 'principal') : ?>
    <div class='form-actions well'>
        <?php echo $html->link('Voltar para implantação', array('controller' => 'clientes_implantacao', 'action' => 'estrutura', $codigo_cliente, $referencia, $terceiros_implantacao), array('class' => 'btn')); ?>
    </div>
<?php endif; ?>

<?php echo $this->Js->writeBuffer(); ?>

<?php
echo $this->Javascript->codeBlock("
    function atualizaStatus(codigo, status, codigo_cliente){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'clientes_funcionarios/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
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

    function window_log(codigo_funcionario)
    {
        var janela = window_sizes();
        window.open(baseUrl + 'funcionarios/listagem_log/' + codigo_funcionario + '/' + Math.random(), janela, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    }


    ");
?>