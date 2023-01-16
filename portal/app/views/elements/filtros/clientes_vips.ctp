<div class='well'>
    <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'clientes_vips'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">      
      <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'FichaScorecard') ?>  
      <?php echo $this->BForm->input("Cliente.razao_social", array('label' => false, 'class' => 'input-xxlarge', 'readonly'=>false,'placeholder' => 'Nome')) ?>  
        <?php //echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'ClienteVip') ?>
        <?php //echo $this->BForm->input('Cliente.razao_social', array('class' => 'input', 'placeholder' => 'Nome', 'label' => false)) ?>
		
		<?php echo $this->BForm->input('cliente_vip', array(
				'type' => 'select',
				'label' => false,
				'multiple' => 'checkbox',
				'checked' => (isset($this->data['Cliente']['cliente_vip']) && $this->data['Cliente']['cliente_vip'] == 1) ? true : false,
				'options' => array(1 => 'Cliente Vip')
			));
		?>
		
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>

	<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaClientesVipsTeleconsult();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:clientes_vips/" + Math.random())
        });
		setup_codigo_cliente();
		$(document).on("click", ".btn-modal", function(e){
			e.preventDefault();
			var link  = $(this).prop("href");
			var title = $(this).attr("title");
			open_dialog(link, title, 640);
		});
		
		

    });', false);
?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>