<?php if(!empty($prestadores)):?>
    <?php echo $paginator->options(array('update' => 'div.lista'));?>
    <?php $pagina_atual = $this->Paginator->counter(array('format' => '%page%')); ?>
     <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-mini">SM</th>
                <th class="input-medium">Embarcador</th>
                <th class="input-medium">Transportador</th>
                <th class="input-mini">Inicio Real (SM)</th>
                <th class="input-mini">Fim Real (SM)</th>
                <th class="input-mini">Placa</th>
                <th class="input-small">Tecnologia</th>
                <th class="input-small">Prestador</th>
                <th class="input-mini">Data de Envio Prestador</th>
                <?php if(isset($exibir_valores) && $exibir_valores): ?>
                    <th class="input-mini ">Valor Honorários</th>
                    <th class="input-mini ">Valor Despesas</th>
                    <th class="input-mini ">Quilômetro</th>
                    <th class="input-mini"></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>



            <?php foreach ($prestadores as $prestador): ?>
            <tr>
                <td><?php echo $this->Buonny->codigo_sm($prestador['HistoricoSm']['codigo_sm']) ?></td>
                <td><?php echo $prestador['Embarcador']['razao_social'] ?></td>
                <td><?php echo $prestador['Transportador']['razao_social'] ?></td>
                <td><?php echo $prestador['Recebsm']['data_inicio'] ?></td>
                <td><?php echo $prestador['Recebsm']['data_final'] ?></td>
                <td><?php echo $this->Buonny->placa($prestador['Recebsm']['Placa'], $prestador['Recebsm']['data_inicio'], $prestador['Recebsm']['data_final'])?></td>
                <td><?php echo $prestador['Tecnologia']['descricao'] ?></td>
                <td><?php echo $prestador['Prestador']['nome'] ?></td>
                <td><?php echo $prestador['HistoricoSmPrestador']['data_inclusao']?></td>
                <?php if(isset($exibir_valores) && $exibir_valores): ?>
                    <td class=" numeric"> <?php echo isset($prestador['HistoricoSmPrestador']['valor_honorarios']) ? $this->Buonny->moeda($prestador['HistoricoSmPrestador']['valor_honorarios'],  array('nozero' => true)) : NULL ?></td>
                    <td class=" numeric"> <?php echo isset($prestador['HistoricoSmPrestador']['valor_despesas']) ? $this->Buonny->moeda($prestador['HistoricoSmPrestador']['valor_despesas'],  array('nozero' => true)) : NULL ?></td>
                    <td class=" numeric"> <?php echo isset($prestador['HistoricoSmPrestador']['quantia_km']) ? $this->Buonny->moeda($prestador['HistoricoSmPrestador']['quantia_km'],  array('nozero' => true)) : NULL ?></td>
                    <td><?php echo $html->link('', array('action' => 'alterar_valores', $prestador['HistoricoSmPrestador']['codigo'], $pagina_atual), array('class' => 'icon-edit btn-modal', 'title' => 'Alterar Valores', 'onclick' => "open_dialog(this, 'Alterar Valores',640); return false;")) ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "09"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Prestador']['count']; ?></td>
                <?php if(isset($exibir_valores) && $exibir_valores): ?>
                    <td class=" numeric"><?php echo  $this->Buonny->moeda($totais[0]['valor_honorarios'], array('nozero' => true)) ?></td>
                    <td class=" numeric"><?php echo  $this->Buonny->moeda($totais[0]['valor_despesas'], array('nozero' => true)) ?></td>
                    <td class=" numeric"><?php echo  $this->Buonny->moeda($totais[0]['quantia_km'], array('nozero' => true)) ?></td>
                    <td></td>
                <?php endif; ?>
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
    <?php echo $this->Buonny->link_js('estatisticas') ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>