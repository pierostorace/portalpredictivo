<?php 
/**********************************************************************GENERAL******************************************************************/
define("VALOR_IGV",0.18);	
define("VALOR_RETENCION_SALDO",0.94);
define("VALOR_RETENCION",0.06);
define("FLAG_ACTIVO","1");
define("FLAG_INACTIVO","0");
define('CORREO_ADMINISTRACION','lyui@galverperu.com;rvela@galverperu.com');
define('CORREO_VENTAS','nbarrios@galverperu.com');
define('GERENCIA_GENERAL','egaldos@galverperu.com');
define('GERENCIA_COMERCIAL','amgaldos@galverperu.com');
define('MONEDA_SOLES','1');
define('MONEDA_DOLARES','2');
/**********************************************************************COTIZACIÓN****************************************************************/ 
define("ESTADO_COTIZACION_PENDIENTE",1);
define("ESTADO_COTIZACION_APROBADA",2);
define("ESTADO_COTIZACION_RECHAZADA",3);
define("ESTADO_COTIZACION_ANULADA",4);
define("ESTADO_COTIZACION_ORDENCOMPRA",5);  
/**********************************************************************ORDENES DE SERVICIO********************************************************/ 
define("ESTADO_OS_PENDIENTE",1);
define("ESTADO_OS_ENPRODUCCION",2);
define("ESTADO_OS_LISTAPARAENTREGAR",3);
define("ESTADO_OS_ANULADA",4);
define("ESTADO_OS_FINALIZADA",5);
/**********************************************************************GUÍAS DE REMISIÓN**********************************************************/
define("ESTADO_GR_EMITIDA",1);
define("ESTADO_GR_FACTURADA",2);
define("ESTADO_GR_ANULADA",3);
define("ESTADO_GR_PENDIENTE",4);
/*********************************************************************FACTURACION**************************************************************/
define("TEXTO_DOCUMENTOXCOBRAR","Documento por cobrar");
define("TEXTO_DOCUMENTOXCOBRAR_AR","Documento por cobrar - Agente de Retenci&oacute;n");
define("ESTADO_COMPROBANTE_PENDIENTE",1);
define("ESTADO_COMPROBANTE_CANCELADA",2);
define("ESTADO_COMPROBANTE_ANULADA",3);
define("ESTADO_COMPROBANTE_PAGO_PARCIAL",4);
define("ESTADO_COMPROBANTE_VENCIDA",5);
define("ESTADO_COMPROBANTE_IMPRESA",6);
define("TIPO_COMPROBANTE_VENTA_BOLETA","1");
define("TIPO_COMPROBANTE_VENTA_FACTURA","2");
define("TIPO_COMPROBANTE_VENTA_NOTACREDITO",9);
define("TIPO_MOVIMIENTO_ENTRADA",17);
define("TIPO_MOVIMIENTO_SALIDA",18);
/**********************************************************************************************************************/

?>