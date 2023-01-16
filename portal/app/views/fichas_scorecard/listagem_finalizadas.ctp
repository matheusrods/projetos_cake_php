<?php if (!empty($dados)) : ?>    
<?php  echo $paginator->options(array('update' => 'div.lista'));  
$total_paginas = $this->Paginator->numbers();?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Liberação</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Profissional</th>
            <th>CPF</th>
            <th>Classificação Manual</th>
            <th>Pontos</th>
            <th>% de pontos</th>
            <th>Classificação Score</th>
            <th>Carga máxima (R$)</th>
            <th>Usuário</th>
            <th class="imput-large">E-mails</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dados as $dado):
            $contatos = !empty($stringEmails[$dado['FichaScorecard']['codigo']])?$stringEmails[$dado['FichaScorecard']['codigo']] : NULL;
            $emails   = $contatos ? strtolower( $contatos) : NULL;           
        ?>
        <tr>
            <td><?= $dado['0']['codigo_liberacao'] ?></td>
            <td><?= $dado['FichaScorecard']['data_alteracao'] ?></td>
            <td><?= $dado['0']['codigo_cliente'].' - '.$dado['0']['cliente'] ?></td>
            <td><?= $dado['0']['nome'] ?></td>
            <td><?= $this->Buonny->documento($dado['0']['profissional_cpf']) ?></td>
            <td class="numeric">
                <?if( ($dado['FichaScorecard']['codigo_score_manual'] > 6) ){ ?>
                    <font color="#f00"><?=$dado['0']['status_manual']?></font>
                <?}else{?>
                    <?=$dado['0']['status_manual']?>
                <?}?>                
            </td>
            <td class="numeric"><?= $dado['0']['total'] ?></td>
            <td class="numeric"><?= $dado['0']['percentual_total'].'%' ?></td>
            <td>
                <?if( $dado[0]['pontos'] <= 0 ){ ?>
                    <font color="#f00"><?=$dado['0']['classificacao_motorista']?></font>
                <?}else{?>
                    <?=$dado['0']['classificacao_motorista']?>
                <?}?>                
            </td>
            <td class="numeric"><?= $this->Buonny->moeda($dado['0']['qtd_maxima']) ?></td>
            <td><?=$dado['0']['usuario']; ?></td>
            <td><?=$emails?></td>
            <td>
                <?php if ($dado['0']['codigo_liberacao'] !='') { ?>
                <?php echo $html->link('', array('controller' => 'fichas_scorecard', 'action' => 'emailConsultaProfissional', $dado['FichaScorecard']['codigo'],$dado['0']['codigo_liberacao']), array('class' => 'icon-envelope', 'title' => 'Reenviar Email'), 'Confirma Reenvio da Consulta?'); ?>
                <?php }else{ ?>
                      &nbsp;&nbsp;&nbsp; 
                <?php } ?>
            </td> 
            <td>   
            	<?php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'resultado_ficha', $dado['FichaScorecard']['codigo']), array('title' => 'Resultado detalhado', 'class'=>'icon-search')); ?>
            </td>
            <td>   
            <?php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'alterar_score', $dado['FichaScorecard']['codigo']), array('title' => 'Alterar score', 'class'=>'icon-cog')); ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
    <tfoot>
        <tr>
            <td colspan = "16">
                <strong>Total</strong>
                <?php echo $this->Paginator->counter('{:count}');?>
            </td>
        </tr>
    </tfoot>    
</table>
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
<?php endif;?>