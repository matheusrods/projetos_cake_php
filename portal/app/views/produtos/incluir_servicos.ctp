<?php echo $this->BForm->create('Produto', array('url' => array('action' => 'incluir_servicos',$this->passedArgs[0]))); ?>

<div class="well">
  <strong>Código do Produto: </strong><?= $produto['Produto']['codigo'] ?>
  <strong>Descrição do Produto: </strong><?= $produto['Produto']['descricao'] ?>
</div>

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('servicos', array('label' => 'Selecionar serviço', 'class' => 'input-xlarge', 'options' => $servicos, 'empty' => 'Selecione um serviço')); ?>
	<?php echo $this->BForm->input('servico_novo', array('label' => 'Cadastrar novo serviço', 'class' => 'input-xlarge')); ?>
</div>  

<div class='form-actions'>
  <?php echo $this->BForm->submit('Incluir Serviço', array('div' => false, 'class' => 'btn btn-success')); ?>
  <?= $html->link('Voltar', array('controller' => 'produtos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>


<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-xxlarge">Serviços</th>
            <th class="acoes input-mini"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($produtos_servicos as $produto_servico): ?>
        <tr>
            <td><?= $produto_servico['Servico']['descricao'] ?></td>
			<td>            
			<?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusProdutoServico('{$produto_servico['ProdutoServico']['codigo']}','{$produto_servico['ProdutoServico']['ativo']}')"));?>
            <?php if($produto_servico['ProdutoServico']['ativo']== 0): ?>
                <span class="badge-empty badge badge-important" title="Desativado"></span>
            <?php elseif($produto_servico['ProdutoServico']['ativo']== 1): ?>
                <span class="badge-empty badge badge-success" title="Ativo"></span>
            <?php endif; ?>
            

            <?php if(!$produto['Produto']['controla_volume']) echo $this->Html->link('', array('controller' => 'produtos_servicos', 'action' => 'excluir', $produto_servico['ProdutoServico']['codigo']), array('escape' => false, 'class' => 'icon-trash', 'title' => 'Excluir'), "Deseja realmente excluir o serviço desse produto?"); ?></td>
            
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

<?php echo $this->BForm->end(); ?>

<?php echo $javascript->codeblock(
	'jQuery(document).ready(function() {
		$("#ProdutoServicos").change(function(){
			if($(this).val() != "") {
				$("#ProdutoServicoNovo").attr("disabled",true);
			} else {
				$("#ProdutoServicoNovo").attr("disabled",false);
			}
		});
		$("#ProdutoServicoNovo").change(function(){
			if($(this).val() != "") {
				$("#ProdutoServicos").attr("disabled",true);
			} else {
				$("#ProdutoServicos").attr("disabled",false);
			}
		});
        
        
	});
    function atualizaStatusProdutoServico(codigo, status){
        $.ajax({
            type: "POST",
            url: baseUrl + "produtos_servicos/editar_status_produto_servico/" + codigo + "/" + status + "/" + Math.random(),                
            success: function(data){
                if(data == 1){
                    window.location.reload();
                } else {                        
                    alert(0,"Não foi possível mudar o status!");
                }
            },
            error: function(erro){                    
                alert(0,"Não foi possível mudar o status!");
            }
        });
    }
'); ?> 


