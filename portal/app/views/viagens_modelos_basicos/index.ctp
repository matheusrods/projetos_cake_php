
<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	</br>
    <div id='filtros'>
	    <?php echo $this->BForm->create('TVmbaViagemModeloBasico', array('url' => array('controller' => 'viagens_modelos_basicos','action' => 'index',)));?>
	    <div class="row-fluid inline">
    		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente','Cliente',False,'TVmbaViagemModeloBasico') ?>
    	</div>
		<div class="row-fluid inline">
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	        <?php echo $html->link('Limpar', 'index', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	        <?php echo $this->BForm->end() ?>
	    </div>	    
	</div>
</div>

<?php if(isset($cliente) &&  !empty($cliente)): ?>
	<div class='actionbar-right'><?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir',$cliente_guardian['TPjurPessoaJuridica']['pjur_pess_oras_codigo'], $cliente['Cliente']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Destino'));?></div>
	</br>
	<div class='row-fluid inline'>
		<div id="cliente" class='well'>
			<strong>Código:</strong><?= $cliente['Cliente']['codigo'];?>
			<strong>Cliente:</strong><?= $cliente['Cliente']['razao_social'];?>
		</div>
	</div>


	<table class="table table-striped ">
	    <thead>
	        <tr>
	            <th class='input-medium'>Placa</th>
	            <th class='input-medium'>CPF</th>
	            <th class='input-xlarge'>Origem</th>
	            <th class='input-xlarge'>Destino</th>
	            <th class='input-medium'>Status</th>
	            <th style="width:15px"></th>
	            <th style="width:15px"></th>
	        </tr>
	    </thead>
		    
		<?php foreach ($listagem as $lista): ?>
		<tr>
			<td><?php echo $this->Buonny->placa(Comum::formatarPlaca($lista['TVeicVeiculo']['veic_placa']),date('d/m/Y 00:00:00'), date('d/m/Y 23:59:59'),$cliente['Cliente']['codigo']);?>
			</td>
			<td><?php echo  $buonny->documento($lista['TPfisPessoaFisica']['pfis_cpf'])?></td>
			<td><?php echo  $lista['TRefeReferenciaOrigem']['refe_descricao']?></td>
			<td><?php echo  $lista['TRefeReferenciaDestino']['refe_descricao']?></td>
			<td><?php echo  $lista['TVmbaViagemModeloBasico']['vmba_ativo'] == 1 ? 'ATIVO' : ($lista['TVmbaViagemModeloBasico']['vmba_ativo'] == 0 ? 'INATIVO':'')?></td>
			<td><?= $html->link('', array('controller' => 'viagens_modelos_basicos','action' => 'editar',$lista['TVmbaViagemModeloBasico']['vmba_codigo'],$cliente['Cliente']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?></td>
			<td>
				<?= $html->link('', array('controller'=>'viagens_modelos_basicos','action'=>'excluir',$lista['TVmbaViagemModeloBasico']['vmba_codigo'],$lista['TVmbdViagemModeloBasicoDest']['vmbd_codigo']), array('class' => 'icon-trash', 'title' => 'Excluir')) ?></td>
		</tr>
		<?php endforeach; ?> 	
	</table>	
<?php endif?>


<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		setup_mascaras();
		jQuery("#Filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");

        });
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
	});	', false);
?>


    




