</div>
</td>
</tr>
<tr>
 <td colspan="2">
 <table border="0" cellpadding="0" cellspacing="0" style="background-color:#972003;width:100%;height:80px;" class="textoPie">
  <tr><td style="padding:20 0 0 10px" align="left">Sistema de Seguimiento de Operaciones Textiles v1.0 beta</td></tr>
  <tr><td style="padding:0 0 20 10px" align="left"><?php echo date("d/m/Y"); ?></td></tr>
</table>
</td>
  </tr>
 </table>
</div>
</center>
<div id="PopUp" class="overlay"></div><div id="PopUp_Upload" class="overlay"></div><div id="loading" class="overlay" style='padding:150px'><img src="../../img/ajax-loader.gif" width="16px" height="16px"/> Cargando...</div>
 <script type="text/javascript">$("#loading").overlay({ mask: { color: '#444', loadSpeed: 0 }, speed: 0, closeOnClick: false });
   $("#PopUp").overlay({ mask: { color: '#444', closeSpeed: 0, loadSpeed: 0 }, speed: 0, fixed: false, oneInstance: false, closeOnEsc: true, closeOnClick: false }); $("#PopUp_Upload").overlay({ mask: { color: '#444', closeSpeed: 0, loadSpeed: 0 }, speed: 0, fixed: false, closeOnEsc: false, closeOnClick: false });
   function JSOnload() {
       var elm = document.createElement("script"); elm.src = "../../js/jscript_framework.js"; document.body.appendChild(elm);
   }
   if (window.addEventListener) window.addEventListener("load", JSOnload, false);
   else if (window.attachEvent) window.attachEvent("onload", JSOnload);
   else window.onload = JSOnload;
 </script>
</body>
</html>