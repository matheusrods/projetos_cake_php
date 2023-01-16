<?php if(!empty($funcionarios)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-mini">Código</th>
               <th class="input-xxlarge">Cliente</th>
               <th class="input-xxlarge">Funcionário</th>
            	<th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($funcionarios as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Funcionario']['codigo'] ?></td>
                <td class="input-mini"><?php echo $dados['Cliente']['razao_social'] ?></td>
                <td class="input-mini"><?php echo $dados['Funcionario']['nome'] ?></td>
                <td>
                <?php echo $this->Html->link('', array('action' => 'incluir', $dados['Funcionario']['codigo']), array('class' => 'icon-file ', 'title' => 'Criar relatório de Audiometria', 'data-toggle' => 'tooltip')); ?>
                </td>
            </tr>
        <?php endforeach ?>
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
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>
    <?php 
echo $this->Javascript->codeBlock("
    $(document).ready(function() {
        $('[data-toggle=\"tooltip\"]').tooltip();
    });
    function atualizaAudiometrias() {
	    var div = jQuery('div.lista');
	    bloquearDiv(div);
	    div.load(baseUrl + 'audiometrias/listagem/' + Math.random());
	}
");
?>
<?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    