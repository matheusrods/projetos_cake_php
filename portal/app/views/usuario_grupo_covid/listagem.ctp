<?php if(!empty($listagem)):?>

    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>

    <div class="double-scroll">
        <table class="table table-striped" style="max-width: 200% !important; width: 200% !important;">
            <thead>
                <tr>
                    <th style="width: 100px">Empresa</th>
                    <th style="width: 100px">Unidade</th>
                    <th style="width: 100px">Setor</th>
                    <th style="width: 100px">Cargo</th>
                    <th style="width: 100px">Funcionário</th>
                    <th>Matricula</th>
                    <th>CPF</th> 
                    <th>Telefone</th> 
                    <th>Email</th> 
                    <th>Nome Contato de Emergência</th> 
                    <th>E-mail</th> 
                    <th>Telefone</th> 
                    <th>Grau de Parentesco</th> 
                    <th>Data Resposta Questionário</th> 
                    <!-- <th>Data Resultado Exame</th>   -->
                    <!-- <th>Data Fim Afastamento</th>  -->
                    <!-- <th>Data Óbito</th>  -->
                    <th>Grupo</th>
                    <th>Passaporte</th>
                    <th>Ações</th>            
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listagem as $dados): ?>
                <tr>
                    <td><?php echo $dados[0]['empresa'] ?></td>
                    <td><?php echo $dados[0]['unidade_nome_fantasia'] ?></td>
                    <td><?php echo $dados[0]['setor_descricao'] ?></td>
                    <td><?php echo $dados[0]['cargo_descricao'] ?></td>
                    <td><?php echo $dados[0]['funcionario_nome'] ?></td>                
                    <td><?php echo $dados[0]['funcionario_matricula'] ?></td> 
                    <td><?php echo $dados[0]['funcionario_cpf'] ?></td>
                    <td><?php echo $dados[0]['telefone'] ?></td>
                    <td><?php echo $dados[0]['email'] ?></td>
                    <td><?php echo $dados['UsuarioContatoEmergencia']['nome'] ?></td>
                    <td><?php echo $dados['UsuarioContatoEmergencia']['email'] ?></td>
                    <td><?php echo $dados['UsuarioContatoEmergencia']['telefone'] ?></td>
                    <td><?php echo $dados['UsuarioContatoEmergencia']['grau_parentesco'] ?></td>
                    <td><?php echo (!is_null($dados[0]['data_respondeu'])) ? date("d/m/Y", strtotime($dados[0]['data_respondeu'])) : ''; ?></td>
                    <!-- <td></td> -->
                    <!-- <td><?php // echo ($dados[0]['fim_quarentena'] ? date("d/m/Y", strtotime($dados[0]['fim_quarentena'])) : '') ?></td> -->
                    <!-- <td></td> -->
                    <td><?php echo strtoupper($dados[0]['grupo']); ?></td>
                    <td><?php echo (!is_null($dados[0]['passaporte'])) ? ($dados[0]['passaporte'] == 1) ? 'VERDE':'VERMELHO' : 'SEM PASSAPORTE HOJE.'; ?></td>
                    <td>
                        <?php echo $this->Html->link('', array('action' => 'editar', $dados[0]['codigo_usuario'], $dados[0]['codigo_cliente_funcionario'], $dados[0]['codigo_funcionario_setor_cargo']), array('class' => 'icon-edit ', 'data-toggle' => 'tooltip', 'title' => 'Editar')); ?>                    
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
           
        </table>
    </div>
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

<?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>


<?php echo $this->Buonny->link_js('jquery.doubleScroll'); ?>

<script>
    $(document).ready(function(){
        $('.double-scroll').doubleScroll();
    });
</script> 
