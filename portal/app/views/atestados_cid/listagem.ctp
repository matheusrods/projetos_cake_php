<table class="table table-striped">
    <thead>
    	<tr>
	    	<th class="input-small">Código CID 10</th>
	        <th class="input-xxlarge">Nome</th>
	        <th></th>    	
    	</tr>
    </thead>
    <?php if(!empty($cids)):?>
        <tbody>
            <?php foreach($cids as $cid):?>
                <tr>
                	<td class="input-small"><?php echo $cid['Cid']['codigo_cid10'];?></td>
                    <td class="input-xxlarge"><?php echo $cid['Cid']['descricao'];?></td>
                    <td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => 'excluirAtestadoCid('.$cid['AtestadoCid']['codigo'].');', 'class' => 'icon-trash ', 'title' => 'Excluir Cid')); ?></td>
                </tr>
            <?php endforeach;?>
        </tbody>   
    <?php else:?>
        <tr>
            <td colspan="3">
                <div>Nenhum dado foi encontrado.</div>
            </td>
        </tr>
    <?php endif;?>    
</table>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
    });
    
    function excluirAtestadoCid(codigo){
        if (confirm('Deseja realmente excluir ?')){
            $.ajax({
                type: 'POST',        
                url: baseUrl + 'atestados_cid/excluir/' + codigo +  '/' + Math.random(),        
                dataType : 'json',
                success : function(data){ 
                    atualizaAtestadoCid(); 
                },
                error : function(error){
                    console.log(error);
                }
            }); 
        }
    }

    function atualizaAtestadoCid(){
        var div = jQuery('#atestado-cid-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'atestados_cid/listagem/".$codigo_atestado."/' + Math.random());
    }
    ");
?>