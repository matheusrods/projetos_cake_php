<?php 
if(isset($permissao_cliente_validador) && $permissao_cliente_validador == true){
    if(isset($listagem) && !empty($listagem)){ 
    ?>

    <?php echo $this->BForm->create('PreFaturamento', array('url' => array('controller' => 'clientes', 'action' => 'pre_faturamento_salvar'))); ?>

    <table class="table table-striped" style='width:1800px;max-width:none;'>
        <thead>
            <tr>
                <th>Código Unidade</th>
                <th>Razão Social Unidade</th>
                <th>Nome Fantasia Unidade</th>
                <th>Código do Cliente Pagador</th>
                <th>Razão Social Cliente Pagador</th>
                <th>Nome Fantasia Cliente Pagador</th>
                <th>Produto</th>
                <?php 
                if($forma_de_cobranca == "Per Capita"){ ?>
                    <th>Nome do Funcionário</th>
                    <th>CPF</th>
                    <th>Setor</th>
                    <th>Cargo</th>
                    <th>Código Matrícula</th>
                    <th>Matrícula</th>
                    <th>Data inclusão</th>
                    <th>Admissão</th>
                    <th>Demissão</th>
                    <th>Dias</th>
                    <th>Valor</th>
                    <!-- <th>Subtotal Por Unidade</th>
                    <th>Total do Cliente Pagador</th> -->
                <?php }
                if($forma_de_cobranca == "Exames Complementares"){ ?>
                    <th>Data do resultado</th>
                    <th>Nome do Funcionário</th>
                    <th>Nome da Clínica</th>
                    <th>Exame</th>
                    <th>Centro de custo</th>
                    <th>Valor</th>
                    <!-- <th>Subtotal Por Unidade</th>
                    <th>Total do Cliente Pagador</th> -->
                <?php } ?>
                <th>Status</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($listagem as $key => $v){ ?>
            <tr>
                <td><?php echo $v['codigo_unidade']; ?></td>    
                <td><?php echo $v['razao_cliente']; ?></td>   
                <td><?php echo $v['nome_cliente']; ?></td>
                <td><?php echo $v['clientepagador_codigo']; ?></td>
                <td><?php echo $v['razao_cliente_pagador']; ?></td>
                <td><?php echo $v['nome_cliente_pagador']; ?></td>
                <td><?php echo $v['forma_de_cobranca']; ?></td>
                <?php if($forma_de_cobranca == "Per Capita"){ ?>
                    <td><?php echo $v['nome_funcionario'];?></td>
                    <td><?php echo AppModel::formataCpf($v['cpf_funcionario']);?></td>
                    <td><?php echo $v['descricao_setor'];?></td>
                    <td><?php echo $v['descricao_cargo'];?></td>
                    <td><?php echo $v['codigo_matricula'];?></td>
                    <td><?php echo $v['matricula'];?></td>
                    <td><?php echo $v['data_inclusao'];?></td>
                    <td><?php echo $v['data_admissao'];?></td>
                    <td><?php echo $v['data_demissao'];?></td>
                    <td><?php echo $v['dias_cobrados'];?></th>
                    <td><?php echo 'R$ '.$this->Ithealth->moeda($v['valor']);?></th>
                    <!-- <td><?php //echo 'R$ '.$this->Ithealth->moeda($v['subtotal_unidade']);?></td>
                    <td><?php //echo 'R$ '.$this->Ithealth->moeda($v['total_cliente_pagador']);?></td> -->
                <?php }
                if($forma_de_cobranca == "Exames Complementares"){ ?>
                    <td><?php echo $v['data_realizacao_do_exame'];?></td>
                    <td><?php echo $v['nome_funcionario'];?></td>
                    <td><?php echo $v['nome_fornecedor'];?></td>
                    <td><?php echo $v['exame'];?></td>
                    <td><?php echo $v['centro_custo'];?></td>
                    <td><?php echo 'R$ '.$this->Ithealth->moeda($v['valor']);?></td>
                    <!-- <td><?php //echo 'R$ '.$this->Ithealth->moeda($v['subtotal_unidade']);?></td>
                    <td><?php //echo 'R$ '.$this->Ithealth->moeda($v['total_cliente_pagador']);?></td> -->
                <?php } ?>
                <td>
                    <?php 
                    if(isset($v['status'])){
                        if($v['status'] == "Aprovado"){
                            echo "<span class='label label-success'>".$v['status']."</span>";
                        }
                        if($v['status'] == "Não Aprovado"){
                            echo "<span class='label label-important'>".$v['status']."</span>";
                        }                    
                        if($v['status'] == "Pendente de Aprovação"){
                            echo "<span class='label label-warning'>Pendente de Aprovação</span>"; 
                        }
                    }
                    ?>
                </td>
                <?php 
                if($permissao_cliente_validador){ 
                    $cod_pedido_exame = (isset($v['codigo_pedido_exame'])) ? $v['codigo_pedido_exame'] : null;
                ?>
                <td>
                    <?php 
                    echo $this->BForm->input('status', 
                        array(
                            'type' => 'radio', 'options' => array("Aprovado", "Não Aprovado"), 
                            'name' => "data[$key][status]",
                            'default'=> ($v['status'] != "Não Aprovado" ? 0 : 1),
                            'multiple' => true,
                            'legend' => false, 
                            'label' => array('class' => 'radio inline input-xsmall')
                        )
                    ); 

                    echo $this->BForm->hidden('codigo_pedido_exame', 
                        array(
                            'name' => "data[$key][codigo_pedido_exame]", 
                            'value' => $cod_pedido_exame
                        )
                    );
                    
                    echo $this->BForm->hidden('data_baixa_exame', 
                        array(
                            'name' => "data[$key][data_baixa_exame]",
                            'value' => (isset($v['data_baixa_exame'])) ? $v['data_baixa_exame'] : null
                        )
                    );
                    
                    echo $this->BForm->hidden('exame', 
                        array(
                            'name' => "data[$key][exame]",
                            'value'=> (isset($v['exame'])) ? $v['exame'] : null
                        )
                    );

                    echo $this->BForm->hidden('data_realizacao_do_exame', 
                        array(
                            'name' => "data[$key][data_realizacao_do_exame]",
                            'value' => (isset($v['data_realizacao_do_exame'])) ? $v['data_realizacao_do_exame'] : null
                        )
                    );   
                    
                    echo $this->BForm->hidden('codigo_unidade', 
                        array(
                            'name' => "data[$key][codigo_unidade]",
                            'value' => (isset($v['codigo_unidade'])) ? $v['codigo_unidade'] : null
                        )
                    );  
                    ?>
                </td> 
                <?php } ?>    
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="form-actions">
        <?php echo $this->BForm->submit('Concluir Validação', array('div' => false, 'class' => 'btn btn-primary')); ?>
    </div>

    <?php echo $this->BForm->end(); ?>

    <?php }else{ ?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php } 
}else{ ?>
    <div class="alert">Usuário não tem permissão para acessar esses dados.</div>
<?php } ?>