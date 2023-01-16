<?php echo $this->BForm->create('FichaScorecard', array('url' => array('controller' => 'fichas_scorecard', 'action' => 'excluir_vinculo_profissional' ))); ?>
<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<?php if( !empty($listar)):?>
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
<table class="table table-striped">
    <thead>
        <tr>            
            <th></th>
            <th><?php echo $this->Paginator->sort('Código', 'codigo_cliente') ?></th>
            <th><?php echo $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
            <th><?php echo $this->Paginator->sort('Nome', 'nome') ?></th>
            <th><?php echo $this->Paginator->sort('CPF', 'codigo_documento') ?></th>
            <th class="input-large"><?php echo $this->Paginator->sort('Categoria', 'descricao') ?></th>
            <th><?php echo $this->Paginator->sort('Status', 'descricao') ?></th>
            <th><?php echo $this->Paginator->sort('Score', 'nivel') ?></th>
        </tr>
    </thead>
    <tbody>    
    <?php foreach ( $listar as $key=>$dados ) : ?>
        <tr>
            <td><input type="checkbox" name="data[FichaScorecard][excluir][]" value="<?php echo $dados[0]['codigo_ficha']; ?>"/></td>
            <td><?=$dados['FichaScorecard']['codigo_cliente']?></td>
            <td><?=$dados['Cliente']['razao_social']?></td>
            <td><?=$dados['ProfissionalLog']['nome']?></td>
            <td><?=Comum::formatarDocumento( $dados['ProfissionalLog']['codigo_documento']) ?></td>
            <td><?=$dados['ProfissionalTipo']['descricao']?></td>
            <td><?=FichaScorecardStatus::descricao( $dados['FichaScorecard']['codigo_status']);?></td>
            <td class="input-medium">            
            <?if( $dados['FichaScorecard']['codigo_status'] == 2 ) {
                echo $status = 'Em Análise';
              }elseif( $dados['FichaScorecard']['total_pontos'] < 1 ) {
                echo $status = 'Divergente';
              }else{
                echo $status = 'Adequado até';
                echo "<br />R$: ". $this->Buonny->moeda( $dados['Score']['valor']);
              }
            ?>        
            </td>
        </tr>         
    <?php endforeach;?>
    </tbody>
</table>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Excluir', array('div' => false, 'class' => 'btn btn-primary', 'name'=>'aprovar')); ?>
</div>
<?php else:?>
    <div class='alert'>Nenhum registro encontrado</div>
<?php endif;?>
<?php echo $this->Js->writeBuffer(); ?>       