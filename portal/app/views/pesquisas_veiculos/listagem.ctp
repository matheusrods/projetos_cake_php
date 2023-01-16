<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped" style='table-layout:fixed'>    
    <thead>
        <tr> 
            <th class='input-small'><?= $this->Paginator->sort('Placa', 'placa') ?></th>
            <th class='input-large'><?= $this->Paginator->sort('Cliente', 'cliente') ?></th>
            <th class='input-large'><?= $this->Paginator->sort('Embarcador', 'embarcador') ?></th>
            <th class='input-large'><?= $this->Paginator->sort('Transportador', 'transportador') ?></th>
            <th class='input-small'><?= $this->Paginator->sort('Status', 'codigo_status') ?></th>
            <th class='input-small'>
            <?php
            if($pesquisa){
                echo $this->Paginator->sort('Responsável', 'usuario_pesquisa');                
            }else {
                echo $this->Paginator->sort('Responsável', 'usuario_aprovacao');  
            }
            ?>
            </th>
            <th class='input-small'><?php
            if($pesquisa){
                echo $this->Paginator->sort('Data', 'data_inclusao');
            }else{
                echo $this->Paginator->sort('Data', 'data_alteracao');
            }
            ?></th>
            
            <?php if($pesquisa){ ?>                
                <th class='input-small'>Tempo para conclusão (minutos)</th>            
            <?php } ?>
            <th class='input-small'></th>
        </tr>
    </thead>
    <tbody>
    <?php         
        if(count($listar) > 0 ): 
            foreach($listar as $ficha ):
                $ficha = $ficha[0];
            if(empty($ficha['tempo_sla'])){
                $ficha['tempo_sla'] = 'Cliente com o tempo de SLA não configurado.';
                $minutos_restantes = 'Tempo de SLA não configurado.';
            }else{
                $minutos_restantes = round((strtotime('+'.$ficha['tempo_sla'].' minutes', strtotime(AppModel::dateTimeToDbDateTime($ficha['data_inclusao']))) - strtotime('now')) / 60);  
            }  
    ?>
        <tr>
            <td><?php echo Comum::formatarPlaca($ficha['placa']); ?></td>
            <td><?php echo $ficha['cliente']; ?></td>
            <td><?php echo $ficha['embarcador']; ?></td>
            <td><?php echo $ficha['transportador']; ?></td>
            <td><?php echo $status[$ficha['codigo_status']]; ?></td>
            <td><?php
            if($ficha['codigo_status']==PesquisaVeiculo::PESQUISA){
                echo $ficha['usuario_pesquisa']; 
            }else if($ficha['codigo_status']==PesquisaVeiculo::APROVADA || 
                     $ficha['codigo_status']==PesquisaVeiculo::REPROVADA){
                echo $ficha['usuario_aprovacao']; 
            }
            ?></td>
            <?php if($pesquisa){ ?>
                <td><?php echo $ficha['data_inclusao'] ?></td>
                <td <?php echo $minutos_restantes < 0 ? "style='color:red;'" : ""; ?>><?php echo $minutos_restantes; ?></td>
            <?php }else{ ?>
                <td><?php echo $ficha['data_alteracao'] ?></td>
            <?php } ?>
            <td><?php if($pesquisa){
                echo $html->link('', array('controller' => 'pesquisas_veiculos', 'action' => 'alterar', $ficha['codigo_cliente'], $ficha['placa'], $ficha['codigo_ficha']), array('class' => 'icon-briefcase', 'title' => 'Pesquisar')); 
            }else{
                echo $html->link('', array('controller' => 'pesquisas_veiculos', 'action' => 'alterar', $ficha['codigo_cliente'], $ficha['placa'], $ficha['codigo_ficha']), array('class' => 'icon-search', 'title' => 'Visualizar')); 
            }
            ?>
            </td>
        </tr>                    
        <?php endforeach; ?>
    <?php endif;?>
    </tbody>
    <thead>
        <tr>
            <th colspan="9">

            Total de Veículos: <?php echo $this->Paginator->params['paging']['PesquisaVeiculo']['count']; ?></th>
        </tr>
    </thead>
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
