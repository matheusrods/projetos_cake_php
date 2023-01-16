<?php
class SchedulerComponent extends Object {
	var $name = 'Scheduler';
	
	var $Outbox = null;
	
	public function __construct(){
		$this->Outbox = ClassRegistry::init('Mailer.Outbox');
	}
	
	public function schedule($content, $options, $model = null, $foreign_key = null){
		if (!empty($options['to'])) {
		    if (isset($options['cc'])){
		    	if (is_array($options['cc'])) {
					$options['cc'] = implode(';', $options['cc']);
				}
				if (is_array($options['to'])) {
					$options['cc'] = implode(';', $options['cc']);
				}
		    }
			$new_mail = $options;
			$new_mail['content'] = $content;
	        $new_mail['model'] = $model;
	        $new_mail['foreign_key'] = $foreign_key;
			$this->Outbox->create();
			return $this->Outbox->save($new_mail);
		}
	}
}
?>