/*Ajax*/
var Pagina="";
function MostrarPaginaIntrepretada(url,div){Pagina=div;$("#loading").overlay().load();ajax=crearXMLHttpRequest();ajax.open("GET",url,true);ajax.onreadystatechange=procesarPaginaIntrepretada;ajax.send(null);}
function procesarPaginaIntrepretada(){if(ajax.readyState==4){var scs=ajax.responseText.extractScript();document.getElementById(Pagina).innerHTML=ajax.responseText;scs.evalScript();$("#loading").overlay().close();}}
function MostrarPeticion (url){/*alert(url);*/ajax=crearXMLHttpRequest();ajax.open("GET",url,true);ajax.onreadystatechange=procesarPeticion;ajax.send(null);}
function procesarPeticion(){if(ajax.readyState==4){/*alert(ajax.responseText);*/var scs=ajax.responseText.extractScript();scs.evalScript();}}
function PopUpPeticion (url){ajax=crearXMLHttpRequest();ajax.open("GET",url,true);ajax.onreadystatechange=PopUpprocesarPeticion;ajax.send(null);}
function PopUpprocesarPeticion(){if(ajax.readyState==4){var scs=ajax.responseText.extractScript();Ver_PopUp(ajax.responseText);scs.evalScript();}}
function UpdatePanel(url,div){var ajax=crearXMLHttpRequest();ajax.open("GET",url,true);ajax.onreadystatechange=function(){if(ajax.readyState==4){var scs=ajax.responseText.extractScript();document.getElementById(div).innerHTML=ajax.responseText;scs.evalScript();}};ajax.send(null);SetDocInnerHTML(div, '<img src="../../img/ajax-loader.gif" width="16px" height="16px" alt="Cargando"/>');}
var crearXMLHttpRequest=function(){var http;try{http=new XMLHttpRequest;crearXMLHttpRequest=function(){return new XMLHttpRequest;};}catch(e){var msxml=['MSXML2.XMLHTTP.3.0','MSXML2.XMLHTTP','Microsoft.XMLHTTP'];for(var i=0,len=msxml.length;i<len;++i){try{http=new ActiveXObject(msxml[i]);crearXMLHttpRequest=function(){return new ActiveXObject(msxml[i]);};break;}catch(e){}}}return http;};
/*Funciones*/
var ie = (navigator.appName.indexOf("Microsoft") >= 0);if (!ie) { HTMLElement.prototype.__defineGetter__("innerText", function () { return (this.textContent); }); HTMLElement.prototype.__defineSetter__("innerText", function (txt) { this.textContent = txt; }); }
String.prototype.startsWith = function (str) { return (this.match("^" + str) == str) }
String.prototype.endsWith = function (str) { return (this.match(str + "$") == str) }
String.prototype.trim = function (str) { return (this.replace(/^\s*|\s*$/g, "")) }
function DocValue(id) { var obj = document.getElementById(id); if (obj) return obj.value; else return ""; }
function SelectText(id) {var obj = document.getElementById(id); if (obj.selectedIndex == -1) return ""; else return obj.options[obj.selectedIndex].text;delete (obj);}
function SelectIndex(id,index) { document.getElementById(id).selectedIndex=index; }
function SelectContains(id_ddl, value) { var ddl = document.getElementById(id_ddl), count = ddl.options.length; for (var j = 0; j < count; j++) { if (ddl.options[j].value == value) { return true; } } return false; }
function Visualizar(id,estado){ document.getElementById(id).style.visibility=estado; }
function AutoDisplay(id) { var obj = document.getElementById(id); if (obj.style.display == '') obj.style.display = 'none'; else obj.style.display = ''; }
function Display(id, estado) {try { document.getElementById(id).style.display = estado; } catch (Error) { }  }
function CountRows(id) { return document.getElementById(id).tBodies[0].rows.length; }
function CountBodyRows(oTBody) { return oTBody.rows.length; }
function Exists(id) { return document.getElementById(id); }
function SetDocValue(id, valor) { document.getElementById(id).value = valor; }
function SetDocInnerHTML(id,valor){document.getElementById(id).innerHTML=valor;}
function SetDocInnerText(id,valor){document.getElementById(id).innerText=valor;}
function SetClass(id, valor) { document.getElementById(id).className = valor; }
function Create_Element(id_container, type, id){ var master = document.getElementById(id_container); var container = document.createElement(type); container.id = id; master.appendChild(container);}
function Delete_Element(id_container, id) { if (document.getElementById(id)) document.getElementById(id_container).removeChild(document.getElementById(id)); }
function QuitarElemento(contenedor, id) { if (Exists(id)) { document.getElementById(id).innerHTML = ""; document.getElementById(contenedor).removeChild(document.getElementById(id)); } }
function ImgMouseOver(obj,image){obj.src='../img/' + image;}
function ImgMouseOut(obj,image){obj.src='../img/' + image;}
var tagScript='(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)';String.prototype.evalScript=function(){return(this.match(new RegExp(tagScript,'img'))||[]).evalScript();};String.prototype.stripScript=function(){return this.replace(new RegExp(tagScript,'img'),'');};String.prototype.extractScript=function(){var matchAll=new RegExp(tagScript,'img');return(this.match(matchAll)||[]);};Array.prototype.evalScript=function(extracted){var s=this.map(function(sr){var sc=(sr.match(new RegExp(tagScript,'im'))||['',''])[1];if(window.execScript){try{window.execScript(sc);}catch(err){}}
else{window.setTimeout(sc,0);}});return true;};Array.prototype.map=function(fun){if(typeof fun!=="function"){return false;} var i=0,l=this.length;for(i=0;i<l;i++){fun(this[i]);}return true;};
function SoloNumerico(e){ tecla = (document.all) ? e.keyCode : e.which; if (tecla==8) return true; patron = /^[0-9]$/; te = String.fromCharCode(tecla); return patron.test(te); }
function SoloDecimal(e,value) { tecla = (document.all) ? e.keyCode : e.which; if (tecla==8) return true; patron = /^[0-9]*\.?[0-9]*$/; te = String.fromCharCode(tecla); return patron.test(value + te); }
function SoloUrl(e){ tecla = (document.all) ? e.keyCode : e.which;  if (tecla==33 || tecla==34 || tecla==35 || tecla==36 || tecla==37 || tecla==38 || tecla==39 || tecla==40 || tecla==41 || tecla==42 || tecla==43 || tecla==47 || tecla==58 || tecla==61 || tecla==63 || tecla==64 || tecla==91 || tecla==92 || tecla==93 || tecla==95 || tecla==46 || tecla==123 || tecla==125) return false; else return true; }//!"#'$%&()*+/:=?@[\]_.{}String.prototype.trim = function (str) { return this.replace(/^\s*|\s*$/g,""); }
function getElementsByClassName(classname, node) { if (!node) node = document.getElementsByTagName("body")[0]; var a = [], re = new RegExp('\\b' + classname + '\\b'), els = node.getElementsByTagName("*"), count = els.length; for (var i = 0, j = count; i < j; i++) if (re.test(els[i].className)) a.push(els[i]); return a; }
function BtnClick(id) { try { if (ie) { Obj(id).click(); } else { Obj(id).onclick(); } } catch (error) {alert(error.message); } }
function BtnMouseDown(id) { Obj(id).onmousedown(); }
Obj=function(id){return document.getElementById(id);}
var notificacion;
function Mostrar_BarraInfo(exito) { if (exito) SetClass("Barra_info", "barra_info_exito"); else SetClass("Barra_info", "barra_info_error"); $("#Barra_info").animate({ height: "30px" }, 1000);  notificacion=setTimeout(function () { Ocultar_Barra_info(); }, 5000); }
function Ocultar_Barra_info() { $("#Barra_info").animate({ height: "0px" }, 1000, 'linear', function () { Display('Barra_info', 'none'); }); }
function Ver_PopUp(r){
  if (r != "") { var Mask = Obj("exposeMask");
      if(Mask){ if (Mask.style.display == "none") { SetDocInnerHTML("PopUp", r); $("#PopUp").overlay().load(); Mask.tabIndex = 0; Mask.style.width = document.body.scrollWidth + "px"; Obj("exposeMask").style.zIndex = 100; Obj("PopUp").style.zIndex = 101; }
          else { Mask.tabIndex += 1; var nuevo_Mask = Mask.cloneNode(false);
              nuevo_Mask.id = nuevo_Mask.id + "" + Mask.tabIndex; nuevo_Mask.style.zIndex = (parseInt(Mask.style.zIndex, 10) + 2 + (Mask.tabIndex * 2));
              Obj("master").appendChild(nuevo_Mask); nuevo_Mask.style.display = '';
              var nuevo_PopUp = Obj("PopUp").cloneNode(false);
              nuevo_PopUp.id = nuevo_PopUp.id + "" + Mask.tabIndex; nuevo_PopUp.style.zIndex = (parseInt(nuevo_Mask.style.zIndex,10) + 1);
              Obj("master").appendChild(nuevo_PopUp);
              nuevo_PopUp.innerHTML = r.replace(/@/g, Mask.tabIndex); nuevo_PopUp.style.left = ((screen.width - nuevo_PopUp.childNodes[0].childNodes[0].width) / 2) + "px";
              delete (nuevo_Mask); delete (nuevo_PopUp);  }
      } else{ SetDocInnerHTML("PopUp", r); $("#PopUp").overlay().load(); Mask = Obj("exposeMask"); Mask.tabIndex = 0; Mask.style.width = document.body.scrollWidth + "px"; Obj("exposeMask").style.zIndex = 100; Obj("PopUp").style.zIndex = 101; }
      r.evalScript();
  } else { Ver_Mensaje("Atención","Lo sentimos, ocurrió un error al presentar la información.<br/>Por favor, inténtelo nuevamente.<br/>Si el problema persiste comuníquese con nuestra Mesa de Ayuda."); }
}
function Cerrar_PopUp(id){ if (id == undefined || id == "PopUp@") { var objs = getElementsByClassName("overlay"), count = objs.length; if (objs[0].id != "PopUp") { if (count > 0) { if (count > 1) objs.sort(OrderByTabIndexDesc); Obj("exposeMask").tabindex -= 1; QuitarElemento("master", objs[0].id); QuitarElemento("master", objs[0].id.replace(/PopUp/, "exposeMask")); }else $("#PopUp").overlay().close(); }else $("#PopUp").overlay().close(); }else { Obj("exposeMask").tabindex -= 1; QuitarElemento("master", id); QuitarElemento("master", id.replace(/PopUp/, "exposeMask")); }}
function Ver_Mensaje(titulo,mensaje){ Ver_PopUp("<div class='panel'><table cellpadding='0' cellspacing='0' width='400' class='panel_borde'><tr class='panel_title'><td valign='middle' style='height:23px;width:100%;padding-left:10px;padding-right:5px;'><div style='float:left;'>&nbsp;" + titulo + "</div><div style='float:right;'><div class='cerrar' onmousedown='Cerrar_PopUp(\"PopUp@\");' style='float:right;'>&nbsp;</div></div></td></tr><tr><td class='panel_texto'>" + mensaje + "</td></tr></table></div>");}
function Cargar_Combo(ctrl,opc,id_obj,id_prm,div){ var prms = id_prm.split('|'), count = prms.length, valores = ""; for (var i = 0; i < count; i++) { valores += DocValue(prms[i]) + "|"; } if (valores.length > 0) valores = valores.substring(0, valores.length - 1); UpdatePanel('../../code/bc/bc_' + ctrl + '.php?opc=' + opc + '&prm=' + valores,div);}
function Buscar_Grilla(ctrl,opc,tabla,prm,div){ MostrarPaginaIntrepretada('../../code/bc/bc_' + ctrl + '.php?opc=' + opc + '&prm=' + (prm!=""?prm + (tabla!=""?'|':''):'') + Recupera_Datos(tabla),div);}
function Operacion(ctrl,opc,tabla,prm){ if (Valida_Datos(tabla) == 0) { MostrarPeticion('../../code/bc/bc_' + ctrl + '.php?opc=' + opc + '&prm=' + (prm!=""?prm + (tabla!=""?'|':''):'') + Recupera_Datos(tabla));}}
function Operacion_Reload(ctrl,opc,tabla,prm,div){ UpdatePanel('../../code/bc/bc_' + ctrl + '.php?opc=' + opc + '&prm=' + (prm!=""?prm + (tabla!=""?'|':''):'') + Recupera_Datos(tabla),div);}
function Operacion_Result(exito){ if (exito){ SetDocInnerText("td_Barra_info", "La operación se realizó satisfactoriamente");Cerrar_PopUp();} else SetDocInnerText("td_Barra_info", "La operación no se pudo realizar satisfactoriamente"); Mostrar_BarraInfo(exito);}
function PopUp(ctrl,opc,tabla,prm){ PopUpPeticion('../../code/bc/bc_' + ctrl + '.php?opc=' + opc + '&prm=' + (prm!=""?prm + (tabla!=""?'|':''):'') + Recupera_Datos(tabla));}
function Recupera_Datos(tabla) { if(tabla=="") return ""; var tagname = new Array('input', 'textarea', 'select'), id_filtros = [], doc = document.getElementById(tabla), b, count, _prm = ""; for (var i = 0; i < 3; i++) { b = doc.getElementsByTagName(tagname[i]); count = b.length; for (var j = 0; j < count; j++) { if (b[j].id.startsWith('fil_')) id_filtros.push(b[j]); } } id_filtros.sort(OrderByTabIndex); count = id_filtros.length; for (var i = 0; i < count; i++) _prm += Recupera_Datos_Item(id_filtros[i]); if (_prm.length > 0) return (_prm.substring(0, _prm.length - 1)); else return _prm;}
function Recupera_Datos_Item(id_filtro) {
    switch (id_filtro.type) {
        case "checkbox": if (id_filtro.id.indexOf('fil_gcol_') == 0) { return id_filtro.checked + '/'; }
            else if (id_filtro.id.indexOf('fil_grow_') == 0) { return id_filtro.checked + '|'; }
            else if (id_filtro.id.indexOf('fil_gcolchk_') == 0) { if (id_filtro.checked) return id_filtro.value + '/'; }
            else if (id_filtro.id.indexOf('fil_growchk_') == 0) { if (id_filtro.checked) return id_filtro.value + '|'; }
            else if (id_filtro.id.indexOf('fil_chk_') == 0) { if (id_filtro.checked) return id_filtro.value + '|'; }
            else { return id_filtro.checked + '¬'; }
        case "radio": if (id_filtro.checked) return RadioValue(id_filtro.getAttribute("name")) + '|'; else return "";
        case "select-multiple": return (SelectValue(id_filtro.id)).toString().replace(/,/g, "|") + '|';
        case "textarea": return id_filtro.value.replace(/\n/g, "<br>") + '|';
        default:
            if (id_filtro.id.indexOf('fil_gcol_') == 0) { return id_filtro.value + '/'; } //agrupa valores de una o más columnas con un solo valor
            else if (id_filtro.id.indexOf('fil_gcoldate_') == 0) { return id_filtro.value + '~'; } //agrupa valores de una o más columnas con un solo valor
            else if (id_filtro.id.indexOf('fil_grow_') == 0) { return id_filtro.value + '|'; } //agrupa valores de una o más columnas con un solo valor
            else if (id_filtro.id.indexOf('fil_multi_') == 0) { return Recuperar_Keys_ListBox(id_filtro.id).join("|") + '|'; } //recupera los valores de un listbox
            else { return id_filtro.value + '|'; }
    }
}
function Recuperar_Keys_ListBox(id) { var obj = document.getElementById(id), count = obj.options.length, datos = new Array(); for (var i = 0; i < count; i++) { datos.push([obj.options[i].value]); } return datos;}
function Recuperar_Datos_ListBox(id) { var obj = document.getElementById(id), count = obj.options.length, datos = new Array();for (var i = 0; i < count; i++) { datos.push([obj.options[i].value, obj.options[i].text]); } return datos;}
function OrderByTabIndex(a, b) { if (parseInt(a.getAttribute('posicion')) > parseInt(b.getAttribute('posicion'))) return 1; else if (parseInt(a.getAttribute('posicion')) < parseInt(b.getAttribute('posicion'))) return -1; else return 0; }
function OrderByTabIndexDesc(b, a) { if (parseInt(a.getAttribute('posicion')) > parseInt(b.getAttribute('posicion'))) return 1; else if (parseInt(a.getAttribute('posicion')) < parseInt(b.getAttribute('posicion'))) return -1; else return 0; }
function Valida_Datos(tabla) {
  if(tabla!=""){ var tagname = new Array('input', 'select'), id_filtros = [], b, count, validacion;
    for (var i = 0; i < 2; i++){ b = document.getElementById(tabla).getElementsByTagName(tagname[i]); count = b.length;
        for (var j = 0; j < count; j++){if (b[j].id.startsWith('val_fil_')){ id_filtros.push(b[j]);}}
    }id_filtros.sort(OrderByTabIndex);count = id_filtros.length;
    for (var i = 0; i < count; i++){ validacion = id_filtros[i].value.split('|');
        if (validacion[0].length > 0){ if (!eval(validacion[0])){Valida_Datos_Msj(validacion[1],tabla);return 1;}}
    } if (Exists("error_" + tabla)) Display("error_" + tabla, "none"); return 0;
  }return 0;
}
function Valida_Datos_Msj(msj, tabla, popup) {
    if (popup == "false" || popup==undefined) {
        if (!Exists("error_" + tabla)) {
            var tbl = document.getElementById(tabla).tBodies[0]; var newrow = document.createElement('tr'); var newcell = document.createElement('td');
            newcell.id = "error_" + tabla; newcell.className = "error"; newcell.setAttribute('colSpan', tbl.rows[0].cells.length); newcell.style.cssText = "padding-left:5px;padding-bottom:5px"; newrow.appendChild(newcell); tbl.appendChild(newrow);
        } SetDocInnerText("error_" + tabla, "Nota: " + msj); Display("error_" + tabla, "");
//        setTimeout("SetDocInnerText(\"error_" + tabla + "\",\"\")", 5000);
    } else{Ver_Mensaje(msj); }
}
function Mover_Paginacion(tbl_id, tr_id) {
  try{
     if (Obj(tr_id + "_pagant")) {
         var paginaactual = parseInt(DocValue(tbl_id + "_PagActual"), 10);
         var rango_up = paginaactual + 7, rango_down = paginaactual - 7, TheDoc = document;
         var id_filtros = [], b = document.getElementById("paginacion_" + tbl_id).getElementsByTagName('span'); count = b.length;
         for (j = 0; j < count; j++) { if (b[j].id.startsWith(tr_id + "_pag_")) id_filtros.push(b[j]); }
         var count = id_filtros.length;
         if (rango_up >= count) { document.getElementById(tr_id + "_pagant").style.display = 'none'; }
         else document.getElementById(tr_id + "_pagant").style.display = '';
         if (rango_down > 1) { document.getElementById(tr_id + "_pagprev").style.display = ''; }
         else document.getElementById(tr_id + "_pagprev").style.display = 'none';
         for (var i = 0; i < count; i++) {
             if ((i + 1) >= rango_down && (i + 1) <= rango_up) id_filtros[i].style.display = '';
             else id_filtros[i].style.display = 'none';
         }
     }
   }catch(err){alert(err.Message);}
}
function Paginacion(pag, tbl_id, tr_id, variable,cantfilas) {
  try{ var TheDoc = document;
    var paginaactual = 0, paganterior = DocValue(tbl_id + "_PagActual"), oTBody = TheDoc.getElementById(tbl_id).tBodies[0], filas_total = CountBodyRows(oTBody), filas_match = 0;
    for (var i = 0; i < filas_total; i++) { filas_match++; }
    var cantpags = parseFloat(filas_match) / cantfilas, hasta = 0, fila = 0;
    if (variable == 0) { if (pag > cantpags) pag = Math.ceil(cantpags); paginaactual = pag; }
    else paginaactual = parseInt(paganterior, 10) + variable;
    TheDoc.getElementById(tr_id + '_pag_' + paginaactual).className='link linkpaginacionactual';
    TheDoc.getElementById(tr_id + '_pag_' + paganterior).className='link linkpaginacion';
    var fin = (cantfilas * paginaactual), oTBodyRow;
    for (var i = 0; i < filas_total; i++){ oTBodyRow = oTBody.rows[fila];
        if (fila >= (fin - cantfilas) && fila < fin) { oTBodyRow.className = oTBodyRow.className.replace(/no_display/g, ""); hasta++; }
        else oTBodyRow.className += " no_display";
        fila++;
    }
    SetDocValue(tbl_id + "_PagActual", paginaactual);
    if (paginaactual != 1) { Visualizar(tbl_id + '_tab_first', 'visible'); Visualizar(tbl_id + '_tab_left', 'visible'); }
    else { Visualizar(tbl_id + '_tab_first', 'hidden'); Visualizar(tbl_id + '_tab_left', 'hidden'); }
    if (paginaactual >= (filas_match / cantfilas)) { Visualizar(tbl_id + '_tab_right', 'hidden'); Visualizar(tbl_id + '_tab_last', 'hidden'); }
    else { Visualizar(tbl_id + '_tab_right', 'visible'); Visualizar(tbl_id + '_tab_last', 'visible'); }
    var titulo = "";
    if (paginaactual == 1) titulo = ((paginaactual * cantfilas) - cantfilas + 1) + " - " + (hasta * paginaactual);
    else titulo = ((paginaactual * cantfilas) - cantfilas + 1) + " - " + (((paginaactual - 1) * cantfilas) + hasta);
    TheDoc.getElementById(tbl_id + '_Titulo_numeracion').innerText = titulo + " of " + filas_match;
    Mover_Paginacion(tbl_id, tr_id);
  }
  catch(err){alert(err.Message);}
}