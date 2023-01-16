<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<div class="actionbar-right">
    <strong>Total Buonnysat: </strong><?=$qtd_produto[82]?>&nbsp;&nbsp;
    <strong>Total Teleconsult: </strong><?=$qtd_produto[1]?>&nbsp;&nbsp;
    <span class='badge-empty badge badge'></span>&nbsp;Não realizada&nbsp;
    <span class='badge-empty badge badge-success'></span>&nbsp;Satisfeito&nbsp;
    <span class='badge-empty badge badge-transito'></span>&nbsp;Parcialmente Satisfeito&nbsp;
    <span class='badge-empty badge badge-important'></span>&nbsp;Insatisfeito&nbsp;
    <span class='badge-empty badge badge-warning'></span>&nbsp;Reagendamento&nbsp;
</div>
<br/><br/>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini numeric"><?php echo $this->Paginator->sort('Código', 'codigo_cliente') ?></th>
            <th><?php echo $this->Paginator->sort('Cliente', 'razao_social') ?></th>
            <th class="input-medium"><?php echo $this->Paginator->sort('Data para Pesquisa', 'data_para_pesquisa') ?></th>
            <th><?php echo $this->Paginator->sort('Produto', 'codigo_produto') ?></th>
            <th class="input-medium"><?php echo $this->Paginator->sort('Status anterior', 'codigo_status_pesquisa') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Status', 'codigo_status_pesquisa') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Pesquisa Realizada', 'data_pesquisa') ?></th>
            <th class='action-icon'>&nbsp;</th>
            <th class='action-icon'>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listagem as $key => $pesquisa ):?>
            <?php $descricao_status_anterior = (!empty($pesquisa['PesquisaSatisfacaoPai']['codigo_status_pesquisa']) ? "Visualizar pesquisa (".$status_pesquisa[$pesquisa['PesquisaSatisfacaoPai']['codigo_status_pesquisa']].")" : 'Não realizada');?>
            <?php $cor_status_anterior = (!empty($pesquisa['PesquisaSatisfacaoPai']['codigo_status_pesquisa']) ? $cor_status_pesquisa[$pesquisa['PesquisaSatisfacaoPai']['codigo_status_pesquisa']] : '');?>
            <?php $descricao_status = (!empty($pesquisa['PesquisaSatisfacao']['codigo_status_pesquisa']) ? $status_pesquisa[$pesquisa['PesquisaSatisfacao']['codigo_status_pesquisa']] : 'Não realizada');?>
            <?php $cor_status = (!empty($pesquisa['PesquisaSatisfacao']['codigo_status_pesquisa']) ? $cor_status_pesquisa[$pesquisa['PesquisaSatisfacao']['codigo_status_pesquisa']] : '');?>
            <tr>
                <td class="input-mini numeric"><?= $pesquisa['PesquisaSatisfacao']['codigo_cliente'];?></td>                
                <td><?= $pesquisa['PesquisaSatisfacao']['codigo_pai'] ? $this->Html->tag('strong', $pesquisa['Cliente']['razao_social']) : $pesquisa['Cliente']['razao_social'] ?></td>
                <td><?= $pesquisa['PesquisaSatisfacao']['data_para_pesquisa'];?></td>
                <td><?= ($pesquisa['PesquisaSatisfacao']['codigo_produto'] == '1') ? 'Teleconsult' : 'Buonnysat';?></td>
                <td class="action-icon">
                    <?= (!empty($cor_status_anterior) ? $this->Html->link('', array(
                        'action' => 'pesquisa_realizada', $pesquisa['PesquisaSatisfacaoPai']['codigo']
                    ), array('escape' => false,'class' => "badge-empty badge badge-$cor_status_anterior",'title' =>$descricao_status_anterior,'onclick' => "return open_dialog(this,'Pesquisa Realizada: {$pesquisa['Cliente']['razao_social']}', 760)")) : "<span class='badge-empty badge badge' title='$descricao_status_anterior'></span>");?>
                    </td>
                <td><?="<span class='badge-empty badge badge-$cor_status' title='$descricao_status'></span>"?></td>
                <td><?=$pesquisa['PesquisaSatisfacao']['data_pesquisa'];?></td>
                <td class="action-icon">
                    <?php if(!empty($pesquisa['PesquisaSatisfacao']['codigo_status_pesquisa'])):?>
                        <?php echo $this->Html->link(' <i class="icon-search"></i>', array('action' => 'pesquisa_realizada',$pesquisa['PesquisaSatisfacao']['codigo']), array('escape' => false,'title' =>'Pesquisa','onclick' => "return open_dialog(this,'Pesquisa Realizada: {$pesquisa['Cliente']['razao_social']}', 760)"));?>
                    <?php endif;?> 
                </td>
                <td class="action-icon">
                    <?php if(empty($pesquisa['PesquisaSatisfacao']['codigo_status_pesquisa'])):?>
                        <?php echo $this->Html->link('<i class="icon-plus"></i>',
                        array('action' => 'pesquisa_satisfacao', $pesquisa['PesquisaSatisfacao']['codigo'] ), 
                        array('escape' => false, 'title' =>'Responder Pesquisa', 
                        'onclick' => "return open_dialog(this, 'Pesquisa Satisfação: {$pesquisa['Cliente']['razao_social']}', 960)"));?>
                    <?php endif;?> 
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
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