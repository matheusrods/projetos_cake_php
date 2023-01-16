<?php echo $this->element('grupos_economicos_clientes/matriz_unidade'); ?>

<?php if(!isset($dados_arquivo)): ?>

	<?php echo $this->BForm->create('Importar', array('type' => 'file', 'enctype' => 'multipart/form-data','url' => array('controller' => 'importar', 'action' => 'importar_usuario_unidade', $codigo_cliente), 'onSubmit' => '$("#carregando").show();')); ?>
	<div class="well">
		<div class='row-fluid inline'>
			<?php echo $html->link('Modelo para Importação', '/files/modelos/arquivo_importacao_usuario_unidade.xls', array('class' => 'btn btn-success', 'escape' => false, 'target' => '_blank',  'title' => 'Visualizar Modelo para Importação', 'style' => 'float:right; margin-right: 50px; color:#FFF'));?>
		</div>
		<div class='row-fluid inline'>

			<?php echo $this->BForm->hidden('codigo_cliente',array('value'=>$codigo_cliente)) ?>

			<?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => 'Anexe aqui seu arquivo para Importação','class' => 'input-xxlarge'));?> 
			<div id="carregando" style="display: none; text-align: center; float: left; margin-top: 30px; margin-left: 20px;">
				<img src="/portal/img/ajax-loader.gif" border="0" />
			</div>
		</div>
		<div class='row-fluid inline'>
			<h6>(*) Somente arquivos em formato .csv - separados por ponto e vírgula( ; )</h6>
		</div>
	</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary'));?>
	<?php echo $html->link('Voltar', array('controller' => 'usuarios', 'action' => 'por_cliente', $codigo_cliente), array('class' => 'btn')); ?>
</div>

<?php echo $this->BForm->end(); ?>	

<?php elseif(isset($dados_arquivo)):?>
	<div id="resultado">
		 <div id="retorno">
			<table class="table table-striped">
		    <tbody>
		        <tr>
		        	<td style="font-weight: bold;" class="input-xxlarge">Arquivo Processado</td>
		        	<td class="input-medium"><?=$dados_arquivo['nome_arquivo']?></td>
		        	<td>&nbsp;</td>
		       	</tr>
	            <tr>
	            	<td style="font-weight: bold;" class="input-xxlarge">Quantidade de registros  com sucesso</td>
	            	<td class="input-medium"><?=$dados_arquivo['sucesso']?></td>
	            	<td style="font-weight: bold;" class="input-medium"><?php echo (!empty($dados_arquivo['nome_arquivo_sucesso']))? $html->link('Abrir Arquivo', array('controller'=>'importar','action' => 'abre_arquivo',$dados_arquivo['nome_arquivo_sucesso'], 'importacao_dados_usuario_unidade'), array('class' => 'btn btn-success', 'style' => 'color:#FFF')) : '' ;?></td>
	            </tr>
	            <tr>
	            	<td style="font-weight: bold;" class="input-xxlarge">Quantidade de registros com erro</td>
	            	<td class="input-medium"><?=$dados_arquivo['erros']?></td>
	            	<td style="font-weight: bold;" class="input-medium"><?php echo (!empty($dados_arquivo['nome_arquivo_erro']))? $html->link('Abrir Arquivo', array('controller'=>'importar','action' => 'abre_arquivo',$dados_arquivo['nome_arquivo_erro'], 'importacao_dados_usuario_unidade'), array('class' => 'btn btn-danger', 'style' => 'color:#FFF')) : '' ;?></td>
	            </tr>
		    </tbody>
		    <tfoot>
	            <tr><td class="input-xxlarge" style="font-weight: bold;">Total</td>
	            	<td><?=$dados_arquivo['total']?></td>
	            	<td>&nbsp;</td>
	            </tr>
		    </tfoot>
		</table>
	</div>

<div class='form-actions'>
	<?php echo $html->link('Nova Importação', array('controller' => 'importar', 'action' => 'importar_usuario_unidade', $codigo_cliente), array('class' => 'btn btn-primary')); ?>
	<?php echo $html->link('Voltar', array('controller' => 'usuarios', 'action' => 'por_cliente', $codigo_cliente), array('class' => 'btn')); ?>
</div>

	<div id="retorno_erro" class="help-block error-message" style="display:none;">Erro ao processar o arquivo! Verifique o Arquivo!</div>
<?php endif; ?>
</div>

<?php echo $this->Javascript->codeBlock('

	jQuery(document).ready(function() {
		setup_mascaras(); 
		setup_datepicker(); 
		setup_time(); 
	});
'); ?>