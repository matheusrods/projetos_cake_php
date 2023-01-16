<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir'));?>
</div>
<br/>
<?php if(isset($listagem) && !empty($listagem)):?>
   <?php
        echo $paginator->options(array('update' => 'div.lista'));
    ?>
    <table class='table table-striped' style='max-width:none;white-space:nowrap'>
        <thead>
            <th><?php echo $this->Paginator->sort('Embarcador', 'prcl_embarcador_codigo') ?></th>
            <th><?php echo $this->Paginator->sort('Transportador', 'prcl_transportador_codigo') ?></th>
            <th><?php echo $this->Paginator->sort('PGR', 'prcl_pgr_codigo') ?></th>
            <th><?php echo $this->Paginator->sort('Tipo Transporte', 'prcl_ttra_codigo') ?></th>
            <th>&nbsp;</th>
        </thead>
        <tbody>
            <?php foreach ($listagem as $dado): ?>
                <tr style='word-wrap:none'>
                    <td>
                        <?php if(!empty($dado['TPrclPgrRelacaoCliente']['prcl_embarcador_codigo'])):?>
                            <?= DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny($dado['TPrclPgrRelacaoCliente']['prcl_embarcador_codigo']) .' - '. $dado['Embarcador']['pjur_razao_social'] ?>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php if(!empty($dado['TPrclPgrRelacaoCliente']['prcl_transportador_codigo'])):?>
                            <?= DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny($dado['TPrclPgrRelacaoCliente']['prcl_transportador_codigo']) .' - '. $dado['Transportador']['pjur_razao_social'] ?>
                        <?php endif;?>
                    </td>
                    <td><?= $this->Html->link($dado['TPrclPgrRelacaoCliente']['prcl_pgr_codigo'] , 'javascript:void(0)', array('onclick' => "visualizar_pgr('{$dado['TPrclPgrRelacaoCliente']['prcl_pgr_codigo']}')")) ?></td>
                    <td><?= $dado['TTtraTipoTransporte']['ttra_descricao'] ?></td>
                    <td><?= $html->link('', array('action' => 'excluir', $dado['TPrclPgrRelacaoCliente']['prcl_codigo'], rand()), array('title' => 'Excluir', 'class' => 'icon-trash'), 'Confirma exclusão?') ?></td>
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
<?php else:?>
    <div class="alert">Nenhum registro encontrado</div> 
<?php endif;?>
<?php echo $this->Javascript->codeBlock("
    function visualizar_pgr(codigo_pgr) {
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/pgpg_pgs/consulta_pgr/');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[TPgpgPg][pgpg_codigo]');
        field.setAttribute('value', codigo_pgr);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-30)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }
", false); 
?>    