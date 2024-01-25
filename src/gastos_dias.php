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
    /**
     * El dia que contiene montos asociados.
     *
     * @var DateTime
     */
    private DateTime $_dia;

    /**
     * Lista con todos los gastos que hay en un dia.
     *
     * @var array
     */
    private array $_montos = [];

    /**
     * Constructor de la clase
     *
     * @param DateTime $dia   El dia que tiene un gasto asociado.
     * @param float    $monto Uno de los montos que ocurrio en ese
     *                        día.
     */
    public function __construct(DateTime $dia, float $monto = 0)
    {
        $this->_dia = $dia;
        if ($monto != 0) {
            $this->_montos[] = $monto;
        }
    }

    /**
     * Asocia un monto al listado de montos del día.
     *
     * @param float $monto El valor del gasto que ocurrio ese día.
     *
     * @return void
     */
    public function agregarMonto(float $monto)
    {
        if ($monto != 0) {
            $this->_montos[] = $monto;
        }
    }

    /**
     * Devuelve el dia como objeto de php.
     *
     * @return DateTime Objeto PHP con la información del día.
     */
    public function getObjetoDia()
    {
        return $this->_dia;
    }

    /**
     * Devuelve la suma de todos los montos que ocurrieron en el día.
     *
     * @return float Sumatoria de los montos.
     */
    public function sumarMontos()
    {
        return array_reduce(
            $this->_montos, function ($carry, $item) {
                $carry += $item;
                return $carry;
            }, 0
        );
    }

    /**
     * Devuelve el promedio de todos los montos asociados al día.
     *
     * @return float Promedio de los montos para el día.
     */
    public function montosPromedios()
    {
        return $this->sumarMontos() / count($this->_montos);
    }

    /**
     * Devuelve la fecha del día como texto en formato YYYYMMDD.
     *
     * @return string La fecha completa en texto.
     */
    public function getFecha()
    {
        return $this->_dia->format("Ymd");
    }

    /**
     * Devuelve el mes y año de la fecha en formato YYYYMM
     *
     * @return string El mes y el año como texto.
     */
    public function getAnnoMes()
    {
        return $this->_dia->format("Ym");
    }


    /**
     * Devuelve el año de la fecha en formato YYYY.
     *
     * @return string El año como texto.
     */
    public function getAnno()
    {
        return $this->_dia->format("Y");
    }

    /**
     * Devuelve el día del mes de la fecha en formato DD.
     *
     * @return string El dia del mes como texto.
     */
    public function getDiaMes()
    {
        return $this->_dia->format("d");
    }

    /**
     * Devuelve el día del año de la fecha en formato DD.
     *
     * @return string El dia del año como texto.
     */
    public function getDiaAnno()
    {
        return $this->_dia->format("z");
    }

}
