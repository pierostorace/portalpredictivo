function Nuevo_AlmacenGuiaDetalle(tabla,obj,tipo)
{
    if (Valida_Datos(tabla) == 0) {
      var tbl_destino=obj.parentNode.parentNode,tBody=tbl_destino.tBodies[0];
      if(tBody.rows.length==1){if(tbl_destino.rows[1].innerText.indexOf("No se han encontrado")>=0)tbl_destino.deleteRow(1);}
      var row = document.createElement('tr'), fila=tBody.rows.length + 1, posicion=(100 * fila)+1;
      row.innerHTML="<td class='filaTablaResultado'></td>"+
      "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Delete_Row(this.parentNode.parentNode)'/></td>"+
      "<td class='filaTablaResultado no_display'>&nbsp;</td>"+
      "<td class='filaTablaResultado center'><select class='chzn-select textoInput' name='fil_grow_cmbItem' id='fil_grow_cmbItem" + (++posicion) + "' posicion='" + posicion + "' validacion='Obj(\"fil_grow_cmbItem" + posicion + "\").value!=\"\"|Debe indicar el item' style='width:300px;' onchange='Cargar_Combo(\"general\",\"Valor_Producto_Stock\",\"td_stock" + posicion + "\",\"fil_grow_cmbItem" + posicion + "\",\"td_stock" + posicion + "\");Cargar_Combo(\"general\",\"Valor_Producto_UnidadMedida\",\"td_medida" + posicion + "\",\"fil_grow_cmbItem" + posicion + "\",\"td_medida" + posicion + "\")'></select></td>"+
      "<td class='filaTablaResultado center' id='td_medida" + posicion + "'></td>"+
      "<td class='filaTablaResultado center' id='td_stock" + posicion + "'></td>"+
      "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_txtCantidad" + (++posicion) + "' value='1' posicion='" + posicion + "' " + (tipo==2?"validacion='parseFloat(DocInnerText(\"td_stock" + (posicion-1) + "\"))>=parseFloat(DocValue(\"fil_grow_txtCantidad" + (posicion) + "\"))|No puede retirar una cantidad mayor al stock actual'":"") + " maxlength='10' style='width:70px' onkeypress='return SoloDecimal(event,this.value)' onchange='Valida_Movimiento(this)' /></td>";
      tBody.appendChild(row);
      Cargar_Combo("general","Combo_Articulos","fil_grow_cmbItem" + (posicion-1),"","fil_grow_cmbItem" + (posicion-1));
    }
}
function Valida_Movimiento(obj_mov)
{
     if(obj_mov.value=="" || obj_mov.value=="0") obj_mov.value="1";
}

function Agregar_Articulos(obj_origen,obj_destino)
{
    var tbl_origen = document.getElementById(obj_origen),tBody_origen=tbl_origen.tBodies[0];
    var inputs=tBody_origen.getElementsByTagName('input'),count = inputs.length,checks=new Array();//the TBODY

    for (j = 0; j < count; j++){if(inputs[j].type=="checkbox" && inputs[j].id!=""){if (inputs[j].checked){ checks.push(inputs[j]);}}}

    var tbl_destino = document.getElementById(obj_destino),tBody_destino=tbl_destino.tBodies[0],count=checks.length;

    if(count>0){
      if(tBody_destino.rows.length==1){if(tBody_destino.rows[0].innerText.indexOf("No se han encontrado")>=0)tBody_destino.deleteRow(0);}
      var fila,row,html;
      for (j = 0; j < count; j++){
         fila=tBody_destino.rows.length;
         row=tBody_destino.insertRow(fila),posicion=(100 * (++fila))+1;
         html="<td class='filaTablaResultado'>&nbsp;</td>"+
          "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Delete_Row(this.parentNode.parentNode)'/></td>"+
          "<td class='filaTablaResultado no_display'><input type='hidden' id='fil_gcol_id" + (++posicion) + "' value='" + checks[j].value + "' posicion='" + posicion + "' /></td>";
         html += "<td class='filaTablaResultado textoData center'>" + checks[j].parentNode.parentNode.cells[2].innerText + "</td>"
         html += "<td class='filaTablaResultado textoData  center'>" + checks[j].parentNode.parentNode.cells[3].innerHTML + "</td>";
         html += "<td class='filaTablaResultado textoData  center'><input type='textbox' id='fil_gcol_stock" + (++posicion) + "' value='" + checks[j].parentNode.parentNode.cells[4].innerHTML + "' posicion='" + posicion + "' class='textoLabel' style='width:70px' disabled /></td>";
         html += "<td class='filaTablaResultado textoData  center'><input type='textbox' id='fil_gcol_stockreal" + (++posicion) + "' value='0' posicion='" + posicion + "' validacion='Obj(\"fil_gcol_stockreal" + posicion + "\").value!=\"\"|Debe indicar un monto igual o mayor a 0' style='width:70px' maxlength='10' onkeypress='return SoloDecimal(event,this.value)' /></td>";
         html += "<td class='filaTablaResultado textoData  center'><input type='textbox' id='fil_grow_txtComentario" + (++posicion) + "' value='' posicion='" + posicion + "' style='width:400px' maxlength='250'/></td>";
         row.innerHTML=html;
      }
    }
}