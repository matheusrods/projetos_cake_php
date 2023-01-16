<table class="table table-striped">
    <thead>		
        <tr> 
            <th>Dia</th>
            <th>Entrada</th>
            <th>Saída</th>
        </tr>            	
    </thead>
    <tbody>
        <?php foreach ($dias_semana as $dia => $descricao ):?>
			<?php $entrada = isset($dados_expediente[$dia]['UsuarioExpediente']['entrada']) ? trim(substr($dados_expediente[$dia]['UsuarioExpediente']['entrada'], 0, 5)) : NULL;?>
			<?php $saida   = isset($dados_expediente[$dia]['UsuarioExpediente']['saida']) 	? trim(substr($dados_expediente[$dia]['UsuarioExpediente']['saida'], 0, 5))   : NULL;?>
            <?php 
            if($entrada == '00:00' && $saida == '00:00' ):
                $entrada = NULL;
                $saida   = NULL;
            endif;?>
        <tr>		
            <td><?= $descricao ?></td>
            <td><?php echo $this->BForm->input("UsuarioExpediente.{$dia}.entrada", array('type'=>'text','label' => FALSE, 'class' => 'hora input-mini', 'value' => $entrada ))?></td>
            <td><?php echo $this->BForm->input("UsuarioExpediente.{$dia}.saida", array('type'=>'text', 'label' => FALSE, 'class' => 'hora input-mini', 'value' => $saida ))?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_time();        
    });', false);?>