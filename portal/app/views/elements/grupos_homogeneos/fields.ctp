 <div class='well' style="padding-top:0px;">
    <h5>Empresa</h5>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Matriz']['codigo']); ?>
    <strong>Razão Social: </strong><?php echo $this->Html->tag('span', $this->data['Matriz']['razao_social']); ?>
  <hr style="border:1px solid #ccc; margin:10px 0 0;"/>
	<h5>Unidade</h5>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Unidade']['codigo']); ?>
    <strong>Razão Social: </strong><?php echo $this->Html->tag('span', $this->data['Unidade']['razao_social']); ?>
</div>
<div class='well'>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('codigo'); ?>
		<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente)); ?>
		<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
		  <?php if(empty($this->passedArgs[2])): ?>
				<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
			<?php else: ?>
				<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
			<?php endif;  ?>

	</div>  
</div> 

<div class='actionbar-right'>
			<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Grupos Homogênios', 'onclick' => "exibe_campos()"));?>
</div>

	<table class="table table-striped" id="setor_cargo">
	    <thead>
            <tr>
            	<th class="input-xxlarge">Setor</th>
            	<th class="input-xxlarge">Cargo</th>
            	<th class="acoes">Ações</th>
            </tr>
        </thead>
        <tbody>
        	<?php if(isset($edit_mode) && $edit_mode == 1):?>
	        	<?php if(isset($this->data['GrupoHomDetalhe']) && !empty($this->data['GrupoHomDetalhe'])):?>
		        	<?php foreach ($this->data['GrupoHomDetalhe'] as $key => $dados): ?>
						<tr id="linha">
							<td class="input-xxlarge">
								<?php echo $this->BForm->input('GrupoHomDetalhe.'.$key.'.codigo', array('class' => 'grupo_homogeneo_campo', 'type' => 'hidden')); ?>
								<?php echo $this->BForm->input('GrupoHomDetalhe.'.$key.'.codigo_setor', array('class' => 'grupo_homogeneo_campo', 'type' => 'hidden')); ?>
								<?php echo $this->BForm->input('GrupoHomDetalhe.'.$key.'.codigo_cargo', array('class' => 'grupo_homogeneo_campo', 'type' => 'hidden')); ?>

								<?php echo $this->Html->tag('span', $dados['descricao_setor']); ?>
							</td>
							<td class="input-xxlarge">								
								<?php echo $this->Html->tag('span', $dados['descricao_cargo']); ?>
							</td>
							<td class="acoes"><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirItem(this)')) ?></td>
						</tr>
		        	<?php endforeach; ?>
	        	<?php endif; ?>
        	<?php endif; ?>
    </tbody>  
    </table>
  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'grupos_homogeneos', 'action' => 'index', $codigo_cliente, $referencia), array('class' => 'btn')); ?>
</div>

<div style="display:none;">
	<table id="modelo_campos">
		<tr id="linha">
			<td class="input-xxlarge">
				<?php echo $this->BForm->input('GrupoHomDetalhe.x.codigo', array('class' => 'grupo_homogeneo_campo', 'type' => 'hidden')); ?>

				<?php echo $this->BForm->input('GrupoHomDetalhe.x.codigo_setor', array('options' => $setor, 'label' => false, 'class' => 'input-xlarge grupo_homogeneo_campo', 'empty' => 'Selecione')); ?>
			</td>
			<td class="input-xxlarge">
				<?php echo $this->BForm->input('GrupoHomDetalhe.x.codigo_cargo', array('options' => $cargo, 'label' => false, 'class' => 'input-xlarge grupo_homogeneo_campo', 'empty' => 'Selecione')); ?>
			</td>
			<td class="acoes"><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirItem(this)')) ?></td>
		</tr>
	</table>
</div>
<?php echo $this->Javascript->codeBlock('
	function exibe_campos(){
		var key = $("#setor_cargo #linha").length;
		$("#modelo_campos tr#linha").clone().appendTo("#setor_cargo").show().each(function(index, elemento){

			$.each($(elemento).find("input[type=\'hidden\']"), function(id, dados_hidden) {
				$(dados_hidden).attr("name", $(dados_hidden).attr("name").replace("[x]", "["+ key +"]"));
		        $(dados_hidden).attr("id", $(dados_hidden).attr("id").replace("X", key));
			});

			$.each($(elemento).find(".grupo_homogeneo_campo"), function(id, dados) {
				$(dados).attr("name", $(dados).attr("name").replace("[x]", "["+ key +"]"));
	            $(dados).attr("id", $(dados).attr("id").replace("X", key));
			});

		});
	}

	function excluirItem(elemento){
		$(elemento).parent().parent().remove();
	}

'); ?>