<?php if(!empty($atendimentos)):?>
    <?php 
        echo $paginator->options(array('update' => 'div.lista'));
?>
     <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-mini">SM</th>
                <th class="input-medium">Nome Motorista</th>
                <th class="input-medium">Transportador</th>
                <th class="input-mini">Placa</th>
                <th class="input-mini">Atendente</th>
                <th class="input-mini">Ramal</th>
                <th class="input-medium">Data Cadastrada</th>
                <th class="input-medium">Motivo</th>
                <th class="input-small">Tecnologia</th>
                <th style='width:30px'></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($atendimentos as $atendimento): ?>
            <tr>
                <td><?php echo $this->Buonny->codigo_sm($atendimento[0]['sm']) ?></td>
                <td><?php echo $atendimento[0]['motorista'] ?></td>
                <td><?php echo $atendimento[0]['transportador'] ?></td>
                <td><?php echo $this->Buonny->placa($atendimento[0]['placa'], Date('d/m/Y 00:00:00'), Date('d/m/Y 23:59:59')) ?></td>
                <td><?php echo $atendimento[0]['apelido'] ?></td>
                <td><?php echo $atendimento[0]['ramal'] ?></td>
                <td><?php echo $atendimento[0]['data_cadastrada'];?></td>
                <td><?php echo $atendimento[0]['motivo'] ?></td>
                <td><?php echo $atendimento[0]['tecnologia'] ?></td>
                <td><?php echo $this->Html->link('', array('action' => 'visualizar', $atendimento[0]['codigo'], rand()), array('title' => 'Visualizar', 'class' => 'icon-eye-open', 'onclick' => 'return open_popup(this);')) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['AtendimentoSac']['count']; ?></td>
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