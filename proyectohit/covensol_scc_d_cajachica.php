<?php
session_start();
$ruta = '../';
$fecha_actual = date("d/m/Y");
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "location.href='../sigesp_inicio_sesion.php'";
	print "</script>";		
}

require_once("clases/covensol_scc_c_cajachica.php");
$objscc=new covensol_scc_c_cajachica('');
$objscc->cargar_seguridad("SCC","covensol_scc_d_cajachica.php");
$datos=array();
$disable = "";

if($_GET['codcaj']){
	$param = $_GET;
	$resp = $objscc->VerificaPermisoCaja($_GET);	
	if($resp===true){
		$param['criterio']='por_codigo';
		$resp = $objscc->ConsultaCajaChica($param);
		if(!$resp['rs']->RecordCount()){$objscc->io_mensajes->message("No existe la caja chica: ".$_GET['codcaj']);}
		else{		
				 $datos = $resp['rs']->fields;
				 $datos = $objscc->FormatDatosCaja($datos);
				 if($datos['codcaj']){$disable = 'readonly="readonly"';}		
		}
	}
}


//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Sectores</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<script src="../base/librerias/js/jquery/jquery.js" type="text/javascript"></script>
<script src="../base/librerias/js/jquery/jquery.ui.draggable.js" type="text/javascript"></script>
<script src="../base/librerias/js/jquery/jquery.alerts.js" type="text/javascript"></script>
<link href="../base/librerias/js/jquery/jquery.alerts.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/librerias_comunes_covensol.js"></script>
<style type="text/css">
<!--


<!--

body {
	background-color: #DFE8F6;
}
-->
</style></head>
<script language="JavaScript" type="text/JavaScript" src="../shared/js/js_ajax.js"></script>
<script language="JavaScript" src="../shared/js/sigesp_js.js"></script>
<script language="JavaScript" src="../public/js/funcion_nomina.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/number_format.js"></script>
<script language="JavaScript" type="text/JavaScript" src="js/covensol_scc_d_cajachica.js"></script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
<body>
<?php 

