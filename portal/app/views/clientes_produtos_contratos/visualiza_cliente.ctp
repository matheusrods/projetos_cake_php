<?php
$indice_produto = 0;
$indice_servico = 0;
$indice_detalhe = 0;
$indice_fields = 0;
?>
<style type="text/css">
   .nome_produto{ 
   	    margin-left:30px;
   	    width:380px;
    }
    .lista_arquivos{ float: right!important;}
</style>

<?php if (isset($lista_arquivos)&& isset($nome_produto)): ?>
	<div class="well">
		<strong>Contratos do cliente:<br/><br/></strong>
		<div class="row">
			<?php foreach ($lista_arquivos as $key => $value):?>
				<div align="left"  class='nome_produto'>	
					<?php echo $nome_produto[$key] ?> 	
					<div class="lista_arquivos" align="right" >
						<?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'icon-file')) . ' ' . basename($lista_arquivos[$key]), '../../arquivos/contratos_clientes/'. $lista_arquivos[$key], array('escape' => false, 'target' => '_blank'));  ?> 
					</div>
				</div>						
			<?php endforeach; ?>
		</div>		
	</div>			
<?php endif; ?>

<?php if (!empty($documentos_cliente)): ?>
<div class="well">
    <strong>Documentos do cliente:</strong>
    <div class="container">
    <div class="row">
        <?php foreach ($documentos_cliente as $arquivo): ?>
        <div class="span6 nowrap-text"><?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'icon-file')) . ' ' . basename($arquivo), 'http://' .URL_CRM . '/' . $arquivo, array('escape' => false, 'target' => '_blank')); ?></div>
        <?php endforeach; ?>
    </div>
    </div>
</div>
<?php endif; ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class='input-large'>Produto</th>
            <th class='input-large'></th>
            <th class='input-large numeric'>Valor (R$)</th>
            <th class='input-large'>Data Vencimento</th>
            <th class='input-large'>N° Contrato</th>
            <th class='input-large'></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produtos as $produto): ?>
            <tr class="produto" produto-id="<?php echo $produto['Produto']['codigo'] ?>">    
                <td><i class="icon-chevron-down"></i><strong><?php echo $produto['Produto']['descricao']; ?></strong></td>
                <td>
                    <?php
                        $pattern = array(
                            '/(.*inativ.*)/i',
                            '/(.*pend.+ncia.*)/i',
                            '/(.*desatualizad.*)/i',
                        );
                        $replacement = array(
                            'INATIVO',
                            'PENDÊNCIA FIN.',
                            'DESATUALIZADO',
                        );
                        $motivo_bloqueio = preg_replace($pattern, $replacement, $produto['MotivoBloqueio']['descricao']);
                        switch ($motivo_bloqueio) {
                            case 'OK':
                                 $class_motivo_bloqueio = 'label label-success';
                                break;
                            case 'DESATUALIZADO':
                                $class_motivo_bloqueio = 'label label-warning';
                                break;
                            case 'PENDÊNCIA FIN.':
                                    $class_motivo_bloqueio = 'label label-important';
                                    break;
                            case 'INATIVO':
                            default:
                                $class_motivo_bloqueio = 'label';
                                break;
                        }
                    ?>
                    <span class="pull-right <?php echo $class_motivo_bloqueio; ?>" title="<?php echo $produto['MotivoBloqueio']['descricao']; ?>"><?php echo $motivo_bloqueio; ?>
                    </span> 
                </td>
                <td></td>
                <?php foreach ($clientes_produtos as $clientes_produto): ?>
                    <?php if($clientes_produto['Produto']['codigo'] == $produto['Produto']['codigo']): ?>
                        <td><strong><?php echo $clientes_produto['ClienteProdutoContrato']['data_vigencia'] ? preg_replace('/\s+.*$/', '', $clientes_produto['ClienteProdutoContrato']['data_vigencia']) : '&nbsp;'; ?></strong></td>
                        <td><strong><?php echo $clientes_produto['ClienteProdutoContrato']['numero'] ? $clientes_produto['ClienteProdutoContrato']['numero'] : '&nbsp;' ?></strong></td>
                        <td></td>   
                    <?php endif;?>
                <?php  endforeach;?>
            </tr>
            <?php foreach ($produto['ClienteProdutoServico2'] as $key => $servico): ?>
                <tr class="servico" produto-id="<?php echo $produto['Produto']['codigo'] ?>">
                    <td style="padding-left:27px"><?php echo $servico['Servico']['descricao'] ?></td>
                    <td></td>
                    <td class="numeric"><?php echo $this->Buonny->moeda($servico['valor'], array('nozero' => true)) ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        $('tr.produto').click(function(){
            produto_id = $(this).attr('produto-id');
            $.each($('tr.servico[produto-id=\"'+produto_id+'\"]'), function(i,v){
                $(v).toggle();
            });

            if($(this).find('i.icon-chevron-down').length > 0){
                $(this).find('i').addClass('icon-chevron-right');
                $(this).find('i').removeClass('icon-chevron-down');
            }else{
                $(this).find('i').addClass('icon-chevron-down');
                $(this).find('i').removeClass('icon-chevron-right');
            }
        });
    });
"); ?>
