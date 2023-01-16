<div class="grupo">
        <div class="fullwide">
            <?php 
                if (count($this->data) > 0): ?>
                <table class="subtable">
                <thead>
                    <tr>
                        <th class="tabela_procuracao_operacoes">Início de vigência</th>
                        <th class="tabela_procuracao_operacoes">Término de vigência</th>
                        <th class="tabela_procuracao_operacoes">Observação</th>
                        <th class="tabela_procuracao_operacoes">Data de inativação</th>
                        <th class="tabela_procuracao_operacoes">Status</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <?php foreach ($this->data as $procuracao): ?>
                <?php $pc = $procuracao['ClienteProcuracao'] ?>
                <?php $procuracao_ainda_eh_valida = ($pc['restante'] > 0 && $pc['data_inativacao'] == null) ?>
                <tr>
                    <td><?php echo $pc['data_vigencia_inicio'] ?></td>
                    <td><?php echo $pc['data_vigencia_fim'] ?></td>
                    <td><?php echo $pc['observacao'] ?></td>
                    <td><?php echo ($pc['data_inativacao']) ? $pc['data_inativacao'] : '-' ?></td>
                    <td><?php echo ($procuracao_ainda_eh_valida) ? 'Ativa' : 'Inativa' ?></td>
                    <td>
                        <?php if($procuracao_ainda_eh_valida): ?>
                            <?php echo $html->link('Inativar', 'javascript:void(0)', array('class' => 'delete finalizar-cliente-procuracao', 'title' => 'Inativar', 'onclick' => "javascript:finalizar_cliente_procuracao({$pc['codigo']}, {$pc['codigo_cliente']})")) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($pc['data_inativacao'] != null && $pc['restante'] > 0): ?>
                            <?php echo $html->link('Reativar', 'javascript:void(0)', array('class' => 'reativar reativar-cliente-procuracao', 'title' => 'Reativar', 'onclick' => "javascript:reativar_cliente_procuracao({$pc['codigo']}, {$pc['codigo_cliente']})")) ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </table>
            <?php else: ?>
                Não existem procurações
            <?php endif; ?>
       </div>
</div>