<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('RelatorioSmConsulta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RelatorioSmConsulta', 'element_name' => 'consulta_geral_sm'), 'divupdate' => '.form-procurar')) ?>
    	    <div class="row-fluid inline">
    	    	<?php echo $this->Buonny->input_periodo($this,'RelatorioSmConsulta') ?>
    			<?php echo $this->BForm->input('somente_remonta', array('label'=> 'Somente Remonta', 'type'=>'checkbox', 'class' => 'checkbox inline')); ?>
				<?php echo $this->BForm->input('quantidade_itens', array('class' => 'input-mini just-number', 'placeholder' => 'Qtd. itens', 'label' => false, 'type' => 'text')) ?>
    		</div>
	        <div class="row-fluid inline" id="div_emb_transp">
	            <?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_embarcador','Embarcador', false, 'RelatorioSmConsulta', true) ?>
	            <?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_transportador','Transportador', false, 'RelatorioSmConsulta', true) ?>
				<?php echo $this->BForm->input('codigo_seguradora', array('class' => 'input-large', 'label' => FALSE,'options' => $seguradoras,'empty' => 'Seguradora')) ?>
				<?php echo $this->Buonny->input_codigo_corretora($this, 'codigo_corretora','Corretora', false, 'RelatorioSmConsulta', NULL, TRUE ) ?>						                  
	        </div>
			
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('placa_carreta', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa carreta', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('sm', array('class' => 'input-small just-number', 'placeholder' => 'SM', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('pedido_cliente', array('class' => 'input-small', 'placeholder' => 'Pedido Cliente', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('loadplan', array('class' => 'input-small', 'placeholder' => 'Loadplan', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('nf', array('class' => 'input-small', 'placeholder' => 'NF', 'label' => false, 'type' => 'text')) ?>
				<?php echo $this->BForm->input('cpf', array('class' => 'input-small', 'placeholder' => 'CPF', 'label' => false, 'type' => 'text')) ?>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('inicializacao', array('class' => 'input-medium', 'label' => false, 'title' => 'Inicialização', 'empty'=>'Inicialização', 'options'=>array(1=>'Automática', 2=>'Manual'))) ?>
				<?php echo $this->BForm->input('finalizacao', array('class' => 'input-medium', 'label' => false, 'title' => 'Inicialização', 'empty'=>'Finalização', 'options'=>array(1=>'Automática', 2=>'Manual'))) ?>
				<?php echo $this->BForm->input('posicionando', array('label' => false,'class' => 'input-medium', 'options' => array('0' => 'Posicionando','1' => 'Sim', '2' => 'Não'))) ?>				
				<?php echo $this->BForm->input('UFOrigem', array('label' => false,'class' => 'input-medium','empty'=>'UF Origem','title'=>'UF Origem', 'options' => $EstadoOrigem)) ?>
				<?php echo $this->BForm->input('eras_codigo', array('class' => 'input-large','label' =>FALSE, 'div'=>'control-group input', 'options'=>$estacao, 'empty' => 'Estação de Rastreamento')) ?>
				
				<?php echo $this->BForm->input('alvos_nao_atingidos', array('type'=>'checkbox', 'div' => 'control-group input checkbox input-large', 'label' => 'Alvos Não Atingidos')); ?>
			</div>
			<div class="row-fluid inline">
				<span class="label label-info">Status das Viagens</span>
				 <span class='pull-right'>
	                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("status_viagem")')) ?>
	                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("status_viagem")')) ?>
	            </span>
	            <div id='status_viagem'>
					<?php echo $this->BForm->input('codigo_status_viagem', array('label'=>false, 'options'=>$status_viagens, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
				</div>
			</div>
			
            <div class="row-fluid inline">
                <span class="label label-info">Tecnologias</span>
                <span class='pull-right'>
                    <?= $this->Html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tecnologias")')) ?>
                    <?= $this->Html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tecnologias")')) ?>
                </span>
                <div id='tecnologias'>
                    <?php echo $this->BForm->input('tecn_codigo', array('label' => false, 'class' => 'checkbox inline input-xlarge', 'options' => $tecnologias, 'multiple' => 'checkbox')); ?>
                </div>
            </div>

			<div class="row-fluid inline">
				<span class="label label-info">Tipos de Veículos</span>
				 <span class='pull-right'>
	                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tipo_veiculo")')) ?>
	                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tipo_veiculo")')) ?>
	            </span>
	            <div id='tipo_veiculo'>
					<?php echo $this->BForm->input('codigo_tipo_veiculo', array('label'=>'', 'options'=>$tipos_veiculos, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
				</div>
			</div>
			<div class="row-fluid inline">
				<span class="label label-info">Tipos de Transporte</span>
				 <span class='pull-right'>
	                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("tipo_transporte")')) ?>
	                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("tipo_transporte")')) ?>
	            </span>
	            <div id='tipo_transporte'>
					<?php echo $this->BForm->input('codigo_tipo_transporte', array('label'=>'', 'options'=>$tipos_transportes, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
				</div>
			</div>
	    <div class="row-fluid inline">
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn filtrar', 'id'=>'filtrar')) ?>
	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
	        <?php echo $this->BForm->end() ?>
	    </div>
	   
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();

    	jQuery("#filtrar").click(function(){
			var div = jQuery("div.lista");
			bloquearDiv(div);
			div.load(baseUrl + "relatorios_sm/listagem_consulta_geral_sm/" + Math.random());
		});	

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RelatorioSm/element_name:consulta_geral_sm/" + Math.random())
            jQuery(".lista").empty();
        });  
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
		' . (isset($is_post) && $is_post ? 'jQuery(".btn.filtrar").submit()' : '') . '
		setup_mascaras(); 
    });', false);
?>
<?php if (!empty($filtrado)): ?>
 	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>

