<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class='input-small'>Ação</th>
            <th class='input-medium'>Responsável</th>
            <th class='input-medium'>CPF</th>
            <th class='input-medium'>Data Log</th>
            <th class='input-medium'>Categoria</th>
            <th class='input-medium'>Status</th>
            <th style="width:13px"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($listar as $lista): ?>
            <?php $ficha_action = $lista['FichaScorecardLog']['codigo_status'] == FichaScorecardStatus::A_APROVAR ? 'aprovar' : 'editar'; ?>
            <tr>
                <td class='input-small'><?php echo $lista['FichaScorecardLog']['acao_sistema'] == 0 ? 'Criada' : ($lista['FichaScorecardLog']['acao_sistema'] == 1 ? 'Alterado' : 'Excluído'); ?></td>
                <td class='input-medium'>
                    <?php echo !empty($lista['UsuarioAlteracao']['apelido']) ? $lista['UsuarioAlteracao']['apelido'] : $lista['Usuario']['apelido'];?>
                </td>
                <td class='input-medium'><?php echo $buonny->documento($lista['ProfissionalLog']['codigo_documento']); ?></td>
                <td class='input-medium'><?php echo $lista['FichaScorecardLog']['data_inclusao']; ?></td>
                <td class='input-medium'><?php echo $lista['ProfissionalTipo']['descricao']; ?></td>
                <td class='input-medium'><?php echo ClassRegistry::init('FichaScorecardStatus')->descricao($lista['FichaScorecardLog']['codigo_status']); ?></td>
                <td>
                    <?php echo $html->link('', array('controller' => 'fichas_status_criterios_log', 'action' => 'visualizar', $lista['FichaScorecardLog']['codigo']), array('class' => 'icon-search', 'title' => 'Visualizar Ficha'));?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
    <?php if( isset($listar) ): ?>
        <tr>
            <td colspan="8" class="input-xlarge"><strong>Total: <?php echo $this->Paginator->counter('{:count}')?></strong></td>
        </tr>
    <?php  endif;?>
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