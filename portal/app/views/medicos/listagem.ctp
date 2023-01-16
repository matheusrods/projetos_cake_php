<?php if(!empty($medicos)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <th class="input-mini">Código</th>
            <th class="input-xlarge">Nome</th>
            <th class="input-mini">Conselho</th>
            <th class="input-medium">Número do Conselho</th>
            <th class="input-mini">Estado</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medicos as $medico): ?>
            <tr>
                <td class="input-mini"><?php echo $medico['Medico']['codigo'] ?></td>
                <td class="input-xlarge"><?php echo $medico['Medico']['nome'] ?></td>
                <td class="input-mini"><?php echo $medico['ConselhoProfissional']['descricao'] ?></td>
                <td class="input-medium"><?php echo Comum::soNumero($medico['Medico']['numero_conselho']);?></td>
                <td class="input-mini"><?php echo $medico['Medico']['conselho_uf'] ?></td>
                <td>
                
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$medico['Medico']['codigo']}','{$medico['Medico']['ativo']}')"));?>

                <?php if($medico['Medico']['ativo']== 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                <?php elseif($medico['Medico']['ativo']== 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif; ?>

                <?php echo $this->Html->link('', array('action' => 'editar', $medico['Medico']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar Médico')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Medico']['count']; ?></td>
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php 
echo $this->Javascript->codeBlock("

     function atualizaStatus(codigo, status){
        $.ajax({
            type: 'POST',
            url: baseUrl + 'medicos/atualiza_status/' + codigo + '/' + status + '/' + Math.random(),
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
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'medicos/listagem/' + Math.random());
    }

    
");
?>