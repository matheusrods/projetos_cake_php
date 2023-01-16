<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?php echo $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
            <th><?php echo $this->Paginator->sort('Nome Fantasia', 'nome_fantasia') ?></th>
            <th colspan="3"><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $cliente) :?>
        <tr>
            <td class="input-mini"><?php echo $cliente['Cliente']['codigo'] ?></td>
            <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
            <td><?php echo $cliente['Cliente']['nome_fantasia'] ?></td>
            <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
            <td class="pagination-centered">
                <?php if ($destino == 'clientes'): ?>
                	<?= $html->link('', array('action' => 'editar', $cliente['Cliente']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
                <?php elseif($destino == 'clientes_funcionarios'): ?>
                	<?php echo $html->link('', array('controller' => 'funcionarios', 'action' => 'index', $cliente['Cliente']['codigo'], 'principal'), array('class' => 'icon-wrench', 'title' => 'Funcionários do Cliente')); ?>
                <?php elseif($destino == 'hospitais_emergencias'): ?>
                    <?php echo $html->link('', array('controller' => 'hospitais_emergencia', 'action' => 'unidades_hospital_emergencia', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Hospitais de Emergência por Unidades')); ?>
                <?php elseif($destino == 'clientes_setores'): ?>
                    <?php echo $html->link('', array('controller' => 'setores', 'action' => 'index', $cliente['Cliente']['codigo'], 'sistema'), array('class' => 'icon-wrench', 'title' => 'Setores do Cliente')); ?>
                <?php elseif($destino == 'implantacao_terceiros'): ?>
                    <?php echo $html->link('', array('controller' => 'setores', 'action' => 'index', $cliente['Cliente']['codigo'], 'implantacao_terceiros'), array('class' => 'icon-wrench', 'title' => 'Setores do Cliente')); ?>
                <?php elseif($destino == 'cargo_implantacao_terceiros'): ?>
                    <?php echo $html->link('', array('controller' => 'cargos', 'action' => 'index', $cliente['Cliente']['codigo'], 'cargo_implantacao_terceiros'), array('class' => 'icon-wrench', 'title' => 'Cargos do Cliente')); ?>
                <?php elseif($destino == 'clientes_cargos'): ?>
                    <?php echo $html->link('', array('controller' => 'cargos', 'action' => 'index', $cliente['Cliente']['codigo'], 'sistema'), array('class' => 'icon-wrench', 'title' => 'Cargos do Cliente')); ?>
                <?php elseif($destino == 'clientes_grupos_homogeneos'): ?>
                    <?php echo $html->link('', array('controller' => 'grupos_homogeneos', 'action' => 'index', $cliente['Cliente']['codigo'], 'sistema'), array('class' => 'icon-wrench', 'title' => 'Cargos do Cliente')); ?>

                <?php elseif ($destino == 'clientes_configuracoes'): ?>
                    <?php echo $this->BMenu->linkOnClick('',array('controller' => 'Clientes', 'action' => 'editar_configuracao', $cliente['Cliente']['codigo'],rand()), array('class' => 'icon-wrench', 'title' => 'Configuração do Cliente')); ?>
                <?php elseif ($destino == 'clientes_operacoes'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_operacoes', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Operações deste Cliente')); ?>
                <?php elseif ($destino == 'clientes_relacionamentos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_relacionamentos', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Relacionamentos deste Cliente')); ?>
                <?php elseif ($destino == 'visualiza_gerenciar_clientes_produtos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_produtos', 'action' => 'gerenciar', $cliente['Cliente']['codigo'],'consulta'), array('class' => 'icon-eye-open', 'title' => 'Visualizar Produtos Cliente')); ?> 
                <?php elseif ($destino == 'gerenciar_clientes_produtos'): ?>
                    <?php echo $html->link('', 'javascript: void(0)', array('escape' => false, 'class' => 'icon-check', 'title' =>'Configurações MOPP', 'onclick'=>'javascript: carregar_configuracao_mopp('.$cliente['Cliente']['codigo'].' ); return false;')) ?>
                    <?php echo $html->link('', array('controller' => 'clientes_produtos', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Produtos Cliente')); ?>  
                <?php elseif ($destino == 'clientes_demonstrativos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes', 'action' => 'demonstrativo_de_servico_buonnycredit', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Demonstrativo de Serviços BuonnyCredit')); ?>
                <?php elseif ($destino == 'clientes_usuarios'): ?>
                    <?php echo $html->link('', array('controller' => 'usuarios', 'action' => 'por_cliente', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Usuários do Cliente')); ?>
                <?php elseif ($destino == 'clientes_alertas_usuarios'): ?>
                    <?php echo $html->link('', array('controller' => 'usuarios', 'action' => 'alertas_por_cliente', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Alertas de Usuários do Cliente')); ?>
                <?php elseif ($destino == 'gerenciar_clientes_produtos_status'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_produtos', 'action' => 'gerenciar', $cliente['Cliente']['codigo'], $financeiro = true), array('class' => 'icon-wrench', 'title' => 'Gerenciar Status dos Produtos de Clientes')); ?>                
                <?php elseif ($destino == 'clientes_produtos_contratos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_produtos_contratos', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Contratos do Cliente')); ?>                
                <?php elseif ($destino == 'gerenciar_clientes_procuracoes'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_procuracoes', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Procurações do Cliente')); ?>                
                <?php elseif ($destino == 'gerenciar_clientes_produtos_descontos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_produtos_descontos', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Concessão de Desconto')) ?>
                <?php else: ?>
                    <?php echo $html->link('', array('controller' => 'clientes_representantes', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar representantes')); ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>

<?php echo $javascript->codeblock('
	jQuery(document).ready(function() {});
	function carregaFuncionarios(id, element) {
	
		if($("#cliente_" + id).is( ":visible" ) === false) {
			var onclick = $(element).attr("onclick");
			$("#cliente_" + id).fadeIn();

			$.ajax({
		        type: "POST",
		        url: "/portal/funcionarios/listagem/" + id,
		        dataType: "html",
		        beforeSend: function() {
		        	$(element).removeAttr("onclick");
		        	$("#carregando_" + id).fadeIn();
		        },
		        success: function(html) {
		        	$("#resultado_" + id).html(html);
		        },
		        complete: function() {
		        	$(element).bind("click", function() { carregaFuncionarios(id); });
		        	$("#carregando_" + id).fadeOut();
		        }
		    });			
		} else {
			$("#cliente_" + id).fadeOut();		
		}	
	}
'); ?>



