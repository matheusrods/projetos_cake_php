<div class='well'>
  <?php echo $bajax->form('PropostaCredenciamento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PropostaCredenciamento', 'element_name' => 'propostas_credenciamento_manutencao_valores_exames'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
    	<?php echo $this->BForm->input('numero', array('class' => 'input-small', 'placeholder' => 'Código', 'label' => false)) ?>
    	<?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => false)) ?>
      	<?php echo $this->BForm->input('codigo_status_proposta_credenciamento', array('label' => false, 'class' => 'input-xlarge', 'default' => 0,'options' => $array_status)); ?>
      	<?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-large', 'default' => '','options' => $array_cadastro)); ?>
      	<?php echo $this->BForm->input('polaridade', array('label' => false, 'class' => 'input-large', 'default' => '','options' => $array_polaridade)); ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery(".lista-manutencao");
        bloquearDiv(div);
        div.load(baseUrl + "propostas_credenciamento/listagem_alteracao_valores_exames/" + Math.random());
        jQuery("#limpar-filtro").click(function() {
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PropostaCredenciamento/element_name:propostas_credenciamento_manutencao_valores_exames/" + Math.random())
        });
    });', false);
?>