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

require_once "gastos_dias.php";
require_once "reporteador.php";

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
    generarGraficosAnualesPorMes($fechas);
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
        graficarMes($fechas_mes, $mes, true);
    }
}

/**
 * Obtiene un arreglo con los años presentes en el listado de fechas.
 *
 * @param array $fechas Listado de las fechas con gastos asociados.
 *
 * @return array Años a los que pertenecen las fechas.
 */
function obtenerAnnos(array $fechas)
{
    $annos_aux = array_map(
        function ($gasto_dia) {
            return $gasto_dia->getAnno();
        }, $fechas
    );
    return array_unique($annos_aux);
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
    $annos = obtenerAnnos($fechas);
    foreach ($annos as $anno) {
        $fechas_anno = array_filter(
            $fechas, function ($gasto_dia) use (&$anno) {
                return $gasto_dia->getAnno() == $anno;
            }
        );
        graficarAnno($fechas_anno, $anno, true);
    }
}

/**
 * Genera graficos de los gastos en cada mes del año del listado de fechas.
 *
 * @param array $fechas Listado de las fechas con los gastos asociados.
 *
 * @return void
 */
function generarGraficosAnualesPorMes(array $fechas)
{
    $annos = obtenerAnnos($fechas);
    foreach ($annos as $anno) {
        $data = [];
        $meses = range(1, 12);
        foreach ($meses as $mes) {
            if ($mes < 10) {
                $format = "%s0%d";
            } else {
                $format = "%s%d";
            }
            $anno_mes = sprintf($format, $anno, $mes);
            $monto = array_reduce(
                $fechas, function ($carry, $item) use (&$anno_mes) {
                    if ($item->getAnnoMes() == $anno_mes) {
                        $carry += $item->sumarMontos();
                    }
                    return $carry;
                }, 0
            );
            $data[$mes] = abs($monto);
        }
        graficarAnnoMeses($data, $anno);
    }
}

/**
 * Genera una grafica de lineas usando la data para un mes dado.
 *
 * @param array  $info          data de entrada para hacer los reportes
 * @param string $mes           Texto con el año y mes
 *                              a procesar.
 * @param bool   $usar_promedio Si se van a usar montos promediados en vez
 *                              de la suma.
 *
 * @return null
 */
function graficarMes(array $info, string $mes, bool $usar_promedio = false)
{
    $month = (int) substr($mes, -2);
    $year = (int) substr($mes, 0, 4);
    $data_x = [];
    $data_y = [];
    foreach ($info as $llave => $valor) {
        $data_x[] = (int) $valor->getDiaMes();
        $data_y[] = abs(
            $usar_promedio ? $valor->promediarMontos() :
            $valor->sumarMontos()
        );
    }
    /*print_r($data_x);
    print_r($data_y);*/
    $max_days = daysInMonth($month, $year);
    $min_days = 1;
    $title = "Consumos del mes " . $month . " del año " . $year;
    $title_x = "Dia del mes";
    $title_y = "Monto";
    $filename = "ejemplo" . $mes;
    hacerGrafico(
        $data_x, $data_y, $min_days, $max_days, $title, $title_x,
        $title_y, $filename
    );

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
 * @param array  $info          data de entrada para hacer los reportes.
 * @param string $anno          Texto con el año a
 *                              procesar.
 * @param bool   $usar_promedio Si se van a usar montos promediados en vez
 *                              de la suma.
 *
 * @return null
 */
function graficarAnno(array $info, string $anno, bool $usar_promedio = false)
{
    $year = (int) $anno;
    $data_x = [];
    $data_y = [];
    foreach ($info as $llave => $valor) {
        $data_x[] = ((int) $valor->getDiaAnno()) + 1;
        $data_y[] = abs(
            $usar_promedio ? $valor->promediarMontos() :
            $valor->sumarMontos()
        );
    }
    /*print_r($data_x);
    print_r($data_y);*/
    $max_days = 366;
    $min_days = 1;
    $title = "Consumos del año " . $year;
    $title_x = "Dia del año ";
    $title_y = "Monto";
    $filename = "ejemplo" . $anno;
    hacerGrafico(
        $data_x, $data_y, $min_days, $max_days, $title, $title_x,
        $title_y, $filename
    );
}

/**
 * Genera una grafica de lineas con los gastos de cada mes del año.
 *
 * @param array  $info Data de gastos mensuales del
 *                     año.
 * @param string $anno Texto con el año a procesar.
 *
 * @return void
 */
function graficarAnnoMeses(array $info, string $anno)
{
    $year = (int) $anno;
    $data_x = [];
    $data_y = [];
    for ($i = 1; $i <= count($info); $i++) {
        $data_x[] = $i;
        $data_y[] = $info[$i];
    }
    print_r($data_x);
    print_r($data_y);
    $max_days = 12;
    $min_days = 1;
    $title = "Consumos mensuales del año " . $year;
    $title_x = "Mes ";
    $title_y = "Monto";
    $filename = "ejemplomeses" . $anno;
    hacerGrafico(
        $data_x, $data_y, $min_days, $max_days, $title, $title_x,
        $title_y, $filename
    );
}

