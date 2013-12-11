<?php
  Class TipoGrafico
  {
      public $lineal = "line";
      public $area = "area";
      public $barras = "bar";
      public $pie = "pie";
  }
  Class TipoTextBox
  {
      public $texto = 0;
      public $numerico = 1;
      public $decimal = 2;
  }
  Class TipoTextDate
  {
      public $fechahora = 0;
      public $fecha = 1;
      public $hora = 2;
  }
  Class InputValidacion
  {
      public $regla = "";
      public $mensaje = "";
      function InputValidacion($var_regla="",$var_mensaje="")
      {
        $this->regla=$var_regla;
        $this->mensaje=$var_mensaje;
      }
  }
  Class GrillaBotones
  {
      public $nuevo = "";
      public $ver = "";
      public $editar = "";
      public $eliminar = "";
      public $seleccionar = "";
      function GrillaBotones($nuevo="",$ver="",$editar = "",$eliminar = "",$seleccionar = "")
      {
        $this->nuevo=$nuevo;
        $this->ver=$ver;
        $this->editar=$editar;
        $this->eliminar=$eliminar;
        $this->seleccionar=$seleccionar;
      }
  }
?>