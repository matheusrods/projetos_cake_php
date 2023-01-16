<?php if(!empty($anexo_digitalizacao)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código do upload</th>
                <th>Tipo de Digitalização</th>
                <th>Nome do documento</th>
                <th>Usuário inclusão</th>
                <th>Data inclusão</th>
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($anexo_digitalizacao as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['AnexoDigitalizacao']['codigo'] ?></td>
                <td><?php echo $dados['TipoDigitalizacao']['descricao'] ?></td>
                <td><?php echo $dados['AnexoDigitalizacao']['nome'] ?></td>
                <td><?php echo $dados['UsuarioInclusao']['nome'] ?></td>
                <td><?php echo $dados['AnexoDigitalizacao']['data_inclusao'] ?></td>
                <td>
                    <?php if(!empty($dados['AnexoDigitalizacao']['caminho_arquivo'])): ?>

                        <a href="https://api.rhhealth.com.br<?php echo $dados['AnexoDigitalizacao']['caminho_arquivo']; ?>" target="_blank" class="icon-file btn-anexos visualiza_anexo" title='Visualizar Digitalização'></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['AnexoDigitalizacao']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
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
    <?php echo $this->Js->writeBuffer(); ?>
    <?php 
echo $this->Javascript->codeBlock("

    function atualizaStatus(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'tipo_digitalizacao/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaLista();
                    $('div.lista').unblock();
                    viewMensagem(1,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaLista();
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

    function atualizaLista() {
        var codigo_cliente = $('#AtribuicaoCodigoCliente').val();
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'atribuicoes/listagem/' +codigo_cliente + '/' + Math.random());
    }
");
?>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    