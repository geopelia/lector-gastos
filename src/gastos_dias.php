<?php
/**
 * Clases utilitarias. 
 *
 * PHP version 8
 * 
 * @category Default
 * @package  Default
 * @author   Atilio Quintero <atilioquintero@gmail.com>
 * @license  GPL v3
 * @link     http://localhost/
 */

/**
 * Clase que guarda el registro de un dia y los montos de ese dia.
 *
 * PHP version 8
 * 
 * @category Default
 * @package  Default
 * @author   Atilio Quintero <atilioquintero@gmail.com>
 * @license  GPL v3
 * @link     http://localhost/
 */
class GastosDias
{
	private DateTime $_dia;
	private array $_montos = [];

	public function __construct(DateTime $dia, float $monto = 0) {
	$this->_dia = $dia;
	if ($monto != 0) {
		$this->_montos[] = $monto;
	}
	}

	public function agregarMonto(float $monto) {
		if ($monto != 0) {
			$this->_montos[] = $monto;
		}		
	}

	public function getObjetoDia() {
		return $this->_dia;
	}

	public function sumarMontos() {
		return array_reduce($this->_montos, function($carry, $item) {
			$carry += $item;
			return $carry;
		}, 0);
	}

	public function getFecha() {
		return $this->_dia->format("Ymd");
	}

	public function getAnnoMes() {
		return $this->_dia->format("Ym");
	}


	public function getAnno() {
		return $this->_dia->format("Y");
	}

	public function getDiaMes() {
		return $this->_dia->format("d");
	}
	
	public function getDiaAnno() {
		return $this->_dia->format("z");
	}
	
}
