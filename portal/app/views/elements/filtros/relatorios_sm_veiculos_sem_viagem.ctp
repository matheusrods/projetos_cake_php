<?php 
	$filtro = $this->data['RelatorioSm']; 
	unset($filtro['quantidade_itens']);
	if(empty($filtro['codigo_cliente'])) unset($filtro['codigo_cliente']);
	$filtrado = (isset($filtro) && $filtro != null);
?>
<div class='well'>
	<h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
	    <?php echo $this->Bajax->form('RelatorioSm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RelatorioSm', 'element_name' => 'relatorios_sm_veiculos_sem_viagem'), 'divupdate' => '.form-procurar')) ?>
			<div class="row-fluid inline">
				<?php echo $this->Buonny->input_cliente_tipo($this, 0, $clientes_tipos); ?>
				<?php echo $this->BForm->input('quantidade_itens', array('class' => 'input-mini', 'placeholder' => 'Qtd. itens', 'label' => false, 'type' => 'text')) ?>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'placeholder' => 'Placa', 'label' => false, 'type' => 'text')) ?>
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
<!-->	    <div class="row-fluid inline">
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
	        <?php echo $this->BForm->end() ?>
	    </div> -->
	</div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
        atualizaListaRelatorioSmVeiculosSemViagem();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RelatorioSm/element_name:relatorios_sm_veiculos_sem_viagem/" + Math.random())
        });  
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
        
        setup_mascaras(); 
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>