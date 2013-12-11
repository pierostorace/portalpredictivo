function Agregar_Documentos(obj_origen,obj_destino)
{
	var totalOperacion=0;
    var tbl_origen = document.getElementById(obj_origen),tBody_origen=tbl_origen.tBodies[0];
    var inputs=tBody_origen.getElementsByTagName('input'),count = inputs.length,checks=new Array();//the TBODY

    for (j = 0; j < count; j++)
    {
    	if(inputs[j].type=="checkbox" && inputs[j].id!="")
    	{
    		if (inputs[j].checked)
    		{ 
    			checks.push(inputs[j]);
    		}
    	}
    }

    var tbl_destino = document.getElementById(obj_destino),tBody_destino=tbl_destino.tBodies[0],count=checks.length;
    
    if(count>0)
    {
      if(tBody_destino.rows.length==1)
      {
    	  if(tBody_destino.rows[0].innerText.indexOf("No se han encontrado")>=0)
    		  tBody_destino.deleteRow(0);
      }
      var fila,row,html;
      for (j = 0; j < count; j++)
      {
         fila=tbl_destino.tBodies[0].rows.length;
         row=tbl_destino.tBodies[0].insertRow(fila),posicion=(100 * (++fila))+1;
         html="<td class='filaTablaResultado'></td>"+
          "<td class='filaTablaResultado'>&nbsp;<img src='../../img/b_eliminar.gif' width='16px' height='16px' border='0' class='link' alt='Presione aqu&iacute; para eliminar este registro.' title='Presione aqu&iacute; para eliminar este registro.' onclick='Delete_Row(this.parentNode.parentNode)'/></td>"+
          "<td class='filaTablaResultado no_display'><input type='hidden' id='fil_gcol_id" + (++posicion) + "' value='" + checks[j].value + "' posicion='" + posicion + "' /></td>";         
         html += "<td class='filaTablaResultado center'>" + checks[j].parentNode.parentNode.cells[5].innerHTML + "</td>";
         html += "<td class='filaTablaResultado center'>" + checks[j].parentNode.parentNode.cells[4].innerHTML + "</td>";
         html += "<td class='filaTablaResultado center'>" + checks[j].parentNode.parentNode.cells[6].innerHTML + "</td>";
         html += "<td class='filaTablaResultado center'>" + checks[j].parentNode.parentNode.cells[8].innerHTML + "</td>";
         html += "<td class='filaTablaResultado center'><select class='chzn-select textoInput' name='fil_gcol_txtTipoMovimiento' id='fil_gcol_txtTipoMovimiento" + (++posicion) + "' posicion='" + posicion + "' validacion='Obj(\"fil_gcol_txtTipoMovimiento" + posicion + "\").value!=\"\"|||Debe indicar el tipo de movimiento' style='width:100px;' onChange='Repetir_EntradaSalida(this.value," + j + ")'></select></td>";
         html += "<td class='filaTablaResultado center'><select class='chzn-select textoInput' name='fil_gcol_txtCajaDetalle' id='fil_gcol_txtCajaDetalle" + (++posicion) + "' posicion='" + posicion + "' validacion='Obj(\"fil_gcol_txtCajaDetalle" + posicion + "\").value!=\"\"|||Debe indicar la caja' style='width:200px;'  onChange='Repetir_TipoCaja(this.value," + j + ")'></select></td>";
         html += "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_txtMonto" + (++posicion) + "' value='" + checks[j].parentNode.parentNode.cells[9].innerHTML + "' posicion='" + posicion + "' validacion='Obj(\"fil_gcol_txtMonto" + posicion + "\").value!=\"0\"|||Debe indicar un monto mayor a 0' style='width:70px' maxlength='10' onkeypress='return SoloDecimal(event,this.value)' onchange='Calcular_Documento_Valor(" + j + ")' /></td>";
         totalOperacion = parseFloat(totalOperacion) + parseFloat(checks[j].parentNode.parentNode.cells[9].innerHTML);
         html += "<td class='filaTablaResultado center'><input type='textbox' id='fil_gcol_txtNroDocumento" + (++posicion) + "' value='' posicion='" + posicion + "' validacion='Obj(\"fil_gcol_txtNroDocumento" + posicion + "\").value!=\"\"|||Debe indicar el nro del documento' style='width:100px' maxlength='24' onChange='Repetir_Documentos(this.value," + j + ")' /></td>";
         html += "<td class='filaTablaResultado center'><select class='chzn-select textoInput' name='fil_gcol_txtTipoOperacion' id='fil_gcol_txtTipoOperacion" + (++posicion) + "' posicion='" + posicion + "' validacion='Obj(\"fil_gcol_txtTipoOperacion" + posicion + "\").value!=\"\"|||Debe indicar del modo de operaci&oacute;n' style='width:150px;' onChange='Repetir_TipoTransaccion(this.value," + j + ")'></select></td>";         
         html += "<td class='filaTablaResultado center'><input type='textbox' id='fil_grow_fecha" + (++posicion) + "' name='fil_grow_fecha" + posicion + "' posicion='" + posicion + "' onclick=JsCalendar('fil_grow_fecha" + posicion + "','fil_grow_fecha" + posicion + "','%d/%m/%Y','','') onchange='Repetir_FechaTransaccion(this.value," + j + ")' style='width:80px'  value='' class='cajaInput' /></td>";
         row.innerHTML=html;
         Cargar_Combo("general","Combo_TipoOperacion_Movimiento","fil_gcol_txtTipoOperacion" + (posicion-1),"","fil_gcol_txtTipoOperacion" + (posicion-1));
         Cargar_Combo("general","Combo_Caja","fil_gcol_txtCajaDetalle" + (posicion-4),"","fil_gcol_txtCajaDetalle" + (posicion-4));
         Cargar_Combo("general","Combo_TipoMovimiento","fil_gcol_txtTipoMovimiento" + (posicion-5),"","fil_gcol_txtTipoMovimiento" + (posicion-5));                  
      }
      SetDocValue("txt_total",totalOperacion.toFixed(2));
    }    
}
function Repetir_Documentos(valor, fila)
{
	var tBody=Obj("tbl_mov_items").tBodies[0],filas=tBody.rows.length+1,cols=6,posicion=0,subtotal=0;
	if(filas>2)
	{
		if(confirm("¿Desea repetir el nro. de documento registrado para los demás comprobantes de la lista?"))
		{		
		    for(var i=(fila+1);i<filas;i++)
		    {
		      posicion=((100 * i) + cols);	      
		      SetDocValue("fil_gcol_txtNroDocumento" + posicion,valor);	      
		    }	    
		}
	}
}
function Repetir_TipoTransaccion(valor, fila)
{
	var tBody=Obj("tbl_mov_items").tBodies[0],filas=tBody.rows.length+1,cols=7,posicion=0,subtotal=0;
	if(filas>2)
	{
		if(confirm("¿Desea repetir el tipo de movimiento registrado para los demás comprobantes de la lista?"))
		{		
		    for(var i=(fila+1);i<filas;i++)
		    {
		      posicion=((100 * i) + cols);	      
		      SetDocValue("fil_gcol_txtTipoOperacion" + posicion,valor);			            
		    }
		}
	}
}
function Repetir_TipoCaja(valor, fila)
{
	var tBody=Obj("tbl_mov_items").tBodies[0],filas=tBody.rows.length+1,cols=4,posicion=0,subtotal=0;
	var totalOperaciones=0;
	if(filas>2)
	{
		if(confirm("¿Desea repetir el tipo de caja registrado para los demás comprobantes de la lista?"))
		{		
		    for(var i=(fila+1);i<filas;i++)
		    {
		      posicion=((100 * i) + cols);	      
		      SetDocValue("fil_gcol_txtCajaDetalle" + posicion,valor);
		      //Si se va a aplicar una retención, el sistema lo calcula en automático
		      if(valor==6)
		      {		    	  		    	  
		    	  SetDocValue("fil_gcol_txtMonto" + (++posicion),(parseFloat(tBody.rows[(i-1)].cells[6].innerHTML)*0.06).toFixed(2));
		    	  
		    	  if(DocValue("fil_gcol_txtTipoMovimiento" + (posicion-2))==1 || DocValue("fil_gcol_txtTipoMovimiento" + (posicion-2))=="")
		    		  totalOperaciones = totalOperaciones + (parseFloat(tBody.rows[(i-1)].cells[6].innerHTML)*0.06);
		    	  else
		    		  totalOperaciones = totalOperaciones - (parseFloat(tBody.rows[(i-1)].cells[6].innerHTML)*0.06);
		      }		      
		    }
		    if(valor==6)
		    	SetDocValue("txt_total",totalOperaciones.toFixed(2));
		}
	}
	else
	{		
		if(valor==6)
		{		    	  		    			
			SetDocValue("fil_gcol_txtMonto105",(parseFloat(tBody.rows[0].cells[6].innerHTML)*0.06).toFixed(2));  
		}	
	}
}
function Repetir_EntradaSalida(valor, fila)
{
	var tBody=Obj("tbl_mov_items").tBodies[0],filas=tBody.rows.length+1,cols=3,posicion=0,subtotal=0;
	if(filas>2)
	{
		if(confirm("¿Desea repetir el tipo de movimiento registrado para los demás comprobantes de la lista?"))
		{		
		    for(var i=(fila+1);i<filas;i++)
		    {
		      posicion=((100 * i) + cols);	      
		      SetDocValue("fil_gcol_txtTipoMovimiento" + posicion,valor);		        
		    }
		}
	}		
	Calcular_Documento_Valor(1);
}
function Repetir_FechaTransaccion(valor, fila)
{
	var tBody=Obj("tbl_mov_items").tBodies[0],filas=tBody.rows.length+1,cols=8,posicion=0,subtotal=0;
	if(filas>2)
	{
		if(confirm("¿Desea repetir la fecha registrada para los demás comprobantes de la lista?"))
		{		
		    for(var i=(fila+1);i<filas;i++)
		    {
		      posicion=((100 * i) + cols);	      
		      SetDocValue("fil_grow_fecha" + posicion,valor);	      
		    }
		}
	}
}
function Valida_Monto(saldo,obj_monto)
{
   if(obj_monto.value=="") obj_monto.value="0";
   else if(saldo<obj_monto.value){ obj_monto.value=saldo;}
}
function Calcular_Documento_Valor(fila)
{
	var tBody=Obj("tbl_mov_items").tBodies[0],filas=tBody.rows.length+1,cols=3,posicion=0,subtotal=0;
	var totalOperaciones=0;
	if(filas>2)
	{				
	    for(var i=1;i<filas;i++)
	    {	      
	      posicion=((100 * i) + cols);	      	      	      
	      if(DocValue("fil_gcol_txtTipoMovimiento" + posicion)==1 || DocValue("fil_gcol_txtTipoMovimiento" + posicion)=="")
	          subtotal +=parseFloat(DocValue("fil_gcol_txtMonto" + (posicion+2)));
	      if(DocValue("fil_gcol_txtTipoMovimiento" + posicion)==2)
	    	  subtotal -=parseFloat(DocValue("fil_gcol_txtMonto" + (posicion+2)));
	      
	      //alert(subtotal);
	    }
	    SetDocValue("txt_total",subtotal.toFixed(2));	
	}		
}