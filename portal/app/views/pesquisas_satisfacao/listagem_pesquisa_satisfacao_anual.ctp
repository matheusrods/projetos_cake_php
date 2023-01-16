<div class="actionbar-right well" >
    <span class='badge-empty badge badge-success'></span>&nbsp;Satisfeito&nbsp;
    <span class='badge-empty badge badge-transito'></span>&nbsp;Parcialmente Satisfeito&nbsp;
    <span class='badge-empty badge badge-important'></span>&nbsp;Insatisfeito&nbsp;
    <span class='badge-empty badge badge-warning'></span>&nbsp;Reagendamento&nbsp;
    <span class='badge-empty badge badge-ativo'></span>&nbsp;Cancelado&nbsp;
    <span class='badge-empty badge badge-bloqueado'></span>&nbsp;Bloqueado&nbsp;
    <span class='badge-empty badge badge'></span>&nbsp;Sem Pesquisa&nbsp;
</div>
<?php $status_pesquisa += array( NULL => NULL);
      $cor_status_pesquisa += array(NULL => NULL);
?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini numeric"><?php echo $this->Paginator->sort('Código', 'codigo_cliente') ?></th>
            <th class="input-xxlarge"><?php echo $this->Paginator->sort('Cliente', 'razao_social') ?></th>
            <th class="input-small"><?php echo $this->Paginator->sort('Produto', 'codigo_produto') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Janeiro', 'janeiro') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Fevereiro', 'fevereiro') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Março', 'março') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Abril', 'abril') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Maio', 'maio') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Junho', 'junho') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Julho', 'julho') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Agosto', 'agosto') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Setembro', 'setembro') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Outubro', 'outubro') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Novembro', 'novembro') ?></th>            
            <th class="input-mini"><?php echo $this->Paginator->sort('Dezembro', 'dezembro') ?></th>            
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listagem as $key => $pesquisa ):?>
            <?php $descricao_status[1] = $pesquisa['0']['janeiro'] !== 0 ? $status_pesquisa[$pesquisa['0']['janeiro']] : 'Sem pesquisa';?>
            <?php $descricao_status[2]= $pesquisa['0']['fevereiro'] !== 0 ? $status_pesquisa[$pesquisa['0']['fevereiro']] : 'Sem pesquisa';?>
            <?php $descricao_status[3]= $pesquisa['0']['marco'] !== 0 ? $status_pesquisa[$pesquisa['0']['marco']] : 'Sem pesquisa';?>
            <?php $descricao_status[4]= $pesquisa['0']['abril'] !== 0 ? $status_pesquisa[$pesquisa['0']['abril']] : 'Sem pesquisa';?>
            <?php $descricao_status[5]= $pesquisa['0']['maio'] !== 0 ? $status_pesquisa[$pesquisa['0']['maio']] : 'Sem pesquisa';?>
            <?php $descricao_status[6]= $pesquisa['0']['junho'] !== 0 ? $status_pesquisa[$pesquisa['0']['junho']] : 'Sem pesquisa';?>
            <?php $descricao_status[7]= $pesquisa['0']['julho'] !== 0 ? $status_pesquisa[$pesquisa['0']['julho']] : 'Sem pesquisa';?>
            <?php $descricao_status[8]= $pesquisa['0']['agosto'] !== 0 ? $status_pesquisa[$pesquisa['0']['agosto']] : 'Sem pesquisa';?>
            <?php $descricao_status[9]= $pesquisa['0']['setembro'] !== 0 ? $status_pesquisa[$pesquisa['0']['setembro']] : 'Sem pesquisa';?>
            <?php $descricao_status[10] = $pesquisa['0']['outubro'] !== 0 ? $status_pesquisa[$pesquisa['0']['outubro']] : 'Sem pesquisa';?>
            <?php $descricao_status[11] = $pesquisa['0']['novembro'] !== 0 ? $status_pesquisa[$pesquisa['0']['novembro']] : 'Sem pesquisa';?>
            <?php $descricao_status[12] = $pesquisa['0']['dezembro'] !== 0 ? $status_pesquisa[$pesquisa['0']['dezembro']] : 'Sem pesquisa';?>
            <?php $cor_mes[1]= ($pesquisa['0']['janeiro'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['janeiro']] : 'Sem pesquisa';?>
            <?php $cor_mes[2]= ($pesquisa['0']['fevereiro'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['fevereiro']] : 'Sem pesquisa';?>
            <?php $cor_mes[3]= ($pesquisa['0']['marco'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['marco']] : 'Sem pesquisa';?>
            <?php $cor_mes[4]= ($pesquisa['0']['abril'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['abril']] : 'Sem pesquisa';?>
            <?php $cor_mes[5]= ($pesquisa['0']['maio'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['maio']] : 'Sem pesquisa';?>
            <?php $cor_mes[6]= ($pesquisa['0']['junho'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['junho']] : 'Sem pesquisa';?>
            <?php $cor_mes[7]= ($pesquisa['0']['julho'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['julho']] : 'Sem pesquisa';?>
            <?php $cor_mes[8]= ($pesquisa['0']['agosto'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['agosto']] : 'Sem pesquisa';?>
            <?php $cor_mes[9]= ($pesquisa['0']['setembro'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['setembro']] : 'Sem pesquisa';?>
            <?php $cor_mes[10] = ($pesquisa['0']['outubro'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['outubro']] : 'Sem pesquisa';?>
            <?php $cor_mes[11] = ($pesquisa['0']['novembro'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['novembro']] : 'Sem pesquisa';?>
            <?php $cor_mes[12] = ($pesquisa['0']['dezembro'] !== 0) ? $cor_status_pesquisa[$pesquisa['0']['dezembro']] : 'Sem pesquisa';?>
            <tr>
                <td class="input-mini numeric"><?= $pesquisa['0']['codigo_cliente'];?></td>                
                <td><?= $pesquisa['0']['razao_social']?></td>
                <td><?= ($pesquisa['0']['codigo_produto'] == '1') ? 'Teleconsult' : 'Buonnysat';?></td>
                <? for ($i=1;$i<=12;$i++): ?>
                    <td><?="<span class='".($cor_mes[$i]===null ? "" : "badge-empty badge badge-".$cor_mes[$i])."' title='".$descricao_status[$i]."'></span>"?></td>    
                <? endfor;?>
            </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td id="boxTotReg" colspan="15" class='numeric'>
                <span class="totRegTxtBasico"><strong>Total de registro(s)</strong> ( </span><?php echo $this->Paginator->counter(array('format' => '%count%')); ?> <span class="totRegTxtBasico">) retornado(s)</span>
            </td>            
        </tr>
    </tfoot>
</table>
<div class='row-fluid'>
   <!--  <div class='numbers span6'> -->
        <?php //echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        <?php //echo $this->Paginator->numbers(); ?>
        <?php //echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    <!-- </div> -->
    <!-- <div class='counter span6'> -->
        <?php //echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    <!-- </div> -->
</div>
<?php echo $this->Js->writeBuffer(); ?>