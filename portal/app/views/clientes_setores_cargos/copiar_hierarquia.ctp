<?php if(!empty($cliente_setor_cargo)):?>

    <?php echo $this->BForm->create('ClienteSetorCargo', array('url' => array('controller' => 'clientes_setores_cargos','action' => 'copiar_hierarquia', $codigo_cliente, $terceiros_implantacao), 'type' => 'post')); ?>
		<div class='well'>
	    	<?php echo $this->BForm->input('codigo_unidade', array('class' => 'input-xxlarge bselect2', 'label' => "Selecione a Unidade que irá copiar a hierarquia selecionada:", 'options' => $unidades, 'empty' => 'Unidades')); ?>
		</div>

		<table class="table table-striped">
			<thead>
				<tr>
					<th class="input-small"><input type="checkbox" id='selAllHierarquia' class="selAllHierarquia" value='1'> Todos </th>
					<th class="input-xxlarge">Unidade</th>
					<th class="input-xxlarge">Setor</th>
					<th class="input-xxlarge">Cargo</th>										
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cliente_setor_cargo as $key => $dados): ?>
					<tr>
						<td class="input-small">
							<?php echo $this->BForm->input("hierarquia.".($key).".codigo",array('type'=>'checkbox','value'=>"{$dados['ClienteSetorCargo']['codigo']}", 'multiple' => 'checkbox', 'class' => 'input-large selHierarquia', 'label' => false)) ?>							
						</td>
						<td class="input-xlarge"><?php echo $dados[0]['razao_social'] ?></td>
						<td class="input-xxlarge"><?php echo $dados['Setor']['descricao'] ?></td>
						<td class="input-xxlarge"><?php echo $dados['Cargo']['descricao'] ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>			
		</table>
		<div class="form-actions">
			<?php echo $this->BForm->submit('Salvar nova Hierarquia', array('div' => false, 'class' => 'btn btn-primary')); ?>
			<?php if($terceiros_implantacao == 'terceiros_implantacao'): ?>
				<?php echo $html->link('Voltar',array('controller' => 'clientes_setores_cargos', 'action' => 'index/'.$codigo_cliente.'/implantacao/null/terceiros_implantacao'), array('class' => 'btn')) ;?>
			<?php else: ?>
				<?php echo $html->link('Voltar',array('controller' => 'clientes_setores_cargos', 'action' => 'index/'.$codigo_cliente.'/implantacao'), array('class' => 'btn')) ;?>
			<?php endif; ?>
		</div>
		
	<?php echo $this->BForm->end() ?>

	
	<script type="text/javascript">
		$(document).ready(function() {
			//funcao para selecionar todos os checks
			$('.selAllHierarquia').click(function(){
				//seleciona os checks
				$('.selHierarquia').prop('checked',$(this).prop('checked'));
			});
		});
	</script>

<?php else:?>
	<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    

