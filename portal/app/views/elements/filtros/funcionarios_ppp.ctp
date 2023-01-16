<div class='well'>
	    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?>
	    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
	    <?php echo $this->BForm->hidden('Funcionario.codigo_cliente', array('value' => $this->data['Cliente']['codigo'])); ?>
	</div>
<div class='well'>
  	<div id='filtros'>
	  	<?php echo $bajax->form('Funcionario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Funcionario', 'element_name' => 'funcionarios', 'codigo_cliente' => $this->data['Cliente']['codigo'], 'referencia' => $referencia, 'acao' => 'ppp'), 'divupdate' => '.form-procurar')) ?>
			<?php echo $this->element('funcionarios/fields_filtros') ?>
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	    <?php echo $this->BForm->end() ?>
	</div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
          var codigo_cliente = $("#FuncionarioCodigoCliente").val();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Funcionario/element_name:funcionarios/codigo_cliente:" + codigo_cliente + "/referencia:'.$referencia.'/acao:ppp" + Math.random())
        });
        
        function atualizaLista() {
          var codigo_cliente = $("#FuncionarioCodigoCliente").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "funcionarios/listagem/" + codigo_cliente + "/'.$referencia.'/ppp/" + Math.random());
        }
        
    });', false);
?>
