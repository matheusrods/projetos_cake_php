<?php echo $this->BForm->create('FichaAssistencial', array('url' => array('controller' => 'fichas_assistenciais',
																		  'action' => 'incluir', 
																		  $dados['PedidoExame']['codigo'])
															)
								); 
?>
<?php echo $this->element('fichas_assistenciais/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>