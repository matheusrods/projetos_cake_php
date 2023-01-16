<div class='well'>
  <div id='filtros'>
    <?= $bajax->form('PosConfiguracoes', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PosConfiguracoes', 'element_name' => 'pos_configuracoes'), 'divupdate' => '.form-procurar')) ?>
      
      <div class="row-fluid inline">
        <?= $this->BForm->input('codigo_cliente', array('class' => 'input-mini', 'placeholder' => '', 'label' => 'Cód Cliente<h11 style="color:red">*</h11>', 'readonly'=>true)) ?>
	      <?= $this->BForm->input('razao_social_cliente', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => 'Razão Social<h11 style="color:red">*</h11>', 'readonly'=>true)) ?>
	      <?= $this->BForm->input('nome_fantasia_cliente', array('class' => 'input-xlarge', 'placeholder' => 'Nome Fantasia', 'label' => 'Nome Fantasia<h11 style="color:red">*</h11>', 'readonly'=>true)) ?>
      </div>
      <div class="row-fluid inline">
        <?= $this->BForm->input('codigo_pos_ferramenta', array('class' => 'input-xlarge', 'label' => 'Ferramentas', 'options' => array('1' => 'Plano de Ação', '2' => 'Safety walk & talk', '3' => 'Observador EHS'), 'empty' => 'Selecione uma Ferramenta')); ?>
        <?= $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => 'Descrição')); ?> 
        <?= $this->BForm->input('chave', array('class' => 'input-xlarge', 'placeholder' => 'Chave', 'label' => 'Descrição')); ?> 
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
          $(".form-procurar").load(baseUrl + "/filtros/limpar/model:PosConfiguracoes/element_name:pos_configuracoes/" + Math.random())
      });
        
      function atualizaLista() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "pos_configuracoes/listagem/" + Math.random());
      }  
      
      atualizaLista();

    });', false);
    
?>
