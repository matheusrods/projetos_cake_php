<?php if(!empty($tecnicas_medicao)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código</th>
            <th class="input-xxlarge">Nome</th>
            <th class="input-xxlarge">Abreviação</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tecnicas_medicao as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['TecnicaMedicao']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['TecnicaMedicao']['nome'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['TecnicaMedicao']['abreviacao'] ?></td>
                <td>

                    <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusTecnicaMedicao('{$dados['TecnicaMedicao']['codigo']}','{$dados['TecnicaMedicao']['ativo']}')"));?>

                    <?php if($dados['TecnicaMedicao']['ativo']== 0): ?>
                        <span class="badge-empty badge badge-important" title="Desativado"></span>
                    <?php elseif($dados['TecnicaMedicao']['ativo']== 1): ?>
                        <span class="badge-empty badge badge-success" title="Ativo"></span>
                    <?php endif; ?>

	                <?php echo $this->Html->link('', array('action' => 'editar', $dados['TecnicaMedicao']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['TecnicaMedicao']['count']; ?></td>
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
    function atualizaListaTecnicasMedicao() {
	    var div = jQuery('div.lista');
	    bloquearDiv(div);
	    div.load(baseUrl + 'tecnicas_medicao/listagem/' + Math.random());
	}

    function atualizaStatusTecnicaMedicao(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'tecnicas_medicao/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){
                if(data == 1){
                    atualizaListaTecnicasMedicao();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaTecnicasMedicao();
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
");
?>
<?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    