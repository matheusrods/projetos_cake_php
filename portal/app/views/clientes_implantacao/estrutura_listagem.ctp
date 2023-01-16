    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-xxlarge">Estrutura</th>
                <th class='numeric input-medium'>Quantidades Cadastradas</th>
                <th class="input-mini">Ações</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Unidades</td>
                <td class='numeric input-medium'><?php echo $this->data['Unidade'];?></td>
                <td class="input-mini"><?php echo $html->link('', array('controller' => 'clientes', 'action' => 'index_unidades', $this->data['Cliente']['codigo'], $referencia, 'null', $terceiros_implantacao), array('class' => 'icon-wrench', 'title' => 'Cadastrar Unidades')); ?></td>
            </tr>
            <tr>
                <td>Setores</td>
                <td class='numeric input-medium'><?php echo $this->data['Setor'];?></td>
                <td class="input-mini"><?php echo $html->link('', array('controller' => 'setores', 'action' => 'index', $this->data['Cliente']['codigo'], $referencia, $terceiros_implantacao), array('class' => 'icon-wrench', 'title' => 'Cadastrar Setores')); ?></td>
            </tr>
            <tr>
                <td>Cargos</td>
                <td class='numeric input-medium'><?php echo $this->data['Cargo'];?></td>
                <td><?php echo $html->link('', array('controller' => 'cargos', 'action' => 'index', $this->data['Cliente']['codigo'],$referencia, $terceiros_implantacao), array('class' => 'icon-wrench', 'title' => 'Cadastrar Cargos')); ?></td>
            </tr>
            <tr>
                <td>Funcionários</td>
                <td class='numeric input-medium'><?php echo $this->data['Funcionario'];?></td>
                <td class="input-mini"><?php echo $html->link('', array('controller' => 'funcionarios', 'action' => 'index', $this->data['Cliente']['codigo'], $referencia, 'funcionarios', $terceiros_implantacao), array('class' => 'icon-wrench', 'title' => 'Cadastrar Funcionários'));?></td>
            </tr>
            <tr>
                <td>ASO - Quantidade de vias</td>
                <td class='numeric input-medium'><?=(!empty($matriz['GrupoEconomico']['vias_aso'])) ? $matriz['GrupoEconomico']['vias_aso'] : 0 ?></td>
                <td class="input-mini"><a href="javascript:void(0);" onclick="manipula_modal('modal_vias_aso',1)";><i class="icon-wrench"></i></a></td>
            </tr>
        </tbody>
    </table>
    <?php if($referencia == "implantacao"): ?>
    <div class='form-actions well'>
        <?php echo $html->link('Concluido', array('controller' => 'clientes_implantacao', 'action' => 'atualiza_status', $this->data['ClienteImplantacao']['codigo_cliente'], 'estrutura', 'C', $terceiros_implantacao ), array('class' => 'btn btn-primary', 'title' => 'Concluir Processo Estrutura')); ?>
        
        <?php if(isset($terceiros_implantacao) && $terceiros_implantacao == 'terceiros_implantacao'): ?>
            <?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'implantation'), array('class' => 'btn', 'title' => 'Voltar para Lista Implantação')); ?>
        <?php else: ?>
            <?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'index'), array('class' => 'btn', 'title' => 'Voltar para Lista Implantação')); ?>
        <?php endif; ?>
    </div>
    <?php endif;?>
    <div class="modal fade" id="modal_vias_aso" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="position: static;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="gridSystemModalLabel">ASO - Quantidade de vias</h4>
                    <div id = 'erro_modal' class="alert alert-error" style="display: none;">                       
                            <p>Preencha a quantidade corretamente</p>
                    </div>          
                </div>
                <div class="modal-body" style="max-height: 100%;">
                    <div class="row-fluid">
                       <!-- Número: <input type="text" value="3" class="input-small" -->
                        <?php echo $this->BForm->input('GrupoEconomico.vias_aso', array('value' =>  $matriz['GrupoEconomico']['vias_aso'], 'class' => 'input-small', 'label' => 'Número' , 'type' => 'text')); ?>

                    </div>
                </div>
                <div id="rodape_botoes" class="form-actions center">
                    <a href="javascript:void(0);" onclick="manipula_modal('modal_vias_aso',0)"; class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Sair</a>

                    <a href="javascript:void(0);" class="btn btn-success btn-lg" onclick="salvar_vias_aso(<?php echo $matriz['GrupoEconomico']['codigo']?>)">
                        <i class="glyphicon glyphicon-share"></i> Salvar
                    </a>
                </div>              
            </div>
        </div>
    </div>
   