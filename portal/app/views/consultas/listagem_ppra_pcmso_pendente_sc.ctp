<?php
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th >Setor</th>
            <th >Cargo</th>
            <th >Funcionários</th>
            <th >Ações</th>
        </tr>        
    </thead>
    <tbody>
        <?php 
        //pr($listagem);
        foreach ($listagemPendentes as $list): 

            $codigo_cliente = $list[0]['codigo_cliente'];
            $class_prefix = 'badge badge-empty badge-';
            // Status 
            $status = $list[0]['status'];

        ?>
        <tr>
            <td><?= $list[0]['Setor'] ?></td>
            <td><?= $list[0]['Cargo'] ?></td>
            <td><?= $list[0]['funcionarios'] ?></td>
            <td><?php
                if( $status == 1 ){
                    echo $this->Html->link(
                        "Aplicar",array(
                            'controller' => $controller_link, 
                            'action'     => 'incluir', $codigo_cliente, $list[0]['CodigoSetor'], $list[0]['CodigoCargo'],'pendente' ),
                        array(
                            'class' => $class_prefix."important" )
                    );
                } else {
                    echo $this->Html->div( $class_prefix."success", "OK"  );
                }
                
            ?></td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
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

<div class='form-actions well'>
    <?php 
    if( $botao_finalizar_processo ){
        echo $html->link(
            'Finalizar Processo', 
                array(
                    'controller' => 'consultas', 
                    'action'     => 'finalizar_processo_pendente',$codigo_cliente, $tipo
                ), 
                array('class' => 'btn btn-success'
            )
        ); 
    }?>
    
    <?php echo $html->link(
        'Voltar', array(
            'controller' => 'consultas', 
            'action'     => 'consulta_ppra_pcmso_pendente'), 
                array('class' => 'btn'
            )
        ); 
    ?>

    <div style="float:right">
        <?php 
            echo $html->link((
                $tipo == 'pcmso' ? 'Preencher com exame clínico' : 'Preencher com ausência de risco'), 
                array(
                    'controller' => 'consultas', 
                    'action'     => 'ausencia_risco', $codigo_cliente, $tipo
                ), 
                array(
                    'class' => 'btn btn-warning', 
                    'title' => ($tipo == 'pcmso' ? 'Preencher com exame clínico' : 'Preencher com ausência de risco')
                )
            ); 
        ?>
    </div> 
</div>
