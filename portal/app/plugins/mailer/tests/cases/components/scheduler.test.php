<?php
class SchedulerCase extends CakeTestCase {
	
	public function startCase() {
		App::import('Component', 'Mailer.Scheduler');
		App::import('Model', 'Mailer.Outbox');
		Mock::generate('Outbox');
	}
	
	public function startTest($method){
		$this->Scheduler = new SchedulerComponent();
	}
	
	public function testSchedule(){
		$this->Scheduler->Outbox = new MockOutbox();
		
		$this->Scheduler->Outbox->setReturnValue('save', true);
		$this->Scheduler->Outbox->expectOnce('save');
		
		$this->assertTrue($this->Scheduler->schedule('', array()));
	}
		
}
