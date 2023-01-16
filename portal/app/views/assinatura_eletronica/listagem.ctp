<?php //debug($medicos);?>

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
            <th class="input-mini">Assinatura</th>
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
                <td class="input-mini"><?php echo (!empty($medico['Medico']['conselho_uf']) ? $medico['Medico']['conselho_uf'] : '-' ) ?></td>
                <td class="input-mini">
                    <?php if ($medico['Medico']['anexo']): ?>
                        <img width="50px" src="<?php echo $medico['Medico']['anexo']['AnexoAssinaturaEletronica']['caminho_arquivo']; ?>">
                    <?php else: ?>
                        <?php echo '-'; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $this->Html->link('', array('action' => 'editar', $medico['Medico']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar Médico')); ?>
                    
                    <?php if ($medico['Medico']['anexo']): ?>
                        <a href="javascript:void(0)" class="icon-eye-open" onclick="visualiza_log('<?php echo $medico['Medico']['codigo']; ?>')" title="Logs"></a>
                    <?php endif; ?>
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
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php 
echo $this->Javascript->codeBlock("
    function visualiza_log(codigo_medico){
        var janela = window_sizes();
        window.open(baseUrl + 'assinatura_eletronica/listagem_log/' + codigo_medico + '/' + Math.random(), janela, 'scrollbars=yes,menubar=no,height='+(janela.height-400)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    }    
");
?>