?>
 <form name="form1" method="post" action="">
   <div align="center">
     <table width="914" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
       <tr>
         <td height="30" colspan="12" class="cd-logo"><img src="../shared/imagebank/header_scc.jpg" width="941" height="40"></td>
       </tr>
       <tr>
         <td height="20" colspan="12" bgcolor="#EAE8D9" ><table width="930" border="0" align="center" cellpadding="0" cellspacing="0">
                       <td width="432" height="20" class="descripcion_sistema">Definici&oacute;n de Caja Chica</td>
                       <td width="346" ><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
           <tr>
             <td height="20"  class="descripcion_sistema">&nbsp;</td>
             <td  class="texto-azul"><div align="right" class="letras-pequenas" ><b><?PHP print $_SESSION['nombre_completo'];?></b></div></td>
         </table></td>
       </tr>
       <tr>
         <td height="20" width="32" class="toolbar"><div align="center"><a href="javascript: nuevox();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo registro" width="20" height="20" border="0" title="Nuevo registro"></a></div></td>
         <td height="20" width="32" class="toolbar"><div align="center"><a href="javascript: guarda_modifica();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0" title="Guardar"></a></div></td>
         <td class="toolbar" width="32"><div align="center"><a href="javascript: buscar('caja');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Haga click para buscar y seleccionar la ventana" width="20" height="20" border="0" title="Haga click para buscar y seleccionar la ventana"></a></div></td>
         <td class="toolbar" width="32"><div align="center" id="boton_eliminar"><a href="javascript: eliminar('inicio');"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0" title="Eliminar"></a></div></td>
         <td class="toolbar" width="32"><div align="center"><a href="javascript: salirx();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
         <td class="toolbar" width="32"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20"></div></td>
         <td class="toolbar" width="32"><div align="center"></div></td>
         <td class="toolbar" width="32"><div align="center"></div></td>
         <td class="toolbar" width="32"><div align="center"></div></td>
         <td class="toolbar" width="32"><div align="center"></div></td>
         <td class="toolbar" width="32"><div align="center"></div></td>
         <td class="toolbar" width="560">&nbsp;</td>
       </tr>
     </table>
    	<div align="center" class="MarronTNR_12" id="Que_hacer">&nbsp;</div>
     <table width="931" border="0" cellpadding="0" cellspacing="0" class="contorno">
      
      <tr>
        <td width="929" align="center">
          
        <table width="931" border="0" align="center" cellpadding="0" cellspacing="3" class="sin-borde">
        <tr class="titulo-ventana">
          <td height="17" colspan="2" class="titulo-ventana"> Caja Chica</td>
        </tr>
        <tr>
          <td width="148" valign="top" class="letras-negrita"><div align="right">C&oacute;digo de Caja:</div></td>
          <td width="738"><div align="left">&nbsp;
            <input name="txtcodcaj" type="text" id="txtcodcaj" size="10" maxlength="6" value="<?php echo $datos['codcaj']; ?>" onkeypress="return cj_keyRestrict(event,'1234567890');"  onBlur="javascript: cerosizquierda(this,4)" />
            </div></td>
        </tr>
        <tr>
          <td valign="top" class="letras-negrita"><div align="right">Descripci&oacute;n:</div></td>
          <td><div align="left">&nbsp;
            <textarea name="txtdencaj" cols="70" rows="4" id="txtdencaj" onkeypress="return cj_keyRestrict(event,'abcdefghijklmn&ntilde;opqrstuvexyzABCDEFGHIJKLMN&Ntilde;OPQRSTUVWXYZ0123456789 &aacute;&eacute;&iacute;&oacute;&uacute;*()$%,&middot;!?&iquest;;:-_&ordf;&ordm;#');" ><?php echo $datos['dencaj']; ?></textarea>
            </div></td>
        </tr>
        <tr class="formato-blanco">
          <td ><div align="right"><strong>Fecha</strong>:</div></td>
          <td height="22" align="left" class="sin-borde"><input name="txtfeccaj" type="text" class="fecha_centro" id="txtfeccaj" value="<?php print $datos['feccaj'] ?>"  size="15" maxlength="10" readonly="readonly" />
            <input name="reset1" type="reset" onclick="return showCalendar('txtfeccaj', '%d/%m/%Y');" value=" ... " /></td>
        </tr>
        <tr class="formato-blanco">
          <td ><div align="right"><strong>Beneficiario</strong>:</div></td>
          <td height="22" align="left" class="sin-borde"><input value="<?php echo $datos['ced_bene']; ?>" name="txtced_bene" type="text" id="txtced_bene" size="20" readonly="yes"  />
            <a href="javascript: buscar('beneficiario');" id="buscacat"><img src="../shared/imagebank/tools20/buscar.gif" alt="Haga click para buscar" width="20" height="20" border="0" title="Haga click para buscar" /></a> &nbsp;&nbsp;&nbsp;<a href="javascript: limpiar('benef');" id="limpiar"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0" title="Eliminar" /></a><label>
              &nbsp;&nbsp;&nbsp;<input name="txtnombene" type="text" class="sin-borde" id="txtnombene"  value="<?php echo $datos['nombene'].' '.$datos['apebene']; ?>" size="70" readonly="readonly" />
            </label>            </td>
        </tr>
        <tr class="formato-blanco">
          <td ><div align="right"><strong>Cuenta Contable</strong>:</div></td>
          <td height="22" align="left"><input value="<?php echo $datos['sc_cuenta']; ?>" name="txtsc_cuenta" type="text" id="txtsc_cuenta" size="20" readonly="yes"  />
            <a href="javascript: buscar('cuenta');" id="buscacat"><img src="../shared/imagebank/tools20/buscar.gif" alt="Haga click para buscar" width="20" height="20" border="0" title="Haga click para buscar" /></a> &nbsp;&nbsp;&nbsp;<a href="javascript: limpiar('cuenta');" id="limpiar"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0" title="Eliminar" /></a>&nbsp;&nbsp;&nbsp;
            <input name="txtdenominacion" type="text" class="sin-borde" id="txtdenominacion" value="<?php echo $datos['denominacion']; ?>" size="70" readonly="readonly" />            </td>          
        </tr>
		<tr class="formato-blanco">
          <td ><div align="right"><strong>Tipo de Documento</strong>:</div></td>
          <td height="22" align="left"><input value="<?php echo $datos['codtipdoc']; ?>" name="txtcodtipdoc" type="text" id="txtcodtipdoc" size="20" readonly="yes"  />
            <a href="javascript: buscar('doc');" id="buscacat"><img src="../shared/imagebank/tools20/buscar.gif" alt="Haga click para buscar" width="20" height="20" border="0" title="Haga click para buscar" /></a> &nbsp;&nbsp;&nbsp;<a href="javascript: limpiar('doc');" id="limpiar"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0" title="Eliminar" /></a>&nbsp;&nbsp;&nbsp;
            <input name="txtdentipdoc" type="text" class="sin-borde" id="txtdentipdoc"  value="<?php echo $datos['dentipdoc']; ?>" size="70" readonly="readonly" />            </td>          
        </tr>
        <tr class="formato-blanco">
          <td ><div align="right"><strong>Tipo Doc. Reposici&oacute;n </strong>:</div></td>
          <td height="22" align="left"><input value="<?php echo $datos['codtipdocrep']; ?>" name="txtcodtipdocrep" type="text" id="txtcodtipdocrep" size="20" readonly="yes">
              <a href="javascript: buscar('docrep');" id="buscacat"><img src="../shared/imagebank/tools20/buscar.gif" alt="Haga click para buscar" width="20" height="20" border="0" title="Haga click para buscar" /></a> &nbsp;&nbsp;&nbsp;<a href="javascript: limpiar('docrep');" id="limpiar"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0" title="Eliminar" /></a>&nbsp;&nbsp;&nbsp;
              <input name="txtdentipdocrep" type="text" class="sin-borde" id="txtdentipdocrep"  value="<?php echo $datos['dentipdocrep']; ?>" size="70" readonly="readonly" />
          </td>
        </tr>
        <tr class="formato-blanco">
          <td ><div align="right"><strong>Moneda</strong>:</div></td>
          <td><input name="txtcodmon" type="text" id="txtcodmon" value="<?php  print $ls_codmon;  ?>" size="5" maxlength="3" readonly="readonly" />
            <a href="javascript: buscar('moneda');"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0" /></a>
            <input name="txtdenmon" type="text" class="sin-borde" id="txtdenmon" value="<?php  echo $datos['denmon'];  ?>" size="22" maxlength="50" readonly="readonly" /></td>
        </tr>
        <tr class="formato-blanco">
          <td ><div align="right"><strong>Tasa de Cambio</strong>:</div></td>
          <td><input name="txttascamordcom"  style="text-align:right" type="text" id="txttascamordcom" value="<?php  echo $datos['tascam']; ?>" size="15" maxlength="10" readonly="readonly" /></td>
        </tr>
        <tr class="formato-blanco">
          <td ><div align="right"><strong>Monto de Apertura</strong>:</div></td>
          <td><input name="txtmonto"  type="text" id="txtmonto" value="<?php echo $datos['monto']; ?>" size="25" maxlength="25" onkeypress="return(cj_formatonumero(this,'.',',',event)); " onkeyup="convertir_monto();"  style="text-align:right" <?php echo $disable; ?> /></td>
        </tr>
        <tr class="formato-blanco">
          <td ><div align="right"><strong>Monto (Mon. Extranjera)</strong>:</div></td>
          <td><input name="txtmonext"  type="text" id="txtmonext" style="text-align:right" value="<?php echo $datos['monext']; ?>" size="25" maxlength="25"onkeypress="return(cj_formatonumero(this,'.',',',event)); " onkeyup="convertir_monto_bs();"  /></td>
        </tr>
        </table></td>
      </tr>
     </table>
     <p><br /></p>
   </div>
	   
   <div id="resultados" align="center"></div>
    <input name="hcodcaj"  type="hidden" id="hcodcaj" value="<?php echo $datos['codcaj'];?>">       
    <input name="NOMBRE_FORM"  type="hidden" id="NOMBRE_FORM"  value="covensol_scc_d_cajachica">
    <?php 
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
		$objscc->imprimir_permisos($objscc->permisos,$objscc->la_permisos,$_SESSION["la_logusr"],"window.close()");
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	?>	  
    

 </form>
</body>
</html>



