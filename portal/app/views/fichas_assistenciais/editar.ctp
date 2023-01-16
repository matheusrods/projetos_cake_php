<?php echo $this->BForm->create('FichaAssistencial', array('url' => array('controller' => 'fichas_assistenciais', 
																		  'action' => 'editar', $codigo), 
															'type' => 'post')
								); 
?>
<?php echo $this->element('fichas_assistenciais/fields_editar'); ?>
<?php echo $this->BForm->end(); ?>