<?php if(!empty($epi)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código</th>
                <th class="input-xxlarge">Descrição</th>
                <th class="input-large">Ca</th>
                <th class="input-large">Validade</th>
                <th class="input-large">Código Externo</th>
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($epi as $dados): ?>
            <?php 
                $data_validade = '';
                if(!empty($dados['Epi']['data_validade_ca'])) $data_validade = reset((explode(' ', $dados['Epi']['data_validade_ca'])));
            ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Epi']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['Epi']['nome'] ?></td>
                <td class="input-large"><?php echo $dados['Epi']['numero_ca'] ?></td>
                <td class="input-large"><?php echo $this->Buonny->retorna_aviso_se_data_menor($data_validade); ?></td>
                <td class="input-large"><?php echo $dados['EpiExterno']['codigo_externo'] ?></td>
                <td>    
                    <?php if($dados['Epi']['ativo']== 0): ?>
                        <span class="badge-empty badge badge-important" title="Desativado"></span>
                    <?php elseif($dados['Epi']['ativo']== 1): ?>
                        <span class="badge-empty badge badge-success" title="Ativo"></span>
                    <?php endif; ?>
                
                    <?php echo $this->Html->link('', array('action' => 'editar_externo',  $codigo_cliente_filtro, $dados['Epi']['codigo'], $dados['EpiExterno']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar Código Externo')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Epi']['count']; ?></td>
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

    function atualizaStatus(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'epi/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaLista();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
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

    function atualizaLista(){
    var div = jQuery('div.lista');
    bloquearDiv(div);
    div.load(baseUrl + 'epi/listagem_externo/' + Math.random());
}
   
");
?>
<?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <?php if(!$listagem): ?>
        <div class="alert">Definir filtro de Cliente.</div>
    <?php else:?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php endif; ?>
<?php endif;?>    