<?php echo $this->element('grupos_economicos_clientes/matriz_unidade'); ?>
<div class='well'>
  <div id='filtros'>
   <?php echo $bajax->form('AplicacaoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AplicacaoExame', 'element_name' => 'aplicacao_exames', 'codigo_cliente' => $dados_cliente['Unidade']['codigo']), 'divupdate' => '.form-procurar')) ?>

	   <?php echo $this->BForm->hidden('codigo_cliente', array('value' => $dados_cliente['Unidade']['codigo'])); ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_setor',array('class' => 'input-xlarge bselect2', 'placeholder' => false, 'label' => 'Setor' , 'options' => $setores, 'empty' => 'Todos', 'default' => '')); ?>
        <?php echo $this->BForm->input('codigo_cargo',array('class' => 'input-xlarge bselect2', 'placeholder' => false, 'label' => 'Cargo' , 'options' => $cargos, 'empty' => 'Todos', 'default' => '')); ?>
        <?php echo $this->Buonny->input_nome_funcionario_com_label($this, 'AplicacaoExame', null, $dados_cliente['Unidade']['codigo']);?>
    </div>      
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
          var codigo_cliente = $("#AplicacaoExameCodigoCliente").val();
          bloquearDiv(jQuery(".form-procurar"));
          jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:AplicacaoExame/element_name:aplicacao_exames/codigo_cliente:" + codigo_cliente + "/" + Math.random())
        });
        		
        function atualizaLista() {
          	var codigo_cliente = $("#AplicacaoExameCodigoCliente").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "aplicacao_exames/listagem/" + codigo_cliente + "/" + Math.random());
        }		
        
        jQuery(".bselect2").select2();
    });', false);
?>