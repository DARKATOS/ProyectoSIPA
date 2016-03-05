<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConvenioCRUD
 *
 * @author Jorge Alejandro
 */

session_start();
class ConvenioCRUD {

    public function __construct() {
    }

    function add($argumentos) {
        if ($_SESSION['rol']==5) {
            extract($argumentos);
            error_log("ENTRO");
            error_log($_SESSION['rol']);
            $sql = "INSERT INTO convenio VALUES($codigo, '$titulo','$razon','$fecha_inicio','$fecha_fin',$codigo_empesa_externa)";
            $ok = UtilConexion::$pdo->exec($sql);
            echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "No se pudo agregar la empresa"));
        }
        else{
            echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "No Tiene Permisos De Administrador"));
        }
    }

    /**
     * Actualiza una fila de Ciudads.
     * @param <type> $argumentos Un array con el id a buscar y los otros nuevos datos,
     * estos datos se proporcionan de la siguiente manera:
     * $argumentos[0]: Id de la Ciudad a buscar y actualizar, los demás datos
     * corresponden a las columnas que se van a actualizar
     */
    function edit($argumentos) {
        if ($_SESSION['rol']==5) {
            extract($argumentos);
            $sql = "UPDATE convenio set titulo='$titulo',razon='$razon',fecha_inicio='$fecha_inicio',fecha_fin='$fecha_fin',codigo_empesa_externa=$codigo_empesa_externa WHERE codigo=$codigo";
            $ok = UtilConexion::$pdo->exec($sql);
            echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "Falló la actualización de los datos"));
        }
        else{
            echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "No Tiene Permisos De Administrador"));
        }
        
    }

    /**
     * Elimina las Ciudads cuyos IDs se pasen como argumentos.
     * @param <type> $argumentos los IDs de las Ciudads a ser eliminadas.
     * $argumentos es un cadena que contiene uno o varios números separados por
     * comas, que corresponden a los IDs de las filas a eliminar.
     */
    function del($argumentos) {
        if ($_SESSION['rol']==5) {
            $datos =$argumentos['id'];
            error_log($datos);
            $ok = UtilConexion::$pdo->exec("DELETE FROM convenio WHERE codigo=$datos");
            echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "Falló la eliminación"));
        }
        else{
            echo json_encode($ok ? array('ok' => $ok, "mensaje" => "") : array('ok' => $ok, "mensaje" => "No Tiene Permisos De Administrador"));
        }
    }

    /**
     * Devuelve los datos necesarios para construir una tabla dinámica.
     * @param <type> $argumentos los argumentos enviados por:
     * Ciudad.js.crearTablaCiudades()
     */
    function select($argumentos) {
        extract($argumentos);
        $count = UtilConexion::$pdo->query("SELECT codigo FROM convenio")->rowCount();
        // Calcula el total de páginas por consulta
        if ($count > 0) {
            $total_pages = ceil($count / $rows);
        } else {
            $total_pages = 0;
        }

        // Si por alguna razón página solicitada es mayor que total de páginas
        // Establecer a página solicitada total paginas  (¿por qué no al contrario?)
        if ($page > $total_pages)
            $page = $total_pages;

        // Calcular la posición de la fila inicial
        $start = $rows * $page - $rows;
        //  Si por alguna razón la posición inicial es negativo ponerlo a cero
        // Caso típico es que el usuario escriba cero para la página solicitada
        if ($start < 0)
            $start = 0;

        $respuesta = [
            'total' => $total_pages,
            'page' => $page,
            'records' => $count
        ];
        $sql = "SELECT * FROM convenio ORDER BY $sidx $sord LIMIT $rows OFFSET $start";
        foreach (UtilConexion::$pdo->query($sql) as $fila) {
            $respuesta['rows'][] = [
                'codigo' => $fila['codigo'],
                'cell' => [$fila['codigo'], $fila['titulo'],$fila['razon'],$fila['fecha_inicio'],$fila['fecha_fin'],$fila['codigo_empesa_externa']]
            ];
        }
        echo json_encode($respuesta);
    }
}
