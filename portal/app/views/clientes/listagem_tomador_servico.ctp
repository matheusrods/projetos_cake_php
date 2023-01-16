

<div class="lista" style="position: relative;">

<div class='well'>
    <?php
    if($this->Buonny->seUsuarioForMulticliente()) { 
        
        echo $this->BForm->create('ClienteListagem', array('autocomplete' => 'off', 'url' => array('controller' => 'Clientes', 'action' => 'listagem_tomador_servico'), 'divupdate' => '.form-procurar'));

        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Cliente');     
        
        echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn'));

        echo $this->BForm->end();

    } else { ?>
    
        <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente_principal['codigo']); ?>
        <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente_principal['razao_social']); ?>
        <?php echo $this->BForm->hidden('Funcionario.codigo_cliente', array('value' => $cliente_principal['codigo'])); ?>
    <?php } ?>
</div>

<?php if(empty($referencia_modulo)): ?>
    <?php if(strpos($codigo_cliente, ',') == 0): ?>
    <div class='actionbar-right'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array( 'controller' => $this->name, 'action' => 'incluir_tomador_servico', $codigo_cliente), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar tomador de serviço'));?>
    </div>
    <?php endif; ?>
<?php endif; ?>



<?php if(!empty($clientes)):?>

    <?php //echo $paginator->options(array('update' => 'div.lista')); ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-mini">Código</th>
                <th>Razão Social</th>
                <th>Nome Fantasia</th>
                <th class="input-medium">CNPJ</th>                
                <th class="input-mini">Ações</th>
            </tr>
        </thead>
        <tbody>
            <tbody>
                <?php foreach($clientes as $cliente) :?>
                    <tr>
                        <td class="input-mini"><?php echo $cliente['Cliente']['codigo'] ?></td>
                        <td title="<?= $cliente['Cliente']['razao_social'] ?>"><div class='truncate input-xlarge'><?= $cliente['Cliente']['razao_social'] ?></div></td>
                        <td title="<?= $cliente['Cliente']['nome_fantasia'] ?>"><div class='truncate input-xlarge'><?= $cliente['Cliente']['nome_fantasia'] ?></div></td>
                        <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>

                        <td class="input-mini">
                            <?php echo $html->link('', array('controller' => 'clientes', 'action' => 'editar_tomador', $cliente['Cliente']['codigo'], $cliente['GrupoEconomico']['codigo_cliente']), array('class' => 'icon-edit', 'title' => 'Editar Tomador')) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>    
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['GrupoEconomico']['count']; ?></td>
                </tr>
            </tfoot>    
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

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

</div> 

<?php echo $this->Js->writeBuffer(); ?>

<script type="text/javascript">
    $(document).ready(function() {});

</script>