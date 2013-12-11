/****************************************** COMPROBANTE DE COMPRA ***********************************/
function Nueva_CotizacionDetalle(tabla,tabla_det,obj)
{
    if (Valida_Datos(tabla) == 0) {
      var tbl_destino=Obj(tabla_det),tBody=tbl_destino.tBodies[0];
      if(tBody.rows.length==1){if(tBody.rows[0].innerText.indexOf("No se han encontrado")>=0)tBody.deleteRow(0);}
      var row = document.createElement('tr'), fila=tBody.rows.length + 1, posicion=(100 * fila)+1;
      if(DocValue("fil_cmbCategoriaDocumento")=="24"){
        row.innerHTML="<td class='filaTablaResultado'></td>"+
        "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Delete_Row(this.parentNode.parentNode)'/></td>"+
        "<td class='filaTablaResultado no_display'>&nbsp;</td>"+
        "<td class='filaTablaResultado center'><select class='chzn-select textoInput' name='fil_gcol_cmbItem' id='fil_gcol_cmbItem" + (++posicion) + "' posicion='" + posicion + "' validacion='Obj(\"fil_gcol_cmbItem" + posicion + "\").value!=\"\"|Debe indicar el item'  onchange='Cargar_Objeto(\"general\",\"Valor_Producto_Precio\",\"fil_cmbcliente\",this.value,\"fil_gcol_Precio" + (posicion+3) + "\",\"value\");' style='width:300px;'></select></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_txtObservacion" + (++posicion) + "' value='' posicion='" + posicion + "' maxlength='250' style='width:350px' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_txtCantidad" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:70px' onkeypress='return SoloNumerico(event)' onchange='Valida_Cantidad(this);Calcular_CotizacionDetalle_Valor(\"" + tbl_destino.id + "\"," + posicion + ");' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_Precio" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:80px' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_Precio(this);Calcular_CotizacionDetalle_Valor(\"" + tbl_destino.id + "\"," + (posicion-1) + ")' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_Valor" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:100px' readonly /></td>";
      tBody.appendChild(row);
//      Cargar_Objeto("general","Combo_ArticulosVenta","","","fil_gcol_cmbItem" + (posicion-4),"panel");
      Cargar_Objeto("general","Combo_ArticulosVentaCliente","fil_cmbcliente","","fil_gcol_cmbItem" + (posicion-4),"panel");
      }
      else{
        row.innerHTML="<td class='filaTablaResultado'></td>"+
        "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Delete_Row(this.parentNode.parentNode)'/></td>"+
        "<td class='filaTablaResultado no_display'>&nbsp;</td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_cmbItem" + (++posicion) + "' value='' posicion='" + posicion + "' validacion='Obj(\"fil_gcol_cmbItem" + posicion + "\").value!=\"\"|Debe indicar el item' maxlength='250' style='width:350px' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_txtCantidad" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:70px' onkeypress='return SoloNumerico(event)' onchange='Valida_Cantidad(this);Calcular_CotizacionDetalle_Valor(\"" + tbl_destino.id + "\"," + posicion + ");' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_txtObservacion" + (++posicion) + "' value='' posicion='" + posicion + "' maxlength='250' style='width:350px' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_Precio" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:80px' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_Precio(this);Calcular_CotizacionDetalle_Valor(\"" + tbl_destino.id + "\"," + (posicion-1) + ")' /></td>"+
        "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_Valor" + (++posicion) + "' value='1' posicion='" + posicion + "' maxlength='10' style='width:100px' readonly /></td>";
        tBody.appendChild(row);
      }
      Calcular_Comprobante_Valor(tBody);
    }
}
function Recarga_Detalles(id_tabla){
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
function Calcular_CotizacionDetalle_Valor(id_tabla,posicion)
{
    var valor=parseFloat(DocValue("fil_gcol_txtCantidad" + posicion)) * parseFloat(DocValue("fil_gcol_Precio" + (posicion+1)));
    SetDocValue("fil_grow_Valor" + (posicion+2),valor.toFixed(2));
    Calcular_Comprobante_Valor(Obj(id_tabla).tBodies[0]);
}
function Calcular_Comprobante_Valor(tBody)
{
    var filas=tBody.rows.length+1,cols=6,posicion=0,subtotal=0;
    for(var i=1;i<filas;i++)
    {
      posicion=((100 * i) + cols);
      subtotal +=parseFloat(DocValue("fil_grow_Valor" + posicion));
    }
    SetDocValue("txt_subtotal",subtotal.toFixed(2));
}