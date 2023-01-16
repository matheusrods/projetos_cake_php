<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $perfil['Uperfil']['codigo']); ?>
    <strong>Perfil: </strong><?php echo $this->Html->tag('span', $perfil['Uperfil']['descricao']); ?>
</div>
<?php echo $this->BForm->create('Permissao',array('url'=>array('controller'=>'uperfis','action'=>'permissoes_perfil',$this->passedArgs[0])));?>
<div id='tree'>
    
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('controller'=>'uperfis','action'=>'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end() ?>
<?php echo $this->Buonny->link_css('dynatree/skin-vista/ui.dynatree'); ?>
<?php $this->addScript($this->Buonny->link_js('dynatree/jquery.dynatree.min.js')) ?>
<?php $itensDynatree = $this->Tree->itensDynatree($objetos) ?>
<?php $this->addScript($this->Javascript->codeBlock('$(document).ready( function() {
    $("#tree").dynatree({
        checkbox:true, 
        selectMode:3, 
        children:'.$itensDynatree.'
    });
    $("#tree").dynatree("getRoot").visit(function(node){
        node.expand(false);
    });

    jQuery("form").submit(function() {
        var nodes = $("#tree").dynatree("getSelectedNodes");
        var aco_string = null;
        for (var i = 0; i < nodes.length; i++) {
            aco_string = nodes[i].data.title.match(/<span\b[^>]*>(.*?)<\/span>/);
            if (aco_string != null) {
                field = document.createElement("input");
                field.setAttribute("name", "data[Permissao][" + aco_string[1] + "]");
                field.setAttribute("value", 1);
                field.setAttribute("type", "hidden");
                document.getElementById("PermissaoPermissoesPerfilForm").appendChild(field);
            }
        }
        return true;
    });
})')) ?>