<div class='well'>
  <div id='filtros'>
    <?= $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'pos_configuracoes_clientes'), 'divupdate' => '.form-procurar')) ?>
      
      <div class="row-fluid inline">
            <?= $this->BForm->input('codigo_cliente', array('class' => 'input-mini', 'placeholder' => '', 'label' => 'Cód Cliente')) ?>
    	</div>
      <div class="row-fluid inline">
        <?= $this->BForm->input('codigo_documento_cliente', array('class' => 'input-medium', 'placeholder' => 'Cnpj Cliente', 'label' => 'CNPJ')) ?>
	      <?= $this->BForm->input('razao_social_cliente', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => 'Razão Social')) ?>
	      <?= $this->BForm->input('nome_fantasia_cliente', array('class' => 'input-xlarge', 'placeholder' => 'Nome Fantasia', 'label' => 'Nome Fantasia')) ?>
      </div>
      <div class="row-fluid inline">
        <?= $this->BForm->input('codigo_opco', array('class' => 'input-medium', 'label' => 'OPCO', 'options' => array('0' => 'Opco 1', '1' => 'Opco 2'), 'empty' => 'Selecione um Opco.', 'default' => "")); ?>  
        <?= $this->BForm->input('codigo_business_unit', array('class' => 'input-xlarge', 'label' => 'Business Unit', 'options' => array('0' => 'BU 1', '1' => 'BU 2'), 'empty' => 'Selecione um BU.', 'default' => "")); ?>  
        <?= $this->BForm->input('codigo_depth_structure', array('class' => 'input-xlarge', 'label' => 'Depth Structure', 'options' => array('0' => 'Depth 1', '1' => 'Depth 2'), 'empty' => 'Selecione um Depth.', 'default' => "")); ?>  
      </div>

      <?= $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?= $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?= $this->BForm->end() ?>
  </div>
</div>

<?= $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
     
      $("#limpar-filtro").click(function(){
          bloquearDiv(jQuery(".form-procurar"));
          $(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:pos_configuracoes_clientes/" + Math.random())
      });
        
      function atualizaLista() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "pos_configuracoes/listagem_clientes/" + Math.random());
      }  
      
      atualizaLista();

    });', false);
    
?>
