<?php
/**
 * Genera reportes de gastos usando liberia JPGraph.
 *
 * PHP version 8
 * 
 * @category Default
 * @package  Default
 * @author   Atilio Quintero <atilioquintero@gmail.com>
 * @license  GPL v3
 * @link     http://localhost/
 */

require_once '../lib/jpgraph/src/jpgraph.php';
require_once '../lib/jpgraph/src/jpgraph_line.php';
require_once "conjunto.php";

/**
 * Función base donde se mandan a crear los diferentes reportes.
 *
 * @param array $info Data de entrada para hacer reportes.
 *
 * @return null
 */
function crearGraficos(array $info)
{
    $fechas = obtenerArregloDias($info);
    $sumados = sumarMontosDia($fechas, $info);

    $meses = filtrarFechas($fechas, OpcFecha::YM);
    foreach ($meses as $mes) {
        graficarMes($sumados, $mes);
    }
    echo "\n";
}
/**
 * Imprime info de depuracion.
 *
 * @param array $info    data de entrada para hacer los reportes.
 * @param array $fechas  Listado de todos los dias procesados.
 * @param array $sumados Data de entrada con montos sumados por dias.
 *
 * @return null
 */
function depurar(array $info, array $fechas, array $sumados) 
{
    print("Hola mundo, el arreglo tiene: " . count($info) . "registros\n" );
    echo "hay " . count($fechas) . " dias distintos\n"; 
    echo "años son :";
    print_r(filtrarFechas($fechas, OpcFecha::Y));
    echo "\nmeses son: ";
    print_r(filtrarFechas($fechas, OpcFecha::YM));
    echo "\nvalores sumados son: " . count($sumados) . "\n";
    print_r($sumados);
}

/**
 * Suma los montos de los gastos del mismo dia.
 *
 * @param array $fechas Listado de todos los dias procesados.
 * @param array $info   data de entrada para hacer los reportes.
 *
 * @return array Arreglo con informacion detallada de dias, monto del dia 
 * y cantidad de registros.
 */
function sumarMontosDia(array $fechas, array $info) 
{
    $sumas = [];
    foreach ($fechas as $fecha) {
        $sumatoria = 0;
        $registros = 0;
        foreach ($info as $registro) {
            $fecha_pivote = $registro["fecha"]->format("Ymd");
            if ($fecha_pivote == $fecha) {
                $sumatoria += $registro["monto"];
                $registros++;
            }

        }
        $sumas[] = [
        "fecha" => $fecha,
        "monto" => $sumatoria,
        "cantidad" => $registros,
        ];
    }
    return $sumas;
}

/**
 * Filtra y devuelve las fechas especificadas sin repetir registros.
 *
 * @param array    $fechas Listado de todos los dias procesados.
 * @param OpcFecha $opcion Opcion de un enumerado para filtrar las fechas.
 *
 * @return array Los elementos de la fecha filtrados y sin duplicados.
 */
function filtrarFechas(array $fechas, OpcFecha $opcion) 
{
    if ($opcion === OpcFecha::YMD) {
        return $fechas;
    }
    $listado = new Conjunto();
    foreach ($fechas as $fecha) {
        switch ($opcion) {
        case OpcFecha::Y:
            $pivote = substr($fecha, 0, 4);
            break;
        case OpcFecha::YM:
            $pivote = substr($fecha, 0, 6);
            break;
        }

        $listado->agregar($pivote);
    }
    return $listado->getLista();
}

/**
 * Da un listado de todas las fechas unicas que aparecen en la data de entrada.
 *
 * @param array $info data de entrada para hacer los reportes.
 *
 * @return array Lista con todas las fechas unicas que aparecen en el 
 * listado a procesar.
 */
function obtenerArregloDias(array $info) 
{
    $fechas = new Conjunto();
    foreach ($info as $registro) {
        $fecha_pivot = $registro["fecha"]->format("Ymd");
        $fechas->agregar($fecha_pivot);
    }
    return $fechas->getLista();
}

/**
 * Genera una grafica de lineas usando la data para un mes dado.
 *
 * @param array  $info data de entrada para hacer los reportes
 * @param string $mes  Texto con el año y mes a
 *                     procesar.
 *
 * @return null
 */
function graficarMes(array $info, string $mes)
{
    $month = (int) substr($mes, -2);
    $year = (int) substr($mes, 0, 4);
    $data_x = [];
    $data_y = [];
    foreach ($info as $record) {
        if (substr($record["fecha"], 0, 6) == $mes) {            
            $data_x[] = (int) substr($record["fecha"], -2);
            $data_y[] = abs($record["monto"]);
        }
    }
    print_r($data_x);
    print_r($data_y);
    $graph = new Graph(600, 400);
    $max_days = daysInMonth($month, $year);
    $graph->setScale("intlin", 0, 0, 1, $max_days);
    $graph->setMargin(100, 10, 40, 40);
    $graph->title->Set("Consumos del mes " . $month . " del año " . $year);
    $graph->xaxis->title->set("Dia del mes");
    $graph->yaxis->title->set("Monto");
    $line_plot=new LinePlot($data_y, $data_x);
    $graph->Add($line_plot);
    $graph->Stroke("../output/ejemplo" . $mes . "-" . (string) time() . ".png");

}

/**
 * Devuelve cuantos dias tiene el mes.
 *
 * @param $month El mes a procesar
 * @param $year  el año a
 *               procesar
 *
 * @return int numero de dias del mes
 */ 
function daysInMonth($month, $year)
{
    return $month == 2 i? ($year % 4 ? 28 : ($year % 100 ? 29 : (
    $year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}
