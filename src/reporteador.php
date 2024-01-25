<?php
/**
 * Clase que sirve de puente para generar reportes con la libreria JPGraph.
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

/**
 * Función que genera e imprime el grafico a un archivo.
 *
 * @param array $data_x Los valores que van en el eje X.
 * @param array $data_y Los valores que van en el eje Y.
 * @param integer $min_x Valor minimo de la escala en X.
 * @param integer $max_x Valor maximo de la escala en X.
 * @param string $title Titulo del grafico.
 * @param string $title_x Texto del eje de las X.
 * @param string $title_y Texto del eje de las Y.
 * @param string $filename Nombre de la imagen generada.
 *
 * @return void
 */
function hacerGrafico(array $data_x, array $data_y, int $min_x, int $max_x, string $title, string $title_x, string $title_y, string $filename) : void {
    $graph = new Graph(600, 400);
    $graph->setScale("intlin", 0, 0, $min_x, $max_x);
    $graph->setMargin(100, 10, 40, 40);
    $graph->title->Set($title);
    $graph->xaxis->title->set($title_x);
    $graph->yaxis->title->set($title_y);
    $line_plot=new LinePlot($data_y, $data_x);
    $graph->Add($line_plot);
    $graph->Stroke("../output/" . $filename . "-" . (string) time() . ".png");
}