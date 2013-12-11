<?php
  Class graphichelper{
     function Crear_Grafico($id,$tipo,$titulo,$subtitulo,$TituloEjeX,$TituloEjeY,$CategoriasX,$NombreY,$SubFijoSeries,$Series){
      $html ="<div id='" . $id . "' style='min-width: 400px; height: 400px; margin: 0 auto'></div>".
        "<script type='text/javascript'>
        $(function () {
            var chart;
            $(document).ready(function() {
                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: '" . $id . "',
                        type: '" . $tipo . "'
                    },
                    title: {
                        text: '" . $titulo . "'
                    },
                    subtitle: {
                        text: '" . $subtitulo . "'
                    },
                    xAxis: {
                        categories: ['" . implode("','",$CategoriasX) . "']
                    },
                    yAxis: {
                        title: {
                            text: '" . $NombreY . "'
                        }
                    },
                    tooltip: {
                        formatter: function() {
                            return '<b>'+ this.series.name +'</b><br/>'+
                                this.x +': " . $SubFijoSeries . "'+ this.y;
                        }
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [";
                    $name="";
                    foreach ($Series as $key => $value)
                    {
                         $html .="{name: '" . $key . "',
                                  data: [" . $value . "]},";
                    }
                    $html .="]
                });
            });

        });</script>";

        return $html;
     }
  }
?>