<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('CidCnae', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'CidCnae', 'element_name' => 'cid_cnae'), 'divupdate' => '.form-procurar')) ?>
      <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_cid10', array('class' => 'input-mini', 'placeholder' => 'CID10', 'label' => false, 'type' => 'text')) ?>
         <?php echo $this->BForm->input('descricao_cid', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?> 
        <?php echo $this->BForm->input('cnae', array('class' => 'input-mini', 'placeholder' => 'CNAE', 'label' => false, 'type' => 'text')) ?>
        <?php echo $this->BForm->input('descricao_cnae', array('class' => 'input-xlarge', 'placeholder' => 'Ramo de atividade', 'label' => false)) ?> 
         <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => "")); ?>  
      </div>        
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:CidCnae/element_name:cid_cnae/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "cid_cnae/listagem/" + Math.random());
        }
        
    });', false);
?>