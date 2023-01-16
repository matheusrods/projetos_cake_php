<?php if(!empty($sist_combate_incendio)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               	<th class="input-medium">Unidade</th>
	            <th class="input-xxlarge">Setor</th>
	            <th class="input-xxlarge">Tipo</th>
	            <th class="input-xxlarge">Revisor</th>
	            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sist_combate_incendio as $dados): ?>
            <tr>
                <td class="input-xlarge"><?php echo $array_cliente[$dados['SistCombateIncendio']['codigo_unidade']]; ?></td>
                <td class="input-xlarge"><?php echo $array_setor[$dados['SistCombateIncendio']['codigo_setor']]; ?></td>
                <td class="input-xlarge"><?php echo $array_tipo[$dados['SistCombateIncendio']['tipo']]; ?></td>
                <td class="input-xlarge"><?php echo $dados['SistCombateIncendio']['revisor']; ?></td>
                <td>
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusSistCombateIncendio('{$dados['SistCombateIncendio']['codigo']}','{$dados['SistCombateIncendio']['ativo']}')"));?>

                <?php if($dados['SistCombateIncendio']['ativo']== 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                <?php elseif($dados['SistCombateIncendio']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif; ?>
                
                <?php echo $this->Html->link('', array('action' => 'editar', $dados['SistCombateIncendio']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['SistCombateIncendio']['count']; ?></td>
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

    function excluir_cargo(codigo) {
        
        if (confirm('Deseja excluir este SistCombateIncendio?'))
            location.href = '/portal/sist_combate_incendio/excluir/' + codigo;
    }

    function atualizaStatusSistCombateIncendio(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'sist_combate_incendio/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaListaSistCombateIncendios();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaSistCombateIncendios();
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

    function atualizaListaSistCombateIncendios() {
    var div = jQuery('div.lista');
    bloquearDiv(div);
    div.load(baseUrl + 'sist_combate_incendio/listagem/' + Math.random());
}
");
?>
<?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    