<div class='well'>
    <div id='filtros'>
    	<?php echo $bajax->form('Profissional', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Profissional', 'element_name' => 'profissionais'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input("Profissional.codigo_documento", array('label' => 'CPF', 'class' => 'input-medium cpf')) ?>
            <?php echo $this->BForm->input("Profissional.nome", array('label' => 'Nome do Profissional', 'class' => 'input-large just-letters')) ?>
            <br>             
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div> 
</div> 

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){ 
        var div = jQuery("div.lista");
        bloquearDiv(div); 
        div.load(baseUrl + "profissionais/listar/" + Math.random());
        setup_mascaras();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Profissional/element_name:profissionais/" + Math.random())
        });
    });', false);?>