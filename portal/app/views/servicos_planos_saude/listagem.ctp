<?php if(!empty($servicos)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-small">Código</th>
            <th class="input-xxlarge">Descrição</th>
            <th class="input-medium">Código Personalizado</th>
            <th class="input-medium">Tipo de Serviço</th>            
            <th class="input-small">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicos as $servico): ?>
            <tr>
                <td class="input-small"><?php echo $servico['Servico']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $servico['Servico']['descricao'] ?></td>
                <td class="input-medium"><?php echo $servico['Servico']['codigo_externo'] ?></td>
                <?php switch($servico['Servico']['tipo_servico']): 
                        case 'E':
                            $tipo_servico = 'Exames Complementares';
                            break;
                        case 'G':
                            $tipo_servico = 'Engenharia';
                            break;
                        case 'C':
                            $tipo_servico = 'Consultorias e Palestras';
                            break;
                        case 'S':
                            $tipo_servico = 'Saúde';
                            break;
                    endswitch; ?>
                <td class="input-medium"><?php echo $tipo_servico;?></td>
                <td>
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusServico('{$servico['Servico']['codigo']}','{$servico['Servico']['ativo']}')"));?>

                <?php if($servico['Servico']['ativo']== 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                <?php elseif($servico['Servico']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif; ?>
                
                <?php echo $this->Html->link('', array('action' => 'editar', $servico['Servico']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                
                <?php //echo $this->Html->link('', array('controller' => 'servicos', 'action' => 'excluir', $servico['Servico']['codigo']), array('escape' => false, 'class' => 'icon-trash', 'title' => 'Excluir'), "Deseja realmente excluir esse serviço?"); ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Servico']['count']; ?></td>
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
$this->addScript($this->Buonny->link_js('comum.js'));
echo $this->Javascript->codeBlock("
    function atualizaStatusServico(codigo, status){
    $.ajax({
        type: 'POST',
        url: baseUrl + 'servicos/editar_status_servicos/' + codigo + '/' + status + '/' + Math.random(),
        beforeSend: function(){
            bloquearDivSemImg($('div.lista'));  
        },
        success: function(data){
            if(data == 1){
                atualizaListaServicos();
                $('div.lista').unblock();
                viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
            } else {
                atualizaListaServicos();
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
"
);
?>
<?php echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    