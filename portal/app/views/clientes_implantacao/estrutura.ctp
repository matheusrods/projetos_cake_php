<?php if(!empty($matriz)): ?>
    <div class='well'>
        <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?>
        <strong>Cliente: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
    </div>
    <div class="row-fluid margin-bottom-10">
        <div class="pull-right">
            <?php echo $html->link('Hierarquia', array('controller' => 'clientes_setores_cargos', 'action' => 'index', $this->data['Cliente']['codigo'], $referencia, 'null', $terceiros_implantacao), array('class' => 'btn btn-default', 'title' => 'Hierarquia')); ?>
            <a onclick="modal_exportacao('<?php echo $this->data['Cliente']['codigo']; ?>', 1);" role="button" class="btn btn-success" data-toggle="modal">Exportar Dados Funcionários</a>
            <?php echo $html->link('Importar Dados Funcionários', array('controller' => 'importar', 'action' => 'importar_funcionario', $this->data['Cliente']['codigo'], $referencia, $terceiros_implantacao), array('class' => 'btn btn-warning', 'title' => 'Importar Dados Funcionários')); ?>

            <?php echo $this->BForm->hidden('codigo_cliente_h', array('value' => $codigo_cliente)); ?>
            <?php echo $this->BForm->hidden('referencia_h', array('value' => $referencia)); ?>
            <?php echo $this->BForm->hidden('terceiros', array('value' => $terceiros_implantacao)); ?>
           
        </div> 
        <div style= "clear: both;"></div>
        <div id="lista"></div>
    </div>
<?php else: ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<div class="modal hide fade" id="exportarBaseDados" data-backdrop="static" style="width: 66%; left: 15%; top: 10%; margin: 0 auto;"></div>

<?php if(isset($terceiros_implantacao) && $terceiros_implantacao == 'terceiros_implantacao'): ?>
    <?= $this->Html->link('Voltar',array('controller'=>'clientes_implantacao','action'=>'implantation'), array('class' => 'btn')); ?>
<?php else: ?>
    <?= $this->Html->link('Voltar',array('controller'=>'clientes_implantacao','action'=>'index'), array('class' => 'btn')); ?>
<?php endif; ?>

<?php echo $this->Buonny->link_js('estrutura'); ?>