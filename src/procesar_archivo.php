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
                $my_info[] = (object) [
                "fecha" => $fecha->format("c"), 
                "monto" => $monto
                ];

            }
            //printf("fecha es : %s y monto es: %f\n", $fecha->format("c"), $monto); 

        }
        echo "Archivo leido\n";
        fclose($manejador);
        $obj = new stdClass();
        $obj->data = $my_info;
        file_put_contents("salida.txt", json_encode($obj));

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
    return DateTime::createFromFormat("Y/m/d", $fecha, $tz);

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

leerArchivo("prueba.csv");
