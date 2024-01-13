<?php
/**
 * Lee un archivo csv y extrae informacion relevante.
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
 * Lee el archivo de fuente y extrae la informacion.
 *
 * @param string $nombre_archivo El archivo a procesar
 *
 * @return null 
 */
function leerArchivo(string $nombre_archivo)
{
    if (file_exists($nombre_archivo)) {
        $tz = new DateTimeZone("America/Bogota");
        $manejador = fopen($nombre_archivo, "r") or die("Unable to open file");
        $row = 0;
        $my_info = [];
        while (($data = fgetcsv($manejador, 0, "\t")) !== false) {
            $row++;
            if ($row <2) {
                continue;
            }
            //depurarArchivo($data, $row);
            $fecha =  procesarFecha($data[0], $tz);
            if ($fecha === false) {
                throw new Exception("Fallo al procesar fecha en linea $row \n");
                //echo $fecha->format("d-m-Y");
            }
            $monto = procesarMonto($data[5]);
            if ($monto < -6000) {
                $my_info[] = [
                "fecha" => $fecha,
                "monto" => $monto,
                ];

            }
            //printf("fecha es : %s y monto es: %f\n", $fecha->format("c"), $monto); 

        }
        echo "Archivo leido\n";
        fclose($manejador);
        //guardarJSON($my_info);
        generarReportes($my_info);

    } else {
        echo "File $nombre_archivo not found \n";
    }

}

/**
 * Muestra informacion de depuracion al leer archivo
 *
 * @param array $data La linea actual del archivo que se lee.
 * @param int   $row  El numero de la
 *                    línea actual.
 *
 * @return null 
 */
function depurarArchivo(array $data, int $row)
{
    $num = count($data);
    echo "fila: $row \n";
    echo "<p> $num files in line $row: <br/></p>\n";
    print_r($data);
    echo "-------------\n";
}

/**
 * Convierte la fecha de cadena a object fecha
 *
 * @param string       $fecha La fecha que viene del archivo
 * @param DateTimeZone $tz    La zona horaria en objeto
 *
 * @return DateTime la fecha como objeto.
 */
function procesarFecha(string $fecha, DateTimeZone $tz)
{
    $date_time = DateTime::createFromFormat("Y/m/d", $fecha, $tz);
    if ($date_time === false) {
        return $date_time;
    }
    $date_time->setTime(0, 0);
    return $date_time;

}

/**
 * Convierte el monto de cadena a tipo float
 * 
 * @param string $monto el monto que viene del archivo
 *
 * @return float El monto de tipo float
 */
function procesarMonto(string $monto)
{
    //    $valor = str_replace(",", "", $monto);
    return (float) filter_var(
        $monto, FILTER_SANITIZE_NUMBER_FLOAT, 
        FILTER_FLAG_ALLOW_FRACTION
    );
}

/**
 * Guarda la informacion util como json en un archivo de texto
 *
 * @param array $info Arreglo con los registros a guardar
 *
 * @return null
 */
function guardarJSon(array $info)
{
    $nuevo_arreglo = [];
    foreach ($info as $elem) {
        $record = new stdClass();
        $record->monto = (string) $elem["monto"];
        $record->fecha = $elem["fecha"]->format("c");
        $nuevo_arreglo[] = $record;
    }
        $obj = new stdClass();
        $obj->data = $nuevo_arreglo;
        file_put_contents("salida.txt", json_encode($obj));
}

/**
 * Genera los reportes a partir de la data suministrada.
 *
 * @param array $info la data para hacer reportes
 *
 * @return null
 */
function generarReportes(array $info) 
{
    include_once "generador_reportes.php";
    crearGraficos($info);
}

leerArchivo("prueba.csv");
