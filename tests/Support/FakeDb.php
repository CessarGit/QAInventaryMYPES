<?php

/**
 * Doble de prueba ("fake") de $this->db para pruebas UNITARIAS.
 * No toca MySQL: guarda en memoria lo que el modelo intenta escribir,
 * para que la prueba lo revise (técnica de caja blanca).
 */
class FakeDb
{
    public $inserts = array();   // [ ['tabla' => ..., 'datos' => ...], ... ]
    public $consultas = array(); // [ ['sql' => ..., 'binds' => ...], ... ]
    public $filasConsulta = array();
    public $transaccion = array();
    private $ultimoId = 0;

    public function insert($tabla, $datos)
    {
        $this->inserts[] = array('tabla' => $tabla, 'datos' => $datos);
        $this->ultimoId++;
        return true;
    }

    public function insert_id()
    {
        return $this->ultimoId;
    }

    public function insertsEn($tabla)
    {
        return array_values(array_filter($this->inserts, function ($i) use ($tabla) {
            return $i['tabla'] === $tabla;
        }));
    }

    public function query($sql, $binds = false)
    {
        $this->consultas[] = array('sql' => $sql, 'binds' => $binds);
        return new FakeResult($this->filasConsulta);
    }

    public function where($campo, $valor = null)
    {
        return $this;
    }

    public function update($tabla, $datos = null)
    {
        return true;
    }

    public function delete($tabla)
    {
        return true;
    }

    public function trans_begin()
    {
        $this->transaccion[] = 'begin';
    }

    public function trans_start()
    {
        $this->transaccion[] = 'start';
    }

    public function trans_complete()
    {
        $this->transaccion[] = 'complete';
        return true;
    }

    public function trans_status()
    {
        return true;
    }

    public function trans_commit()
    {
        $this->transaccion[] = 'commit';
    }

    public function trans_rollback()
    {
        $this->transaccion[] = 'rollback';
    }
}

class FakeResult
{
    private $filas;

    public function __construct(array $filas)
    {
        $this->filas = $filas;
    }

    public function num_rows()
    {
        return count($this->filas);
    }

    public function row_array()
    {
        return $this->filas ? $this->filas[0] : null;
    }

    public function result_array()
    {
        return $this->filas;
    }
}
