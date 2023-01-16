<?php
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th >Setor</th>
            <th >Cargo</th>
            <?php if($tipo == 'pcmso'){
                echo '<th >Funcionário</th>';
            }
            ?> 
            <th></th>
            <th >Ações</th>
        </tr>        
    </thead>
    <tbody>
        <?php 
        foreach ($listagemPendentes as $list): 

            $list[0]['codigo_cliente'] = $codigo_cliente;  
            //debug($codigo_cliente);
            $class_prefix = 'badge badge-empty badge-';
            // Status 
            $status = $list[0]['status'];

        ?>
        <tr>
            <td><?= $list[0]['Setor'] ?></td>
            <td><?= $list[0]['Cargo'] ?></td>
            <?php if($tipo == 'pcmso'): ?>
                <td><?php echo $list[0]['FuncionarioNome']; ?></td>
            <?php endif; ?>
            <td></td>

            <td><?php
                    if( $status == 1 ){
                        echo $this->Html->link("Aplicar" , array(
                                    'controller' => $controller_link, 
                                    'action'=>'incluir', 
                                    $codigo_cliente, 
                                    $list[0]['CodigoSetor'], 
                                    $list[0]['CodigoCargo'],
                                    (!empty($list[0]['CodigoFuncionario']) ? $list[0]['CodigoFuncionario'] : (!empty($list[0]['codigo_funcionario_2']) ? $list[0]['codigo_funcionario_2'] : 'null'))), 
                                array('class' => $class_prefix."important")
                            );
                    } elseif($status == 3) {
                        echo $this->Html->link("Validar", 'javascript:void(0);', array(
                            'class' => $class_prefix."warning", 
                            'escape' => false,
                            'onclick' => "validar_pcmso('" . (empty($list[0]['CodigoClienteAlocacao']) ? $list[0]['codigo_cliente'] : $list[0]['CodigoClienteAlocacao']) . "','" . (empty($list[0]['codigo_cliente']) ? 'null' : $list[0]['codigo_cliente']) . "','" . (empty($list[0]['CodigoSetor']) ? 'null' : $list[0]['CodigoSetor']) . "','{$list[0]['CodigoCargo']}','" . (!empty($list[0]['CodigoFuncionario']) ? $list[0]['CodigoFuncionario'] : (!empty($list[0]['codigo_funcionario_2']) ? $list[0]['codigo_funcionario_2'] : 'null'  )    )  . "')"));
                    } else {
                        echo $this->Html->div($class_prefix."success", "OK");
                    }                
                ?>
            </td>
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

        echo $html->link('Finalizar Processo', 
                array( 'controller' => 'consultas', 
                       'action' => 'finalizar_processo_pendente',
                       $codigo_cliente, 
                       $tipo), 
                array('class' => 'btn btn-success')); 
    }    

    ?>
    
    <?php echo $html->link('Voltar', array('controller' => 'consultas', 'action' => 'ppra_pcmso_pendente_terceiros'), array('class' => 'btn')); ?>

    <div style="float:right">
        <?php 
            echo $html->link( ( $tipo == 'pcmso' ? 'Preencher com exame clínico' : 'Preencher com ausência de risco' ) , 
                array(  'controller' => 'consultas', 
                        'action' => 'ausencia_risco', 
                        $codigo_cliente, $tipo
                    ), 
                array('class' => 'btn btn-warning', 'title' => ( $tipo == 'pcmso' ? 'Preencher com exame clínico' : 'Preencher com ausência de risco' ) )
            ); 
        ?>
    </div> 
</div>

<div class="modal fade" id="modal_validar_pcmso" data-backdrop="static" style="width: 95%; left: 2%; top: 15%; margin: 0 auto;"></div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function() {
        setup_mascaras(); 
        setup_time(); 
        setup_datepicker();

            function valida_pcmso(codigo_cliente_alocacao, codigo_setor, codigo_cargo, codigo_funcionario){
            console.log("aqui");
        };

        validar_pcmso = function(codigo_cliente_alocacao, codigo_cliente, codigo_setor, codigo_cargo, codigo_funcionario){
            //if(mostra){
                // console.log("aqui");
                var div = jQuery("div#modal_validar_pcmso");
                bloquearDiv(div);
                div.load(baseUrl + "consultas/modal_validar_pcmso/" + codigo_cliente_alocacao + "/" + codigo_cliente + "/" + codigo_setor + "/" + codigo_cargo + "/" + codigo_funcionario + "/" + Math.random());
                $("#modal_validar_pcmso").css("z-index", "1050");
                $("#modal_validar_pcmso").modal("show");
            //} else {
            //    $(".modal").css("z-index", "-1");
            //    $("#modal_validar_pcmso").modal("hide");
            //}    
        }
    })

'); ?> 
