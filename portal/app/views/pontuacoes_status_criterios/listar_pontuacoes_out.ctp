



	<div class='actionbar-right'>
	    <!--- Aqui fica botão incluir  -->
	    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('controller' => 'PontuacoesStatusCriterios','action' => 'incluir'), array('class' => 'btn btn-success', 'escape' => false ));?>
	</div>



<?php if($seguradora && $cliente):?>
    <div class="well">  
        <strong> Codigo: </strong><?php echo $cliente['Cliente']['codigo'];?>
        <strong> Cliente: </strong><?php echo $cliente['Cliente']['razao_social'];?><br/>    
        <strong> Codigo:  </strong><?php echo $seguradora['Seguradora']['codigo'];?>
        <strong> Seguradora:  </strong><?php echo $seguradora['Seguradora']['nome']; ?>
    </div>
<?php elseif($cliente):?>
	<div class="well">  
	     <strong> Codigo: </strong><?php echo $cliente['Cliente']['codigo'];?>
	     <strong> Cliente: </strong><?php echo $cliente['Cliente']['razao_social'];?><br/>
	</div>
<?php elseif($seguradora):?>
	<div class="well">     
	    <strong> Codigo:  </strong><?php echo $seguradora['Seguradora']['codigo'];?>
	    <strong> Seguradora:  </strong><?php echo $seguradora['Seguradora']['nome']; ?>
	</div>
<?php endif;?>

<?php if($listagem):?>    

    <table class="table table-striped">
        
        <thead>
            <tr>
                <th>Critérios</th>
                <th class='input-large'>Status</th>
                <th class='input-large'>Pontos</th>
                <th style="width:13px"></th>
                <th style="width:13px"></th>

                
            </tr>
        </thead>
        <tbody>
            
            <?php 
                $codigo_criterio_anterior = null;
                $criterio = 0;
            ?>
            <?php foreach($listagem as $clientes): ?>
                <?php if ($codigo_criterio_anterior != $clientes['Criterio']['codigo']): ?>

                    <tr id="<?php echo $clientes['Criterio']['codigo']; ?>" style="cursor:pointer" criterio="<?=++$criterio?>"  >
                        <td>
                            <i class="icon-chevron-right"></i> 
                            <strong><?php echo $clientes['Criterio']['descricao']; ?></strong>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>

                    <tr style="display:none" class="criterio-<?=$criterio?>">
                         <td>&nbsp;</td>
                        <td><?php echo $clientes['StatusCriterio']['descricao']; ?></td>
                        <td><?php echo $clientes['PontuacoesStatusCriterio']['pontos']; ?></td>
                        
                        <td><?php echo $html->link('', array('controller' => 'pontuacoes_status_criterios', 'action' => 'editar',$clientes['PontuacoesStatusCriterio']['codigo'],$clientes['Criterio']['codigo'] ), array('class' => 'icon-edit', 'title' => 'Alterar ')); ?>
                        </td>
                        <td>
                                <?php echo $html->link('', array('controller' => 'pontuacoes_status_criterios', 'action' => 'delete', $clientes['PontuacoesStatusCriterio']['codigo'], rand()), array('onclick' => 'return confirm("Confirma a exclusão do Status  Critério?")', 'title' => 'Excluir Critério', 'class' => 'icon-trash')) ?>
                        </td>
                        
                    </tr>
                
                <?php else: ?>
                    
                    <tr style="display:none" class="criterio-<?=$criterio?>">
                         <td>&nbsp;</td>
                        <td><?php echo $clientes['StatusCriterio']['descricao']; ?></td>
                        <td><?php echo $clientes['PontuacoesStatusCriterio']['pontos']; ?></td>
                         <td><?php echo $html->link('', array('controller' => 'pontuacoes_status_criterios', 'action' => 'editar',$clientes['PontuacoesStatusCriterio']['codigo'],$clientes['Criterio']['codigo'] ), array('class' => 'icon-edit', 'title' => 'Alterar ')); ?>
                        </td>
                        <td>
                                <?php echo $html->link('', array('controller' => 'pontuacoes_status_criterios', 'action' => 'delete', $clientes['PontuacoesStatusCriterio']['codigo'], rand()), array('onclick' => 'return confirm("Confirma a exclusão do Status  Critério?")', 'title' => 'Excluir Critério', 'class' => 'icon-trash')) ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php $codigo_criterio_anterior = $clientes['Criterio']['codigo'] ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
        echo $this->Javascript->codeBlock('
            $(function() {
                $("tr a").click(function(){
                    window.location = $(this).attr("href");
                    return false;
                });

                $("tr").click(function(){
                    $(".criterio-"+$(this).attr("criterio")).toggle();
                    
                    if($(this).find("i.icon-chevron-down").length > 0){
                        $(this).find("i").addClass("icon-chevron-right");
                        $(this).find("i").removeClass("icon-chevron-down");
                    }else{
                        $(this).find("i").addClass("icon-chevron-down");
                        $(this).find("i").removeClass("icon-chevron-right");
                    }

                    return false;
                });
            });', false);
    ?>
    <?php echo $this->Js->writeBuffer(); ?>
<?php endif;?>