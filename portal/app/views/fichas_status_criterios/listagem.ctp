<?php 
    if ($action!='resultados_pesquisa_cliente'){
      echo $paginator->options(array('update' => 'div.lista')); 
      $total_paginas = $this->Paginator->numbers();
    }
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Código Ficha</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Seguradora</th>
            <th>Profissional</th>
            <th>CPF</th>
            <th>Pontos</th>
            <th>% de pontos</th>
            <th>Classificação</th>
            <th>Carga máxima (R$)</th>
            <th style="width:50px"></th>
        </tr>
    </thead>
    <tbody>
        <?php 
             foreach ($dados as $dado): ?>
        <tr>
            <td><?= $dado['codigo_ficha'] ?></td>
            <td><?= $dado['data_inclusao'] ?></td>
            <td><?= $dado['codigo_cliente'].' - '.$dado['cliente'] ?></td>
            <td><?= $dado['seguradora'] ?></td>
            <td><?= $dado['profissional'] ?></td>
            <td><?= $this->Buonny->documento($dado['profissional_cpf']) ?></td>
            <td><?= $dado['total'] ?></td>
            <td><?= $dado['percentual_total'].'%' ?></td>
            <td>
                <?if( $dado['pontos'] <= 0 ){ ?>
                    <font color="#f00"><?=$dado['classificacao_motorista']?></font>
                <?}else{?>
                    <?=$dado['classificacao_motorista']?>
                <?}?>                
            </td>
            <td><?= $this->Buonny->moeda($dado['qtd_maxima']) ?></td>
            <td>
            	<?php if ($action!='resultados_pesquisa_cliente'){ ?>
                <?php echo $html->link('', array('controller' => 'fichas_scorecard', 'action' => 'editar', $dado['codigo_ficha']), array('class' => 'icon-edit', 'title' => 'Editar Cadastro Ficha'));?>
            	<?php } ?>
                <?php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'resultado_ficha', $dado['codigo_ficha']), array('title' => 'Resultado detalhado', 'class'=>'icon-search')); ?>
                <?php if ($action!='resultados_pesquisa_cliente'){ ?>
            	<?php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'alterar_score', $dado['codigo_ficha']), array('title' => 'Alterar score', 'class'=>'icon-cog')); ?>
                <?php } ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<?php if ($action!='resultados_pesquisa_cliente'){ ?>
<div class='row-fluid'>
    <div class='numbers span6'>
        <?php if($this->Paginator->counter('{:pages}') > 1): ?>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        <?php endif; ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?php } ?>
