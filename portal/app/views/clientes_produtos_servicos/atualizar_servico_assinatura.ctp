<?php echo $this->BForm->create('ClienteProdutoServico2', array('url' => array('controller' => 'clientes_produtos_servicos', 'action' => 'atualizar_servico_assinatura', $codigo_cliente, $codigo_produto, $codigo_servico, $codigo))); ?>
<?php echo $this->BForm->hidden('codigo',				  array('value' => $codigo)); ?>
<?php echo $this->BForm->hidden('codigo_produto',		  array('value' => $codigo_produto)); ?>
<?php echo $this->BForm->hidden('codigo_servico',		  array('value' => $codigo_servico)); ?>
<?php echo $this->BForm->hidden('codigo_cliente',		  array('value' => $codigo_cliente)); ?>
<?php echo $this->BForm->hidden('codigo_cliente_produto', array('value' => $codigo_cliente_produto)); ?>

<div class="row-fluid">
    <div>
        <strong>Produto..:</strong>
        <?php echo $produto_nome ?>
    </div>

    <div>
        <strong>Serviço..:</strong>
        <?php echo $servico_nome ?>
    </div>

</div>

<br />

<div id='controle-de-volume'>
	<div class='row-fluid inline' onclick='javascript:return false'>
		<?php echo $this->BForm->input('tipo_premio_minimo', array('legend' => false, 'type' => 'radio', 'label' => array('class' => 'radio inline'), 'options' => array(1 => 'por Produto', 2 => 'por Serviço'))); ?>
	</div>
    <div class='row-fluid inline'>
        <?php 
        $valor_premio_minimo = empty($valor_premio_minimo) || !is_numeric($valor_premio_minimo) ? 0 : $valor_premio_minimo;

        echo $this->BForm->input('valor_premio_minimo', array(
        'type' => $produto_quantitativo ? 'text' : 'hidden', 
        'label' => 'Prêmio Mínimo(R$)', 
        'class' => 'numeric moeda input-medium', 
        'value' => $this->Buonny->moeda($valor_premio_minimo)
        )); 
        ?>
        <?php echo $this->BForm->input('qtd_premio_minimo', array('type' => $produto_quantitativo ? 
        'text' : 'hidden','label' => 'Qtd Prêmio Mínimo', 'class' => 'numeric input-medium', 'value' => $qtd_premio_minimo) ); ?>
        <?php echo $this->BForm->input('valor_maximo', array('type' => $produto_quantitativo ? 'text' : 'hidden','label' => 'Teto Máximo(R$)', 'class' => 'numeric moeda input-medium', 'value' => $this->Buonny->moeda($valor_maximo))); ?>
        <?php echo $this->BForm->input('quantidade', array('type' => 'hidden', 'value' => '1')); ?>
        
        <?= $this->BForm->input('valor', array('value' => $this->Buonny->moeda($valor), 'label' => 'Valor (R$)', 'class' => 'input-medium numeric moeda', 'maxlength' => 14), array('edit' => true) ); ?>
        
        <?php echo $this->BForm->input('codigo_cliente_pagador', array('label' => 'Cód. Cliente Pagador', 'class' => 'numeric input-medium', 'maxlength' => 10, 'value' => $codigo_cliente_pagador)); ?>
        
    </div>
    <div class='row-fluid inline'>
        <?php 
            if ($consulta_motorista) {
                echo $this->BForm->input("consulta_embarcador", array('class' => 'input-mini', 'label' => "Bloquear exibição do Número de liberação no sistema Teleconsult", 'options' => array(1 => 'sim', 0 => 'não'), 'value' => $consulta_embarcador, 'name' => "data[ClienteProdutoServico2][consulta_embarcador]"));
            }
        ?>
    </div>
</div>

<div class="form-actions submit_box">
    <input type="submit" value="Salvar" class="btn btn-primary" />
    <?php echo $html->link('Voltar', array('controller' => 'clientes_produtos', 'action' => 'assinatura'), array('class'=>'btn')); ?>
</div>

<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
    });', false);
?>