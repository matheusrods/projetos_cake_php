<?php if(!empty($funcionarios)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

        <table class='table table-striped tablesorter'>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Nascimento</th>
                    <th>RG</th>
                    <th>CPF</th>
                    <th>Sexo</th>
                    <th>Data de Admissão</th>
                    <th>Setor</th>
                    <th>Cargo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($funcionarios as $funcionario): ?>
                <tr>
                    <td><?php echo $funcionario['Funcionario']['codigo'] ?></td>
                    <td><?php echo $funcionario['Funcionario']['nome'] ?></td>
                    <td><?php echo $funcionario['Funcionario']['data_nascimento'] ?></td>
                    <td><?php echo $funcionario['Funcionario']['rg']." - ".$funcionario['Funcionario']['rg_orgao'] ?></td>
                    <td><?php echo Comum::formatarDocumento($funcionario['Funcionario']['cpf']); ?></td>
                    <td><?php echo ($funcionario['Funcionario']['sexo'] == 'M') ? 'Masculino' : 'Feminino'; ?></td>
                    <td><?php echo $funcionario['ClienteFuncionario']['admissao'] ?></td>
                    <td><?php echo $funcionario['Setor']['descricao'] ?></td>
                    <td><?php echo $funcionario['Cargo']['descricao'] ?></td>
                    <td>
                    	<?php echo $html->link('', array('action' => 'imprimir_relatorio', $funcionario['Funcionario']['codigo']), array('class' => 'icon-print', 'data-toggle' => 'tooltip', 'title' => 'Relatório PPP')) ?>
                    </td>
                </tr>
                <?php endforeach; ?> 
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Funcionario']['count']; ?></td>
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

<?php if($referencia != 'principal') : ?>
<div class='form-actions well'>
    <?php echo $html->link('Voltar para Lista de Unidades', array('controller' => 'clientes', 'action' => 'index_unidades', $matriz['GrupoEconomicoCliente']['matriz'], $referencia,'funcionarios'), array('class' => 'btn')); ?>
</div>
<?php endif; ?>



<?php echo $this->Js->writeBuffer(); ?>

<?php 
    echo $this->Javascript->codeBlock("
        $(document).ready(function() {
            $('[data-toggle=\"tooltip\"]').tooltip();
        });
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

    function atualizaLista(codigo_cliente) {
    var div = jQuery('div.lista');
    bloquearDiv(div);
    div.load(baseUrl + 'funcionarios/listagem/'+ codigo_cliente + '/".$referencia."/' + Math.random());
}
");
?>