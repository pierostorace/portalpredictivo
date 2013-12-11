/****************************************** COMPROBANTE DE VENTA ***********************************/
function Nuevo_ComprobanteDetalle(tabla,tabla_det,obj)
{
    if (Valida_Datos(tabla) == 0) {
      var tbl_destino=Obj(tabla_det),tBody=tbl_destino.tBodies[0];
      if(tBody.rows.length==1){if(tBody.rows[0].innerText.indexOf("No se han encontrado")>=0)tBody.deleteRow(0);}
      var row = document.createElement('tr'), fila=tBody.rows.length + 1, posicion=(100 * fila)+1;
      if(DocValue("fil_cmbcategoriacomprobante")=="4"){
        row.innerHTML="<td class='filaTablaResultado'></td>"+
        "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Delete_Row(this.parentNode.parentNode)'/></td>"+
        "<td class='filaTablaResultado no_display'>&nbsp;</td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtCantidad" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:70px' onkeypress='return SoloNumerico(event)' onchange='Valida_Cantidad(this);Calcular_ComprobanteDetalle_Valor(\"" + tbl_destino.id + "\"," + posicion + ");' /></td>"+
        "<td class='filaTablaResultado center'><select class='chzn-select textoInput' name='fil_grow_cmbItem' id='fil_grow_cmbItem" + (++posicion) + "' posicion='" + posicion + "' validacion='Obj(\"fil_grow_cmbItem" + posicion + "\").value!=\"\"|Debe indicar el item' style='width:300px;'></select></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtObservacion" + (++posicion) + "' value='' posicion='" + posicion + "' maxlength='250' style='width:350px' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_Precio" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:80px' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_Precio(this);Calcular_ComprobanteDetalle_Valor(\"" + tbl_destino.id + "\"," + (posicion-3) + ")' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_Valor" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:100px' readonly /></td>";
      tBody.appendChild(row);
//      Cargar_Combo("general","Combo_ArticulosCompra","fil_grow_cmbItem" + (posicion-3),"","fil_grow_cmbItem" + (posicion-3));
      Cargar_Objeto("general","Combo_ArticulosVentaCliente","fil_cmbcliente","","fil_gcol_cmbItem" + (posicion-3),"panel");
      }
      else{
        row.innerHTML="<td class='filaTablaResultado'></td>"+
        "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Delete_Row(this.parentNode.parentNode)'/></td>"+
        "<td class='filaTablaResultado no_display'>&nbsp;</td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtCantidad" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:70px' onkeypress='return SoloNumerico(event)' onchange='Valida_Cantidad(this);Calcular_ComprobanteDetalle_Valor(\"" + tbl_destino.id + "\"," + posicion + ");' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtDescripcion" + (++posicion) + "' value='' posicion='" + posicion + "' validacion='Obj(\"fil_grow_txtDescripcion" + posicion + "\").value!=\"\"|Debe indicar el item' maxlength='250' style='width:350px' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtObservacion" + (++posicion) + "' value='' posicion='" + posicion + "' maxlength='250' style='width:350px' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_Precio" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:80px' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_Precio(this);Calcular_ComprobanteDetalle_Valor(\"" + tbl_destino.id + "\"," + (posicion-3) + ")' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_Valor" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:100px' readonly /></td>";
        tBody.appendChild(row);
      }
      Calcular_Comprobante_Valor(tBody);
    }
}
function Valida_Categoria(id_tabla){
   if(Obj(id_tabla)){ var tBody=Obj(id_tabla).tBodies[0],filas=tBody.rows.length;
     if(filas>0 && tBody.rows[0].innerText.indexOf("No se han encontrado")<0){ for(var i=0;i<filas;i++){ tBody.deleteRow(i); } }
   }
}
function Valida_Cantidad(obj_cantidad)
{
   if(obj_cantidad.value=="" || obj_cantidad.value=="0")obj_cantidad.value="1";
}
function Valida_Precio(obj_precio)
{
   if(obj_precio.value=="" || obj_precio.value=="0")obj_precio.value="1";
}
function Calcular_ComprobanteDetalle_Valor(id_tabla,posicion)
{
    var valor=parseFloat(DocValue("fil_grow_txtCantidad" + posicion)) * parseFloat(DocValue("fil_grow_Precio" + (posicion+3)));
    SetDocValue("fil_gcol_Valor" + (posicion+4),valor);
    Calcular_Comprobante_Valor(Obj(id_tabla).tBodies[0]);
}
function Calcular_Comprobante_Valor(tBody)
{
    var filas=tBody.rows.length+1,cols=6,posicion=0,subtotal=0;
    for(var i=1;i<filas;i++)
    {
      posicion=((100 * i) + cols);
      subtotal +=parseFloat(DocValue("fil_gcol_Valor" + posicion));
    }
    SetDocValue("txt_subtotal",subtotal.toFixed(2));
    if(DocValue("fil_cmbtipocomprobante")=="4"){ //Factura
      var igv_prc=parseFloat(DocValue("txt_igv_prc"));
      var igv=subtotal * igv_prc;
      SetDocValue("txt_igv",igv.toFixed(2));
      SetDocValue("txt_total",subtotal + igv);
    }
    else{
      SetDocValue("txt_total",subtotal.toFixed(2));
    }
}

/****************************************** NOTA DE CREDITO ***********************************/
function Agregar_NotaCredito_DocReferencia(id_origen,id_destino)
{
    var tbl_origen=Obj(id_origen),tBody_origen=tbl_origen.tBodies[0];
    var tbl_destino=Obj(id_destino),tBody_destino=tbl_destino.tBodies[0];
    var inputs=tBody_origen.getElementsByTagName('input'),count = inputs.length,checks=new Array();//the TBODY

    for (j = 0; j < count; j++){if(inputs[j].type=="checkbox" && inputs[j].id!=""){if (inputs[j].checked){ checks.push(inputs[j]);}}}

    count = checks.length;
    if(count>0){
      if(tBody_destino.rows.length==1){if(tBody_destino.rows[0].innerText.indexOf("No se han encontrado")>=0)tBody_destino.deleteRow(0);}
      var fila,row,html;
      for (j = 0; j < count; j++){
         row = document.createElement('tr'), fila=tBody_destino.rows.length + 1, posicion=(100 * fila)+1;
         row.innerHTML="<td class='filaTablaResultado'></td>"+
          "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Delete_Row(this.parentNode.parentNode)'/></td>"+
          "<td class='filaTablaResultado no_display'><input type='hidden' id='fil_grow_id" + (++posicion) + "' value='" + checks[j].value + "' posicion='" + posicion + "' /></td>"+
          "<td class='filaTablaResultado center'>" + checks[j].parentNode.parentNode.cells[5].innerText + "</td>";
         tBody_destino.appendChild(row);
         Obj("hf_idcventa").value +=(Obj("hf_idcventa").value!=""?",":"") + checks[j].value;
      }
    }
    //Calcular_NotaCredito_Monto(id_destino);
}
function Valida_DocReferencia_Monto(obj_monto)
{   
   if(obj_monto.value==""){ obj_monto.value="0";}
//   else if(parseFloat(DocValue("txt_total"))<obj_monto.value){ obj_monto.value="0";}
}
function Calcular_NotaCredito_Monto(id_tabla)
{
    var tBody=Obj(id_tabla).tBodies[0], filas=tBody.rows.length+1,cols=3,posicion=0,subtotal=0;
    for(var i=1;i<filas;i++)
    {
      posicion=((100 * i) + cols);
      subtotal +=parseFloat(DocValue("fil_gcol_txtMonto" + posicion));
    }
    var igv_prc=parseFloat(DocValue("txt_igv_prc"));
    SetDocValue("txt_subtotal",subtotal.toFixed(2));
    var igv=subtotal * igv_prc;
    SetDocValue("txt_igv",igv.toFixed(2));
    SetDocValue("txt_total",(subtotal + igv).toFixed(2));
}
function Nuevo_NotaCreditoDetalle(tabla,tabla_det,obj)
{
    if (Valida_Datos(tabla) == 0) {
      var tbl_destino=Obj(tabla_det),tBody=tbl_destino.tBodies[0];
      if(tBody.rows.length==1){if(tBody.rows[0].innerText.indexOf("No se han encontrado")>=0)tBody.deleteRow(0);}
      var row = document.createElement('tr'), fila=tBody.rows.length + 1, posicion=(1000 * fila)+1;
      if(DocValue("fil_cmbcategoriacomprobante")=="11"){
        row.innerHTML="<td class='filaTablaResultado'></td>"+
        "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Descontar_Monto(DocValue(\"fil_gcol_Valor" + (posicion+4) + "\"));Delete_Row(this.parentNode.parentNode);'/></td>"+
        "<td class='filaTablaResultado no_display'>&nbsp;</td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtCantidad" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:70px' onkeypress='return SoloNumerico(event)' onchange='Valida_NotaCreditoCantidad(this);Calcular_NotaCreditoDetalle_Valor(\"" + tabla_det + "\"," + posicion + ");' /></td>"+
        "<td class='filaTablaResultado center'><select class='chzn-select textoInput' name='fil_grow_cmbItem' id='fil_grow_cmbItem" + (++posicion) + "' posicion='" + posicion + "' validacion='Obj(\"fil_grow_cmbItem" + posicion + "\").value!=\"\"|||Debe indicar el item' style='width:300px;'></select></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_Precio" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:80px' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_NotaCreditoPrecio(this);Calcular_NotaCreditoDetalle_Valor(\"" + tabla_det + "\"," + (posicion-2) + ")' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_Valor" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:100px' readonly /></td>";
        tBody.appendChild(row);
        Cargar_Combo("general","Combo_Articulos_ComprobanteVenta","fil_grow_cmbItem" + (posicion-2),"hf_idcventa","fil_grow_cmbItem" + (posicion-2));
      }
      else{
        row.innerHTML="<td class='filaTablaResultado'></td>"+
        "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Descontar_Monto(DocValue(\"fil_gcol_Valor" + (posicion+4) + "\"));Delete_Row(this.parentNode.parentNode);'/></td>"+
        "<td class='filaTablaResultado no_display'>&nbsp;</td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtCantidad" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:70px' onkeypress='return SoloNumerico(event)' onchange='Valida_NotaCreditoCantidad(this);Calcular_NotaCreditoDetalle_Valor(\"" + tabla_det + "\"," + posicion + ");' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtDescripcion" + (++posicion) + "' value='' posicion='" + posicion + "' maxlength='250' style='width:300px' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_Precio" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:80px' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_NotaCreditoPrecio(this);Calcular_NotaCreditoDetalle_Valor(\"" + tabla_det + "\"," + (posicion-2) + ")' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_Valor" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:100px' readonly /></td>";
        tBody.appendChild(row);
      }
      Calcular_NotaCredito_Valor(tBody);
    }
}
function Descontar_Monto(valor)
{
    var subtotal=parseFloat(DocValue("txt_subtotal")) - parseFloat(valor);
    SetDocValue("txt_subtotal",subtotal.toFixed(2));
    var igv_prc=parseFloat(DocValue("txt_igv_prc"));
    var igv=subtotal * igv_prc;
    SetDocValue("txt_igv",igv.toFixed(2));
    SetDocValue("txt_total",(subtotal + igv).toFixed(2));
}
function Valida_NotaCreditoCantidad(obj_cantidad)
{
   if(obj_cantidad.value=="" || obj_cantidad.value=="0")obj_cantidad.value="1";
}
function Valida_NotaCreditoPrecio(obj_precio)
{
   if(obj_precio.value=="" || obj_precio.value=="0")obj_precio.value="1";
}
function Calcular_NotaCreditoDetalle_Valor(id_tabla,posicion)
{
    var valor=parseFloat(DocValue("fil_grow_txtCantidad" + posicion)) * parseFloat(DocValue("fil_grow_Precio" + (posicion+2)));
    SetDocValue("fil_gcol_Valor" + (posicion+3),valor.toFixed(2));
    Calcular_NotaCredito_Valor(Obj(id_tabla).tBodies[0]);
}
function Calcular_NotaCredito_Valor(tBody)
{
    var filas=tBody.rows.length,cols=6,subtotal=0;
    for(var i=0;i<filas;i++)
    {
      subtotal +=parseFloat(tBody.rows[i].cells[cols].childNodes[0].value);
    }
    SetDocValue("txt_subtotal",subtotal.toFixed(2));
//    if(DocValue("fil_cmbtipocomprobante")=="4"){ //Factura
      var igv_prc=parseFloat(DocValue("txt_igv_prc"));
      var igv=subtotal * igv_prc;
      SetDocValue("txt_igv",igv.toFixed(2));
      SetDocValue("txt_total",(subtotal + igv).toFixed(2));
//    }
//    else{
//      SetDocValue("txt_total",subtotal.toFixed(2));
//    }
}

/****************************************** NOTA DE DEBITO ***********************************/
function Agregar_NotaDebito_DocReferencia(id_origen,id_destino)
{
    var tbl_origen=Obj(id_origen),tBody_origen=tbl_origen.tBodies[0];
    var tbl_destino=Obj(id_destino),tBody_destino=tbl_destino.tBodies[0];
    var inputs=tBody_origen.getElementsByTagName('input'),count = inputs.length,checks=new Array();//the TBODY

    for (j = 0; j < count; j++){if(inputs[j].type=="checkbox" && inputs[j].id!=""){if (inputs[j].checked){ checks.push(inputs[j]);}}}

    count = checks.length;
    if(count>0){
      if(tBody_destino.rows.length==1){if(tBody_destino.rows[0].innerText.indexOf("No se han encontrado")>=0)tBody_destino.deleteRow(0);}
      var fila,row,html;
      for (j = 0; j < count; j++){
         row = document.createElement('tr'), fila=tBody_destino.rows.length + 1, posicion=(100 * fila)+1;
         row.innerHTML="<td class='filaTablaResultado'></td>"+
          "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Delete_Row(this.parentNode.parentNode)'/></td>"+
          "<td class='filaTablaResultado no_display'><input type='hidden' id='fil_grow_id" + (++posicion) + "' value='" + checks[j].value + "' posicion='" + posicion + "' /></td>"+
          "<td class='filaTablaResultado center'>" + checks[j].parentNode.parentNode.cells[5].innerText + "</td>"+
          "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_txtMonto" + (++posicion) + "' value='0' posicion='" + posicion + "' style='width:70px' maxlength='10' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_NotaDebito_DocReferencia_Monto(this);Calcular_NotaDebito_Monto(\"" + tbl_destino.id + "\");' /></td>";
         tBody_destino.appendChild(row);
         Obj("hf_idcventa").value +=(Obj("hf_idcventa").value!=""?",":"") + checks[j].value;
      }
    }
    Calcular_NotaDebito_Monto(id_destino);
}
function Valida_NotaDebito_DocReferencia_Monto(obj_monto)
{
   if(obj_monto.value==""){ obj_monto.value="0";}
   //else if(parseFloat(DocValue("txt_total"))<obj_monto.value){ obj_monto.value="0";}
}
function Calcular_NotaDebito_Monto(id_tabla)
{
    var tBody=Obj(id_tabla).tBodies[0], filas=tBody.rows.length+1,cols=3,posicion=0,subtotal=0;
    for(var i=1;i<filas;i++)
    {
      posicion=((100 * i) + cols);
      subtotal +=parseFloat(DocValue("fil_gcol_txtMonto" + posicion));
    }
    SetDocValue("txt_monto",subtotal.toFixed(2));
}
function Nuevo_NotaDebitoDetalle(tabla,tabla_det,obj)
{
    if (Valida_Datos(tabla) == 0) {
      var tbl_destino=Obj(tabla_det),tBody=tbl_destino.tBodies[0];
      if(tBody.rows.length==1){if(tBody.rows[0].innerText.indexOf("No se han encontrado")>=0)tBody.deleteRow(0);}
      var row = document.createElement('tr'), fila=tBody.rows.length + 1, posicion=(1000 * fila)+1;
      if(DocValue("fil_cmbcategoriacomprobante")=="13"){
        row.innerHTML="<td class='filaTablaResultado'></td>"+
        "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Descontar_Monto(DocValue(\"fil_gcol_Valor" + (posicion+4) + "\"));Delete_Row(this.parentNode.parentNode)'/></td>"+
        "<td class='filaTablaResultado no_display'>&nbsp;</td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtCantidad" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:70px' onkeypress='return SoloNumerico(event)' onchange='Valida_NotaDebitoCantidad(this);Calcular_NotaDebitoDetalle_Valor(\"" + tabla_det + "\"," + posicion + ");' /></td>"+
        "<td class='filaTablaResultado center'><select class='chzn-select textoInput' name='fil_grow_cmbItem' id='fil_grow_cmbItem" + (++posicion) + "' posicion='" + posicion + "' validacion='Obj(\"fil_grow_cmbItem" + posicion + "\").value!=\"\"|Debe indicar el item' style='width:300px;'></select></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_Precio" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:80px' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_NotaDebitoPrecio(this);Calcular_NotaDebitoDetalle_Valor(\"" + tabla_det + "\"," + (posicion-2) + ")' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_Valor" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:100px' readonly /></td>";
        tBody.appendChild(row);
        Cargar_Combo("general","Combo_Articulos_ComprobanteVenta","fil_grow_cmbItem" + (posicion-2),"hf_idcventa","fil_grow_cmbItem" + (posicion-2));
      }
      else{
        row.innerHTML="<td class='filaTablaResultado'></td>"+
        "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Descontar_Monto(DocValue(\"fil_gcol_Valor" + (posicion+4) + "\"));Delete_Row(this.parentNode.parentNode)'/></td>"+
        "<td class='filaTablaResultado no_display'>&nbsp;</td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtCantidad" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:70px' onkeypress='return SoloNumerico(event)' onchange='Valida_NotaDebitoCantidad(this);Calcular_NotaDebitoDetalle_Valor(\"" + tabla_det + "\"," + posicion + ");' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtDescripcion" + (++posicion) + "' value='' posicion='" + posicion + "' maxlength='250' style='width:300px' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_Precio" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:80px' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_NotaDebitoPrecio(this);Calcular_NotaDebitoDetalle_Valor(\"" + tabla_det + "\"," + (posicion-2) + ")' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_Valor" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:100px' readonly /></td>";
        tBody.appendChild(row);
      }
      Calcular_NotaDebito_Valor(tBody);
    }
}
function Descontar_Monto(valor)
{
    var subtotal=parseFloat(DocValue("txt_subtotal")) - parseFloat(valor);
    SetDocValue("txt_subtotal",subtotal.toFixed(2));
    var igv_prc=parseFloat(DocValue("txt_igv_prc"));
    var igv=subtotal * igv_prc;
    SetDocValue("txt_igv",igv.toFixed(2));
    SetDocValue("txt_total",(subtotal + igv).toFixed(2));
}
function Valida_NotaDebitoCantidad(obj_cantidad)
{
   if(obj_cantidad.value=="" || obj_cantidad.value=="0")obj_cantidad.value="1";
}
function Valida_NotaDebitoPrecio(obj_precio)
{
   if(obj_precio.value=="" || obj_precio.value=="0")obj_precio.value="1";
}
function Calcular_NotaDebitoDetalle_Valor(id_tabla,posicion)
{
    var valor=parseFloat(DocValue("fil_grow_txtCantidad" + posicion)) * parseFloat(DocValue("fil_grow_Precio" + (posicion+2)));
    SetDocValue("fil_gcol_Valor" + (posicion+3),valor.toFixed(2));
    Calcular_NotaCredito_Valor(Obj(id_tabla).tBodies[0]);
}
function Calcular_NotaDebito_Valor(tBody)
{
    var filas=tBody.rows.length,cols=6,subtotal=0;
    for(var i=0;i<filas;i++)
    {
      subtotal +=parseFloat(tBody.rows[i].cells[cols].childNodes[0].value);
    }
    SetDocValue("txt_subtotal",subtotal.toFixed(2));
//    if(DocValue("fil_cmbtipocomprobante")=="4"){ //Factura
      var igv_prc=parseFloat(DocValue("txt_igv_prc"));
      var igv=subtotal * igv_prc;
      SetDocValue("txt_igv",igv.toFixed(2));
      SetDocValue("txt_total",(subtotal + igv).toFixed(2));
//    }
//    else{
//      SetDocValue("txt_total",subtotal.toFixed(2));
//    }
}