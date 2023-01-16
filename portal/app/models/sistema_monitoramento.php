<?php

class SistemaMonitoramento extends AppModel {

    var $name = 'SistemaMonitoramento';
    var $useTable = false;

		const MONITORA = 1;
		const GUARDIAN = 2;
		
		public static function lista() {
			return array(
				self::GUARDIAN => 'Guardian',
				self::MONITORA => 'Monitora'
			);
		}

		public static function descricao($id) {
			if (empty($id))
				return null;
			$itens = self::lista();
			return $itens[$id];
		}

}
