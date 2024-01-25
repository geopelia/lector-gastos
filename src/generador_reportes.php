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
require_once "gastos_dias.php";

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
    generarGraficosMensuales($fechas);
    generarGraficosAnuales($fechas);
    echo "\n";
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
    $fechas = [];
    foreach ($info as $registro) {
        $fecha_pivot = $registro["fecha"]->format("Ymd");
        if (isset($fechas[$fecha_pivot])) {
            $fechas[$fecha_pivot]->agregarMonto($registro["monto"]);
        } else {
            $gastos_dias = new GastosDias($registro["fecha"], $registro["monto"]);
            $fechas[$fecha_pivot] = $gastos_dias;
        }
    }
    return array_values($fechas);
}

/**
 * Genera graficos de gastos mensuales para cada mes presente en las fechas.
 *
 * @param array $fechas Listado de las fechas con gastos asociados.
 *
 * @return void
 */
function generarGraficosMensuales(array $fechas)
{
    $meses_aux = array_map(
        function ($gasto_dia) {
            return $gasto_dia->getAnnoMes();
        }, $fechas
    );
    $meses = array_unique($meses_aux);
    foreach ($meses as $mes) {
        $fechas_mes = array_filter(
            $fechas, function ($gasto_dia) use (&$mes) {
                return $gasto_dia->getAnnoMes() == $mes;
            }
        );
          graficarMes($fechas_mes, $mes);
    }
}

/**
 * Genera graficos de gastos anuales para cada año presente en el listado de fechas.
 *
 * @param array $fechas Listado de las fechas con los gastos asociados.
 *
 * @return void
 */
function generarGraficosAnuales(array $fechas)
{
    $annos_aux = array_map(
        function ($gasto_dia) {
            return $gasto_dia->getAnno();
        }, $fechas
    );
    $annos = array_unique($annos_aux);
    foreach ($annos as $anno) {
        $fechas_anno = array_filter(
            $fechas, function ($gasto_dia) use (&$anno) {
                return $gasto_dia->getAnno() == $anno;
            }
        );
          graficarAnno($fechas_anno, $anno);
    }
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
    foreach ($info as $llave => $valor) {
            $data_x[] = (int) $valor->getDiaMes();
            $data_y[] = abs($valor->sumarMontos());
    }
    /*print_r($data_x);
    print_r($data_y);*/
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
    return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : (
    $year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}

/**
 * Genera una grafica de lineas usando la data para un año dado.
 *
 * @param array  $info data de entrada para hacer los reportes.
 * @param string $anno Texto con el año a procesar.
 *
 * @return null
 */
function graficarAnno(array $info, string $anno)
{
    $year = (int) $anno;
    $data_x = [];
    $data_y = [];
    foreach ($info as $llave => $valor) {
            $data_x[] = ((int) $valor->getDiaAnno()) + 1;
            $data_y[] = abs($valor->sumarMontos());
    }
    /*print_r($data_x);
    print_r($data_y);*/
    $graph = new Graph(600, 400);
    $max_days = 366;
    $graph->setScale("intlin", 0, 0, 1, $max_days);
    $graph->setMargin(100, 10, 40, 40);
    $graph->title->Set("Consumos del año " . $year);
    $graph->xaxis->title->set("Dia del año ");
    $graph->yaxis->title->set("Monto");
    $line_plot=new LinePlot($data_y, $data_x);
    $graph->Add($line_plot);
    $graph->Stroke("../output/ejemplo" . $anno . "-" . (string) time() . ".png");
}

