<table class="table table-striped">
  <thead>
    <tr>
      <th>Retorno</th>
      <th>Contato ( Fone / Email )</th>
      <th>Tipo</th>
      <th>Representante</th>
      <th></th>
    </tr>
  </thead>
  <?php if(!empty($contatos)):?>
  <tbody>
	<?php foreach ($contatos as $contato): ?>
	  <?php $descricao_contato = $contato['FornecedorContato']['ddd'].$contato['FornecedorContato']['descricao']; ?>
	  <?php if (in_array($contato['FornecedorContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
	  <?php    $descricao_contato = Comum::formatarTelefone($descricao_contato);?>
	  <?php endif; ?>
	  <tr>
	      <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
	      <td><?php echo $descricao_contato ?></td>
	      <td><?php echo $contato['TipoContato']['descricao'] ?></td>
	      <td><?php echo $contato['FornecedorContato']['nome'] ?></td>
	      <td> 	      
	        <?php echo $html->link('', array('controller' => 'fornecedores_contatos', 'action' => 'editar', $codigo_fornecedor, $contato['FornecedorContato']['codigo']), array('escape' => false, 'class' => 'icon-edit dialog_contato', 'title' => 'Editar')) ?>
			<?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => 'excluirFornecedorContato('.$contato['FornecedorContato']['codigo'].');', 'class' => 'icon-trash ', 'title' => 'Excluir Contato')); ?>
		</td>
	  </tr>
	<?php endforeach; ?>
	</tbody>
	<?php else:?>
            <tr>
                <td colspan="5">
                    <div>Nenhum dado foi encontrado.</div>
                </td>
            </tr>    
        <?php endif;?>
</table>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
    });

    function excluirFornecedorContato(codigo){
        if (confirm('Deseja realmente excluir ?')){
            $.ajax({
                type: 'POST',        
                url: baseUrl + 'fornecedores_contatos/excluir/' + codigo +  '/' + Math.random(),        
                dataType : 'json',
                success : function(data){ 
                   atualizaFornecedorContato();
                   atualizaFornecedorContatoAgendamento();
                },
                error : function(error){
                    console.log(error);
                }
            }); 
        }
    }
    function atualizaFornecedorContato(){
      var div = jQuery('#fornecedor-contato-lista');
      bloquearDiv(div);
      div.load(baseUrl + 'fornecedores_contatos/contatos_por_fornecedores/".$codigo_fornecedor."/' + Math.random());
    }

    function atualizaFornecedorContatoAgendamento(){
      var div = jQuery('#fornecedor_contato_agendamento_lista');
      bloquearDiv(div);
      div.load(baseUrl + 'fornecedores_contatos/contatos_por_fornecedores_agendamento/".$codigo_fornecedor."/' + Math.random());
    }
    ") 
?>