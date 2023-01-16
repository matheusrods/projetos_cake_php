<?php echo $paginator->options(array('update' => 'div#lista')); ?>    
<div class="row-fluid">
    <span class="span4">
        <strong>Total:</strong> 
        <?php echo $this->Paginator->params['paging']['LogFaturamentoExcluido']['count']; ?>
    </span>
</div>
<?php if(isset($log_faturamento)): ?>    
<div class='row-fluid'>
    <table class="table table-striped table-bordered " style='width:2600px;max-width:none' >
        <thead>
            <tr>            
                <th class='input-small'>Razão Social</th>
                <th class='input-small'>Excluído Por</th>
                <th class='input-small'>Motivo Exclusão</th>
                <th class='input-small'>Operação</th>
                <th class='input-small'>Profissional</th>
                <th class='input-small'>CPF</th>
                <th class='input-small'>Categoria</th>
                <th class='input-small'>Data</th>
                <th class='input-small'>Número Consulta</th>
                <th class='input-small'>Placa</th>
                <th class='input-small'>Carreta</th>
                <th class='input-small'>BiTrem</th>
                <th class='input-small'>Carga</th>
                <th class='input-small'>Origem</th>
                <th class='input-small'>Destino</th>
            </tr>
        </thead>
        <tbody>   
        <?php foreach($log_faturamento as $log): ?>
            <tr>            
                <td><?php echo $log[0]['codigo_cliente'].' - '.$log[0]['razao_social']; ?></td>
                <td><?php echo $log[0]['usuario']; ?></td>
                <td><?php echo $log[0]['motivo_exclusao']; ?></td>
                <td><?php echo $log[0]['tipo_operacao']; ?></td>
                <td><?php echo $log[0]['profissional']; ?></td>
                <td><?php echo COMUM::formatarDocumento($log[0]['cpf']); ?></td>
                <td><?php echo $log[0]['profissional_tipo']; ?></td>
                <td><?php echo $log[0]['data_exclusao']; ?></td>
                <?php $existe_perfiladequado = stristr($log[0]['observacao'],'PERFIL ADEQUADO AO RISCO'); ?>
                <?php if ($existe_perfiladequado!=''){ ?>
                    <td><?php echo $log[0]['num_consulta']; ?></td>
                <?php }else{ ?>
                    <td></td>
                <?php } ?>                
                <td><?php echo COMUM::formatarPlaca(strtoupper($log[0]['placa'])); ?></td>
                <td><?php echo COMUM::formatarPlaca(strtoupper($log[0]['carreta'])); ?></td>
                <td><?php echo COMUM::formatarPlaca(strtoupper($log[0]['bitrem'])); ?></td>
                <td><?php echo $log[0]['carga_tipo_descricao']; ?></td>
                <td><?php echo $log[0]['endereco_origem']; ?></td>
                <td><?php echo $log[0]['endereco_destino']; ?></td>
            </tr>
        <?php endforeach; ?>    
        </tbody>
    </table>
</div>
<?php endif; ?>
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