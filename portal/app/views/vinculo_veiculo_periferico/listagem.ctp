<?php if(isset($veiculo_sem_vinculo) && $veiculo_sem_vinculo):?>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<?php else:?>
    <div class="well">
        <b>Todos tipos de veículos já possuem vínculo.</b>
    </div>
<?php endif?>
<?php if(isset($dados) && !empty($dados)):?>
    <table class="table">
        <thead>       
            <tr>
                <th class="input-xxlarge">Tipo do veículo</th>
                <th>Periférico(s)</th>
                <td>&nbsp;</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados as $key => $dado): ?>
            <tr>
                <td><b><?= key($dado) ?></b></td>
                <td>
                    <i class="icon-chevron-right per<?=$key?>" onclick="abrir_perifericos(<?=$key?>)"></i>
                </td>
                <td class="numeric">
                    <?= $this->Html->link('', array('action' => 'editar', $key), array('escape' => false, 'class' => 'icon-edit', 'title' => 'Editar')); ?>    
                    <?= $html->link('', array('controller' => $this->name,'action' => 'excluir', $key), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Tem certeza que deseja excluir o vínculo do tipo veículo com os periféricos?'); ?>
                </td>                  
                <?php foreach ($dado as $perifericos):?>
    				<?php foreach ($perifericos as $periferico):?>
    					<tr class="perifericos<?=$key?>" style="display:none;">
                            <td>&nbsp;</td>
    						<td><?= $periferico ?></td>
                            <td>&nbsp;</td>                        
    					</tr>	 
    				<?php endforeach;?>
                <?php endforeach;?>
            </tr>
            <?php endforeach; ?>        
        </tbody>
    </table>
    <?= $this->Javascript->codeBlock('	
        function abrir_perifericos(periferico){      
    		$(".perifericos"+periferico).toggle();
            if($(".perifericos"+periferico).css("display") == "none"){
                $(".per"+periferico).removeClass("icon-chevron-down");
                $(".per"+periferico).addClass("icon-chevron-right");
            }else{
                $(".per"+periferico).removeClass("icon-chevron-right");
                $(".per"+periferico).addClass("icon-chevron-down");
            }       
    	}
    ');?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    