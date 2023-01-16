<?php echo $paginator->options(array('update' => 'div.lista'));?>
<div class="row-fluid">
    <span class="span4">
        <? if(empty($authUsuario['Usuario']['codigo_cliente'])):?>
            <strong>Total:</strong> 
            <?php echo $this->Paginator->params['paging']['FichaScorecard']['count']; ?>
        <? endif; ?>
    </span>
</div>
<table  class="table table-striped horizontal-scroll" >
    <thead>
        <tr>
            <th class="input-small"><?php echo $this->Paginator->sort('Codigo', 'codigo') ?></th>
            <th class="input-large"><?php echo $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
            <th class="input-large"><?php echo $this->Paginator->sort('Usuário', 'codigo_usuario_inclusao') ?></th>
            <th class="input-small"><?php echo $this->Paginator->sort('RG', 'rg') ?></th>
            <th class="input-xxlarge"><?php echo $this->Paginator->sort('Profissional', 'nome') ?></th>
            <th class="input-medium"><?php echo $this->Paginator->sort('Categoria', 'descricao') ?></th>
            <th class="input-small"><?php echo $this->Paginator->sort('Inclusão', 'data_inclusao') ?></th>
            <th class="input-small"><?php echo $this->Paginator->sort('Validade', 'data_validade') ?></th>
            <th class="input-small"><?php echo $this->Paginator->sort('Classificação', 'nivel') ?></th>            
            <th class="input-small"><?php echo $this->Paginator->sort('Vinculado', 'ativo') ?></th>
        </tr>
    </thead>

    <?php if(!empty($listar)):?>
    <?php foreach ( $listar as $key=> $dados): ?>
        <tr>
            <td><?php echo $dados['FichaScorecard']['codigo_cliente'];?></td>
            <td><?php echo $dados['Cliente']['razao_social'];?></td>
            <td><?php echo $dados['Usuario']['apelido'];?></td>
            <td><?php echo $dados['ProfissionalLog']['rg'];?></td>
            <td><?php echo $dados['ProfissionalLog']['nome'];?></td>
            <td><?php echo $dados['ProfissionalTipo']['descricao']?></td>
            <td><?php echo $dados['FichaScorecard']['data_inclusao']?></td>
            <td><?php echo substr($dados['FichaScorecard']['data_validade'], 0,10)?></td>            
            <td>
                <?php if( $dados['FichaScorecard']['codigo_status'] == FichaScorecardStatus::FINALIZADA ) :?>
                <?php echo ( FichaScorecard::ENVIA_EMAIL_SCORECARD ? ($dados['ParametroScore']['nivel']) : $dados[0]['status_manual']) ;?>
                <?php else:?>
                    Em Análise
                <?php endif;?>
            </td>
            <td>
                <?php if( $dados['FichaScorecard']['ativo'] == 2 ): ?>
                    <span align="center" class="badge-empty badge badge-important" title="Excluído"></span>
                <?php else:?>
                    <span align="center" class="badge-empty badge badge-success" title="Ativo"></span>
                <?php endif;?>
            </td>
        </tr>
    <?php endforeach;?>
    <?php endif;?>
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