<?php

    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        echo $javascript->codeBlock("close_dialog('{$this->Buonny->flash()}')");
        exit;
    } 
?>

<div class='well'>
    <?php echo $this->Bajax->form('ClienteProduto', array('url' => array('controller' => 'clientes_produtos', 'action' => 'cadastrar_valores_servicos_do_produto',$codigo_produto,$codigo_cliente))) ?>
    <div class="row-fluid inline">
        <table class="table table-striped table-bordered tablesorter">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th class="numeric">Valor</th>
                </tr>
            </thead>
            <tbody>
				<?php foreach ($servicos as $servico): ?>
					<tr>
						<td><?= $servico['Servico']['descricao']; ?></td>
                        <td class="numeric"><?= $this->BForm->input($servico['Servico']['codigo'], array('class' => 'input-medium numeric moeda', 'label' => false ,'value' => $this->Buonny->moeda(isset($servicovalordefault[$servico['Servico']['codigo']]) ? $servicovalordefault[$servico['Servico']['codigo']] : 0),'div' => '')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>        
        </table>
    </div>    
    <div class="form-actions">
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success', 'onclick' => '$(".lista").html("");')); ?>
        <?php echo $html->link('Voltar', '#', array('class' => 'btn closeDialog', 'onclick' => 'close_dialog();')); ?>
    </div>
    
    <?php echo $this->BForm->end();?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        
    });', false);
?>