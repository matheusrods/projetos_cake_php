<div class="well">
    <strong>Cliente: </strong>
    <span><?php echo $cliente_produto['Cliente']['razao_social']; ?></span>
    <strong>Produto: </strong>
    <span><?php echo $cliente_produto['Produto']['descricao']; ?></span>
    <strong>Status do Produto: </strong>
    <span><?php echo $cliente_produto['MotivoBloqueio']['descricao']; ?></span>
</div>
<div id="editar-contrato">
  <?php echo $this->BForm->create('ClienteProdutoContrato', array('url' => array('action' => 'atualizar', $codigo_cliente_produto), 'enctype' => 'multipart/form-data')); ?>
  <?php echo $this->element('clientes_produtos_contratos/fields', array('edit_mode' => true)); ?>
</div>

<div id="visualizar-contrato" style="display:none"></div>
