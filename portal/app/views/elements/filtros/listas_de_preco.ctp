<div class='well'>
  <?php echo $bajax->form('ListaDePreco', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ListaDePreco', 'element_name' => 'listas_de_preco'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
    	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor','ListaDePreco');?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "listas_de_preco/listagem/" + Math.random());
		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ListaDePreco/element_name:listas_de_preco/" + Math.random())
        });
    });', false);
?>