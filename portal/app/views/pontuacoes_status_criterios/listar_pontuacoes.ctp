
    <div class='actionbar-right'>
	    <!--- Aqui fica botão incluir  -->
	    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('controller' => 'PontuacoesStatusCriterios','action' => 'incluir'), array('class' => 'btn btn-success', 'escape' => false ));?>
	</div>

<?php if($listagem == NULL || $listagem!= NULL):?>    
  
    <table class="table table-striped">
   
        <thead>
            <tr>
                <th class='input-large'> Cliente </th>
                <th class='input-large'>Seguradora</th>
                <th class='input-large'>Critérios</th>
                <th class='input-large'>Status</th>
                <th class='input-large'>Pontos</th>
                <th style="width:13px"></th>
                <th style="width:13px"></th>
            </tr>
        </thead>
        <tbody>
          
            <?php  if( $listagem!= NULL):?>    

               <?php  foreach($listagem as $clientes): ?>
                   
                    <td><?php echo $clientes['Cliente']['razao_social'];?></td>
                    <td><?php echo $clientes['Seguradora']['nome']; ?></td>  
                    <td><?php echo $clientes['Criterio']['descricao']; ?></td>
                    <td><?php echo $clientes['StatusCriterio']['descricao']; ?></td>
                    <td><?php echo $clientes['PontuacoesStatusCriterio']['pontos']; ?></td>
                    <td><?php echo $html->link('', array('controller' => 'pontuacoes_status_criterios', 'action' => 'editar',$clientes['PontuacoesStatusCriterio']['codigo'],$clientes['Criterio']['codigo'],$clientes['PontuacoesStatusCriterio']['codigo_seguradora'],$clientes['PontuacoesStatusCriterio']['codigo_cliente'] ), array('class' => 'icon-edit', 'title' => 'Alterar ')); ?>
                    </td>
                    <td><?php echo $html->link('', array('controller' => 'pontuacoes_status_criterios', 'action' => 'delete', $clientes['PontuacoesStatusCriterio']['codigo'], rand()), array('onclick' => 'return confirm("Confirma a exclusão do Status  Critério?")', 'title' => 'Excluir Critério', 'class' => 'icon-trash')) ?>
                    </td>
                    </tr></tr>
                <?php endforeach; ?>
            <?php endif;?> 
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