<?php if(!empty($rotas) || !empty($codigo_cliente)):?>
<?php echo $paginator->options(array('update' => 'div.lista'));?>
         <div class='actionbar-right'>
            <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'adicionar_rota', $codigo_cliente), array('title' => 'Adicionar Rota', 'escape' => false, 'class' => 'btn btn-success'));?>
        </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-mini">Codigo Externo</th>
                <th class="input-medium">Alvo Origem</th>
                <th class="input-medium">Alvo Destino</th>
                <th class="input-medium">Descrição</th>
                <th class="input-mini">Observação</th>
                <th class="input-mini">Status</th>
                <th class="input-mini">Valor Combustivel</th>
                <th class="input-mini">Litros Combustivel</th>
                <th class="input-mini">Valor Pedagio</th>
                <th class="input-mini">Data da Ultima atualização</th>
                <th style='width:40px'></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rotas as $rota): ?>
            <tr>
                <td><?php echo $rota['TRotaRota']['rota_codigo_externo'] ?></td>
                <td><?php echo $rota['TRponRotaPontoOrigem']['rpon_descricao'] ?></td>
                <td><?php echo $rota['TRponRotaPontoDestino']['rpon_descricao'] ?></td>
                <td><?php echo $rota['TRotaRota']['rota_descricao'] ?></td>
                <td><?php echo $rota['TRotaRota']['rota_observacao'] ?></td>
                <td><?php echo ($rota['TRotaRota']['rota_ativo'] == 'A' ? 'Ativa' : 'Inativa') ?></td>
                <td><?php echo $this->Buonny->moeda($rota['TRotaRota']['rota_previsao_valor_combustivel'] ,  array('nozero' => true, 'places' => 2))?></td>
                <td><?php echo $this->Buonny->moeda($rota['TRotaRota']['rota_previsao_litros_combustivel'] ,  array('nozero' => true, 'places' => 2))?></td>
                <td><?php echo $this->Buonny->moeda($rota['TRotaRota']['rota_previsao_valor_pedagio'] ,  array('nozero' => true, 'places' => 2)) ?></td>
                <td><?php echo $rota['TRotaRota']['rota_data_ultima_atualizacao_custos'] ?></td>
                <td><?= $this->Html->link('', array('action' => 'editar_rota', $rota['TRotaRota']['rota_codigo'], rand()), array('title' => 'Editar Rota', 'class' => 'icon-edit')) ?>
                    <?= $this->Html->link('', array('action' => 'remover_rota', $rota['TRotaRota']['rota_codigo'], rand()), array('title' => 'Excluir Rota', 'class' => 'icon-trash')) ?></td>
                </tr>
            </tr>
        <?php endforeach ?>
        </tbody> 
        <tfoot>
            <tr>
                <td colspan = "11"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['TRotaRota']['count']; ?></td>
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    