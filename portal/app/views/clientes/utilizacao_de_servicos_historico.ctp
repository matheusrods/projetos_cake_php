<div class='form-procurar'>	
    <div class='well'>
	    <?php echo $this->BForm->create('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes', 'action' => 'utilizacao_de_servicos_historico'))) ?>

	    <div class="row-fluid inline">
            <?php echo empty($authUsuario['Usuario']['codigo_cliente']) ? $this->Buonny->input_codigo_cliente($this) : $this->BForm->input('codigo_cliente', array('readonly' => true, 'class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')); ?>
		
			<?php echo $this->BForm->input('mes_faturamento', array('label' => false, 'placeholder' => 'Mês', 'class' => 'input-medium', 'options' => $meses, 'title' => 'Mês de Faturamento')) ?>
	    
	        <?php echo $this->BForm->input('ano_faturamento', array('label' => false, 'placeholder' => 'Ano','class' => 'input-small', 'title' => 'Ano de Faturamento')) ?>
	    
	        <?php echo $this->BForm->input('codigo_produto', array('class' => 'input-medium', 'label' => false, 'options' => $produtos, 'empty' => 'Produtos','title' => 'Produtos')); ?>
		
				<div class="pull-left padding-left-20 padding-top-5">	
	        <?php echo $this->BForm->label('detalhar_servicos', 'Detalhar Serviços', array('class' => 'pull-right margin-left-5')); ?>
	        <?php echo $this->BForm->checkbox('detalhar_servicos',  array('hiddenField' => false, 'class' => 'pull-left')); ?>

				</div>
	    
	        <?php if(isset($authUsuario['Uperfil']['codigo_tipo_perfil']) && $authUsuario['Uperfil']['codigo_tipo_perfil'] == TipoPerfil::INTERNO): ?>
		    
		        <?php echo $this->BForm->input('regiao_tipo_faturamento', array('label' => false, 'placeholder' => 'Faturamento', 'options' => array(1 => 'Total', 0 => 'Parcial'), 'class' => 'input-small','empty' => 'Tipo Faturamento')); ?>
				<?php echo $this->BForm->input('codigo_endereco_regiao', array('label' => false, 'placeholder' => 'Filial', 'class' => 'input-medium', 'options' => $filiais, 'title' => 'Filial', 'empty' => 'Filial')) ?>
			<?php endif; ?>
	    </div>
	    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	    <?php echo $this->BForm->end();?>
	</div>
</div>
<?php if (count($utilizacoes_assinatura) > 0): echo $this->element('clientes/utilizacoes_de_servicos_assinatura_historico', array('utilizacoes_assinatura' => $utilizacoes_assinatura)); ?>
<?php else:?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>	
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>