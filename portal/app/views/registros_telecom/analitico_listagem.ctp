<?php if(!empty($registros_telecom)):?>

<?php 
    echo $paginator->options(array('update' => 'div.lista'));
?>
    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-medium"><?php echo $this->Paginator->sort('Nome', 'Usuario.nome'); ?></th>
            <th class="input-small"><?php echo $this->Paginator->sort('Login', 'Usuario.apelido'); ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Operadora', 'RegistroTelecom.codigo_operadora'); ?></th>
            <th class="input-small"><?php echo $this->Paginator->sort('Tipo Cobrança', 'TipoRetorno.descricao'); ?></th>
            <th class="input-small"><?php echo $this->Paginator->sort('Contato', 'RegistroTelecom.codigo_tipo_retorno'); ?></th>
            <th class="input-medium"><?php echo $this->Paginator->sort('Departamento', 'RegistroTelecom.departamento'); ?></th>
            <th class="input-mini numeric"><?php echo $this->Paginator->sort('Qtde.', 'RegistroTelecom.quantidade'); ?></th>
            <th class="input-mini numeric"><?php echo $this->Paginator->sort('Valor', 'RegistroTelecom.valor'); ?></th> 
        </tr>
    </thead>
    <tbody>
    <?php foreach ($registros_telecom as $registro): ?>
        <tr>
            <td><?php echo $registro[0]['nome'] ?></td>
            <td><?php echo $registro[0]['apelido'] ?></td>
            <td><?php echo ($registro[0]['operadora_descricao']) ?></td>
            <td><?php echo $registro[0]['tipo_retorno_descricao'] ?></td>
            <td><?php echo (in_array($registro[0]['codigo_tipo_retorno'],Array(1,3,5,7,8,9,11)) ? $this->Buonny->telefone($registro[0]['identificador']) : $registro[0]['identificador']) ?></td>
            <td><?php echo utf8_encode($registro[0]['departamento_descricao']) ?></td>

            <td class="numeric"><?php echo number_format($registro[0]['quantidade'],1,',','') ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($registro[0]['valor']) ?></td>
        </tr>
    <?php endforeach ?>
</tbody>
    <tfoot>
        <tr>
            <th class="numeric"><?=$resultado[0]['registros']?></th>
            <th></th>
            <th></th>
            <th></th>
            <th class="numeric"><?=$resultado[0]['identificadores']?></th>
            <th></th>
            <th class="numeric"><?php echo $this->Buonny->moeda($resultado[0]['quantidade'],Array('format'=>true)) ?></th>
            <th class="numeric"><?php echo $this->Buonny->moeda($resultado[0]['valor'],Array('format'=>true)) ?></th>
        </tr>
    </tfoot>    
</table>
<div class='row-fluid'>
    <div class='numbers span5'>
        <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span7'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        
    </div>
</div>
<?php else: ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>   
<?php echo $this->Js->writeBuffer(); ?>
