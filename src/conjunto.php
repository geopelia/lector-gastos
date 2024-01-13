<?php
/**
 * Clases utilitarias para guardar valores unicos en arreglosi y filtrar.
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
 * Clase que permite insertar elementos en lista solo si ya no existen
 *
 * @category Default
 * @package  Default
 * @author   Atilio Quintero <atilioquintero@gmail.com>
 * @license  GPL v3
 * @link     http://localhost/
 */
class Conjunto
{
    private array $_lista = [];

    /**
     * Agrega un elemento a la lista sino existe.
     *
     * @param string $valor Valor a agregar
     *
     * @return null
     */
    public function agregar(string $valor)
    {
        if ($valor == "") {
            return;
        }
        if (!in_array($valor, $this->lista)) {
            $this->_lista[] = $valor;
        }
    }

    /**
     * Devuelve la lista con los valores unicos.
     *
     * @return array lista sin valores duplicados.
     */
    public function getLista()
    {
        return $this->_lista;
    }

}

/**
 * Enumerado con las distintas opciones para extraer fechas de un listado de fechas.
 *
 * @category Default
 * @package  Default
 * @author   Atilio Quintero <atilioquintero@gmail.com>
 * @license  GPL v3
 * @link     http://localhost/
 */
enum OpcFecha
{
    case Y;
    case YM;
    case YMD;
}    

