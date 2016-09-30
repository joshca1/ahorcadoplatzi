// ActionScript Remote Document
ruta = "../";



function buscar(catalogo){
	f=document.form1;
	switch(catalogo){
		
		case 'cuenta':
			if(f.hcodcaj.value!=""){return;}
			window.open('covensol_scc_cat_ctasscg.php?obj=txtsc_cuenta','','toolbar=no,directories=no,location=no, width=900, height=300, scrollbars=yes, top=0, left=0, estatus=no')
		break;
		
		case 'beneficiario':
		//CAÑIZALES COMENTAR EL IF PARA Q PUEDA ABRIR LA BUSQUEDA	
           // if(f.hcodcaj.value!=""){return;}		
			window.open('covensol_scc_cat_beneficiarios.php','','toolbar=no,directories=no,location=no, width=900, height=300, scrollbars=yes, top=0, left=0, estatus=no')
		break;
		
		case 'doc':	
             //if(f.hcodcaj.value!=""){return;}		
			window.open('covensol_scc_cat_tipodocumentos.php?estpre=4&campo=txtcodtipdoc','','toolbar=no,directories=no,location=no, width=900, height=300, scrollbars=yes, top=0, left=0, estatus=no')
		break;
		
		case 'docrep':	
             //if(f.hcodcaj.value!=""){return;}		
			window.open('covensol_scc_cat_tipodocumentos.php?estpre=2&campo=txtcodtipdocrep','','toolbar=no,directories=no,location=no, width=900, height=300, scrollbars=yes, top=0, left=0, estatus=no')
		break;
		
		case 'moneda':	
            if(f.hcodcaj.value!=""){return;}		
			window.open('../soc/sigesp_soc_cat_moneda.php',"_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
		break;
		
		case 'caja':							
			window.open('covensol_scc_cat_cajachica.php','','toolbar=no,directories=no,location=no, width=900, height=300, scrollbars=yes, top=0, left=0, estatus=no')
		break;

	}
	
}

function convertir_monto(){
	if(document.getElementById("txtcodmon")!=null){		
		if(document.getElementById("txtcodmon").value==""){return;}		
		jmonto = covensol_formato_calculo(document.form1.txtmonto.value);		
		jtascam = document.form1.txttascamordcom.value;		
		tasacambio = covensol_formato_calculo(jtascam);		
		monnetext = covensol_redondear((jmonto)/tasacambio,2);
		document.getElementById("txtmonext").value = uf_convertir(monnetext);		
	}
}

function convertir_monto_bs(obj){
	if(document.getElementById("txtcodmon").value==""){document.form1.txtmonext.value=""; alert("Debe seleccionar una moneda"); return;}
	if(document.getElementById("txtcodmon")!=null){		
		if(document.getElementById("txtcodmon").value==""){return;}		
		jmonnetext = covensol_formato_calculo(document.form1.txtmonext.value);		
		jtascam = document.form1.txttascamordcom.value;		
		tasacambio = covensol_formato_calculo(jtascam);		
		jmonto = covensol_redondear(jmonnetext*tasacambio,2);
		document.getElementById("txtmonto").value = uf_convertir(jmonto);		
	}
}

function salirx(){
	window.close();
}

function valida_entrada(){//valida los campos de entrada de datos.
	
	f=document.form1;
	
	if(f.txtcodcaj.value == ""){		
		mensajes_sigesp("VALIDACIÓN DE DATOS","Introduzca el Código favor!");
		f.txtcodcaj.focus();
		return false;
	}
	
	if(f.txtdencaj.value == ""){		
		mensajes_sigesp("VALIDACIÓN DE DATOS","Introduzca la denominación !");		
		f.txtdencaj.focus();
		return false;
	}
	
	if(f.txtfeccaj.value == ""){		
		mensajes_sigesp("VALIDACIÓN DE DATOS","Introduzca la fecha !");		
		f.txtfeccaj.focus();
		return false;
	}
	
	if(f.txtced_bene.value == ""){		
		mensajes_sigesp("VALIDACIÓN DE DATOS","Seleccione la beneficiario !");		
		f.txtced_bene.focus();
		return false;
	}
	
	if(f.txtsc_cuenta.value == ""){		
		mensajes_sigesp("VALIDACIÓN DE DATOS","Seleccione la cuenta contable !");		
		f.txtsc_cuenta.focus();
		return false;
	}
	
	if(f.txtcodtipdoc.value == ""){		
		mensajes_sigesp("VALIDACIÓN DE DATOS","Seleccione el tipo de documento de APERTURA!");		
		f.txtcodtipdoc.focus();
		return false;
	}
	
	
	if(f.txtcodtipdocrep.value == ""){		
		mensajes_sigesp("VALIDACIÓN DE DATOS","Seleccione el tipo de documento de REPOSICIÓN!");		
		f.txtcodtipdocrep.focus();
		return false;
	}
	
	if(f.txtmonto.value == ""){		
		mensajes_sigesp("VALIDACIÓN DE DATOS","Debe introducir el monto de Apertura!");		
		f.txtmonto.focus();
		return false;
	}
	
	return true;
	

}

function funcion_respuesta(x){
		f=document.form1;
		switch(x){
			
			case 'insertado':
				f.hcodcaj.value = f.txtcodcaj.value;				
				//document.getElementById('boton_eliminar').style.visibility = 'visible';
				inicializar();
				alert('La Caja Chica fué guardada con éxito');
			break;
			
			case 'ir_nuevo':		        
			        alert(f.hfilas_afectadas.value);	
					nuevox();				
			break;
			
			case 'actualizado':					
					alert('La Caja Chica fué actualizado con éxito');	
			break;
			
		}
	
}



function envia_datos(criteriox){

				f=document.form1;
				
				datos = "criterio=" + criteriox +
				        "&codcaj=" + f.txtcodcaj.value + 
						"&dencaj=" + f.txtdencaj.value + 
						"&sc_cuenta=" + f.txtsc_cuenta.value + 
						"&ced_bene=" + f.txtced_bene.value + 
						"&codtipdoc=" + f.txtcodtipdoc.value + 
						"&conciliado=S"  + 
						"&codtipdocrep=" + f.txtcodtipdocrep.value + 
						"&monto=" + f.txtmonto.value + 
						"&feccaj=" + f.txtfeccaj.value + 
						"&codtipmov=APR" + 						
						"&codmon=" + f.txtcodmon.value + 	
						"&tascam=" + f.txttascamordcom.value;	
				//alert(datos);
				enviar_ajax(datos,'covensol_scc_d_cajachica_ajax.php','resultados','POST','',ruta);	

}

function resultado_guardar(res){
						
			if (res==true){				
				envia_datos('guardar');				
			}else{
				mensajes_sigesp("VALIDACIÓN DE DATOS","Operación Cancelada");
				return
			}			
					
}
function resultado_modificar(res){
						
			if (res==true){
				if(f.hcodcaj.value != ''){
					envia_datos('modificar');
				}
				else{
					
					mensajes_sigesp("VALIDACIÓN DE DATOS","<b>ERROR:</b><br>Falta el código para la operación de Update");
					return
				}
			}else{
				mensajes_sigesp("VALIDACIÓN DE DATOS","Operación Cancelada");
				return
			}			
					
}

function guarda_modifica(){
	f=document.form1;
	if(document.form1.hcodcaj.value == ""){
		
		if(!valida_entrada()){return;}
		
		if(f.incluir.value=='1'){
			confirmacion_sigesp("CONFIRMACIÓN DE DATOS",'Se va a ingresar una Caja Chica Nuevo  <br><br>¿Esta de acuerdo?',resultado_guardar);		
		}
		else{
			mensajes_sigesp("VALIDACIÓN DE DATOS","No tiene permisos para guardar!");
		}
		
	}else{
		
		if(!valida_entrada()){return;}
		
		if(f.cambiar.value=='1'){
			confirmacion_sigesp("CONFIRMACIÓN DE DATOS",'Se va a modificar una Caja Chica  <br><br>¿Esta de acuerdo?',resultado_modificar);			
		}
		else{
			mensajes_sigesp("VALIDACIÓN DE DATOS","No tiene permisos para modificar!");
		}
		
	}
	
}


function eliminar(res){
		f=document.form1;
		if(!f.hcodcaj.value){
				mensajes_sigesp("VALIDACIÓN DE DATOS","Cargue primero la Caja Chica !");
				return;	
		}		
		if(f.eliminar.value!='1'){
				mensajes_sigesp("VALIDACIÓN DE DATOS","No tiene permisos para eliminar.");
				return;	
		}
		if(f.hcodcaj.value != "" && res=='inicio'){			
			confirmacion_sigesp("CONFIRMACIÓN DE DATOS",'Se va a eliminar la Caja Chica  <br><br>¿Esta de acuerdo?',eliminar);
			return;
		}
		if (res==true){			
			envia_datos('eliminar');
			return;					
		}
		return;
}

function nuevox(){
		window.location='covensol_scc_d_cajachica.php';
}

function relocalizar_fecha(){	   
       DatePickerControl.init();
	   DatePickerControl.relocateButtons();
}

function limpiar_estado(){
	f=document.form1;
	f.txt_desest.value = '';
	f.txt_codest.value = '';
}

function inicializar(){	
	document.getElementById('boton_eliminar').style.visibility = 'hidden';
	f=document.form1;	
	if(f.hcodcaj.value!=""){
		document.getElementById('boton_eliminar').style.visibility = 'visible';
		//document.getElementById('txtcodsec').disabled = true;		
	}	
	procesar_mesajes_ajax();			
}


$(document).ready(function() {
   inicializar();
});



