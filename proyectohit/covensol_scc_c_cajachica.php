<?php

class covensol_scc_c_cajachica {

	function covensol_scc_c_cajachica($propiedades=array()){		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Function: Formulación
		// Access: public (covensol_scc_c_cajachica)
		// Description: Constructor de la Clase
		// Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 31/10/2012 								
		// Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ruta;
		if($ruta==''){$ruta="../";}		
		//require_once($ruta."shared/class_folder/sigesp_include.php");
		require_once($ruta."shared/class_folder/sigesp_include_covensol.php");
		$io_include=new sigesp_include_covensol();
		$io_conexion=$io_include->uf_conectar();
		//require_once($ruta."shared/class_folder/class_sql.php");
		require_once($ruta."shared/class_folder/class_sql_covensol.php");
		$this->io_sql=new class_sql_covensol($io_conexion);	
		require_once($ruta."shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();
		require_once($ruta."shared/class_folder/sigesp_conexiones.php");
		$this->io_conexiones=new conexiones();
		require_once($ruta."shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();		
		require_once($ruta."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();				
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		
		if($propiedades['habilitar_json_lib']){
			require_once($ruta.'shared/class_folder/JSON.php');
			$this->json = new JSON();
		}	
		$this->postgres_ilike = '';
		if($_SESSION["ls_gestor"] == 'POSTGRES'){$this->postgres_ilike = 'I';}
		
		$this->cfg_codsis = 'SCC';
		$this->cfg_seccion = 'SEGURIDAD';
		$this->cfg_type ='C';
		$this->nominas   = $this->obtCfg('NOMINAS_PERSONAL');
		
		require_once($ruta."shared/class_folder/class_fecha.php");		
		$this->io_fecha= new class_fecha();	
		require_once($ruta."shared/class_folder/class_sigesp_int.php");
		require_once($ruta."shared/class_folder/class_sigesp_int_int.php");
		require_once($ruta."shared/class_folder/class_sigesp_int_spg.php");
		require_once($ruta."shared/class_folder/class_sigesp_int_scg.php");
		require_once($ruta."shared/class_folder/class_sigesp_int_spi.php");
        $this->io_sigesp_int=new class_sigesp_int_int();
		$this->io_sigesp_int_spg=new class_sigesp_int_spg();
		$this->io_sigesp_int_scg=new class_sigesp_int_scg();
		$this->io_sigesp_int_spi=new class_sigesp_int_spi();	
		require_once($ruta."shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
		
		require_once($ruta."spg/sigesp_spg_c_comprobante.php");
		$this->classcmp=new sigesp_spg_c_comprobante();
		require_once($ruta."spg/class_folder/sigesp_spg_c_reprocesar_spg.php");
		$this->Saldos = new sigesp_spg_c_reprocesar_spg;

		$this->EsAjax = 0;
	}
	
	
   function cargar_seguridad($as_sistema,$as_ventanas)
   {
		$ls_empresa=$_SESSION["la_empresa"]["codemp"];
		$ls_logusr=$_SESSION["la_logusr"];
		$this->seguridad["empresa"]=$ls_empresa;
		$this->seguridad["logusr"]=$ls_logusr;
		$this->seguridad["sistema"]=$as_sistema;
		$this->seguridad["ventanas"]=$as_ventanas;
		$this->permisos="";
		$this->la_permisos = array();
		$this->la_permisos["leer"]="";
		$this->la_permisos["incluir"]="";
		$this->la_permisos["cambiar"]="";
		$this->la_permisos["eliminar"]="";
		$this->la_permisos["imprimir"]="";
		$this->la_permisos["anular"]="";
		$this->la_permisos["ejecutar"]="";
		if (array_key_exists("permisos",$_POST)||($ls_logusr=="PSEGIS"))
		{	
			if($ls_logusr=="PSEGIS")
			{
				$this->permisos="1";
				$this->la_permisos=$this->io_seguridad->uf_sss_load_permisossigesp();
			}
			else
			{
				$this->permisos=$_POST["permisos"];
				$this->la_permisos["leer"]=$_POST["leer"];
				$this->la_permisos["incluir"]=$_POST["incluir"];
				$this->la_permisos["cambiar"]=$_POST["cambiar"];
				$this->la_permisos["eliminar"]=$_POST["eliminar"];
				$this->la_permisos["imprimir"]=$_POST["imprimir"];
				$this->la_permisos["anular"]=$_POST["anular"];
				$this->la_permisos["ejecutar"]=$_POST["ejecutar"];
			}
		}
		else
		{
			$this->permisos=$this->io_seguridad->uf_sss_load_permisos($ls_empresa,$ls_logusr,$as_sistema,$as_ventanas,$this->la_permisos);
		}
		
   }// end function cargar_seguridad
   
   function guardar_seguridad($param=array()){
	   $resp = $this->io_seguridad->uf_sss_insert_eventos_ventana( $this->seguridad["empresa"],
																   $this->seguridad["sistema"],
																   $this->seguridad['evento'],
																   $this->seguridad["logusr"],
																   $this->seguridad["ventanas"],
	   															   $this->seguridad['descripcion']);
	   return true;
	   
   }
   
   function imprimir_permisos($as_permisos,$aa_permisos,$as_logusr,$as_accion)
   {
		if (($as_permisos)||($as_logusr=="PSEGIS"))
		{
			print("<input type=hidden name=permisos id=permisos value='$as_permisos'>");
			print("<input type=hidden name=leer id=leer value='$aa_permisos[leer]'>");
			print("<input type=hidden name=incluir id=incluir value='$aa_permisos[incluir]'>");
			print("<input type=hidden name=cambiar id=cambiar value='$aa_permisos[cambiar]'>");
			print("<input type=hidden name=eliminar id=eliminar value='$aa_permisos[eliminar]'>");
			print("<input type=hidden name=imprimir id=imprimir value='$aa_permisos[imprimir]'>");
			print("<input type=hidden name=anular id=anular value='$aa_permisos[anular]'>");
			print("<input type=hidden name=ejecutar id=ejecutar value='$aa_permisos[ejecutar]'>");
		}
		else
		{
			print("<script language=JavaScript>");
			print("".$as_accion."");
			print("</script>");
		}
   }// end function uf_print_permisos
   
   function obtCfg($param,$prop=array())
	{
		
		
		$prop['criterio'] = (!$prop['criterio'])?'param':$prop['criterio'];
		$this->cfg_type = $this->cfg_type?$this->cfg_type:'C';

  
		switch($prop['criterio']){					
				
				case 'param':					
					$ls_sql="SELECT value
							  FROM sigesp_config 
							 WHERE codemp='".$this->ls_codemp."' 
							   AND codsis='".$this->cfg_codsis."' 
							   AND seccion='".$this->cfg_seccion."' 
							   AND type='".$this->cfg_type."'
							   AND entry='".$param."' ";
					break;
		
		}
		
		
				
		$this->RsFopConf=$this->io_sql->select($ls_sql);
				
		if($this->RsFopConf===false)
		{
			$metodo = 'obtCfg';
			$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR->:</b><br>'.$this->io_sql->message;					
			$this->io_mensajes->message($mensaje);
			return;					
		}
			
		return $this->RsFopConf->fields["value"];
	}
	
	function VerificaPermisoCaja($datos=array())
	{
		
  				
		if(!$datos['codcaj'])
		{				
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Caja Inválida. Faltan el Código de Caja. 				           			    
						<br><b>METODO:</b> VerificaPermisoCaja ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;
		}
		
		$ls_sql="SELECT *
				  FROM sss_permisos_internos 
				 WHERE codemp='".$this->ls_codemp."' 
				   AND codsis='".$this->cfg_codsis."' 
				   AND codusu='".$_SESSION["la_logusr"]."' 
				   AND codintper='".$datos['codcaj']."'
				   AND enabled='1' ";
		
				
		$this->RsSegCaja=$this->io_sql->select($ls_sql);
				
		if($this->RsSegCaja===false)
		{
			$metodo = 'VerificaPermisoCaja';
			$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR->:</b><br>'.$this->io_sql->message;					
			$this->io_mensajes->message($mensaje);
			return;					
		}
		
		if(!$this->RsSegCaja->RecordCount())
		{
				$mensaje = '<b>SEGURIDAD DE DATOS:</b><br> El usuario no posee permisos para esta Caja. 				           			    
							<br><b>METODO:</b> VerificaPermisoCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);
				return false;
		}		
		
		return true;
	}
	
   function formato_numerico_us($numero){
	
			$busca = array(".", ",");
			$sustituye   = array("", ".");
			number_format(str_replace($busca,$sustituye,$numero),2,'.','');
			return number_format(str_replace($busca,$sustituye,$numero),2,'.','');
	
	}
	
   function FormatLonCodEstPro($datos=array()){
	            
				$ls_incio=25-$_SESSION["la_empresa"]['loncodestpro1'];
				$datos['codestpro1']=substr($datos['codestpro1'],$ls_incio,$_SESSION["la_empresa"]['loncodestpro1']);
				
				$ls_incio=25-$_SESSION["la_empresa"]['loncodestpro2'];
				$datos['codestpro2']=substr($datos['codestpro2'],$ls_incio,$_SESSION["la_empresa"]['loncodestpro2']);
				
				$ls_incio=25-$_SESSION["la_empresa"]['loncodestpro3'];
				$datos['codestpro3']=substr($datos['codestpro3'],$ls_incio,$_SESSION["la_empresa"]['loncodestpro3']);
				
				$ls_incio=25-$_SESSION["la_empresa"]['loncodestpro4'];
				$datos['codestpro4']=substr($datos['codestpro4'],$ls_incio,$_SESSION["la_empresa"]['loncodestpro4']);
				
				$ls_incio=25-$_SESSION["la_empresa"]['loncodestpro5'];
				$datos['codestpro5']=substr($datos['codestpro5'],$ls_incio,$_SESSION["la_empresa"]['loncodestpro5']);
				
				$this->CODESTPRO = $datos['codestpro1'].'-'.$datos['codestpro2'].'-'.$datos['codestpro3'];
				
				return $datos;
   }
   
   function ComboEstatusPry($opciones=array()){

				if(!$opciones['nombre_combo']){$nombre_combo = 'sel_estpry';}else{$nombre_combo = $opciones['nombre_combo'];}
				if(!$opciones['codestpry']){$carga = ' Seleccione '; $id_carga = '';}
				else{	
				    $opciones['criterio'] = 'por_codigo';
					$ofic = $this->ConsultaEstatusPry($opciones,'por_codigo');
					if($ofic===false){return false;}
					if(!$ofic['cantidad']){
						$mensaje = '<b>ERROR DE DATOS: </b> No existe el Código: '.$opciones['codestpry'].
						   		   '<br><b>METODO:</b> ComboEstatusPry ';					
						$this->io_conexiones->mensajes_ajax($mensaje);																	
						return false;	
					}
					$carga = $ofic['fila']['desestproy'];				  
					$id_carga = $opciones['codestpry'];
				}			
				
				$opciones['criterio'] = 'por_listado';							
				$resp = $this->ConsultaEstatusPry($opciones,'por_listado');
				if($resp===false){return false;}
				
				$combo = '<select name="'.$nombre_combo.'" id="'.$nombre_combo.'" onChange="'.$opciones['funcion_js'].'">
				              <option value="'.$id_carga.'">- '.$carga.' -</option>';
				
				foreach($resp['rs'] as $dato) { 				
					$combo .= '<option value="'.$dato["codestpry"].'">'.$dato["desestproy"].'</option>';								
				}
				$combo .= '</select>';
																							
				return $combo;
	}
   
   function ConsultaConceptos($param=array()){
	
			$campos = " * ";
			$criteriosql='';
			$criterio="";
			$param['criterio'] = $param['criterio']?$param['criterio']:'por_listado';
						
			switch($param['criterio']){
								
				case "por_listado":	
				
						$campos = " *  ";													
					    $sql_criterio = "  WHERE c.codconcaj ".$this->postgres_ilike."LIKE('%".$param['codconcaj']."%') 
						                     AND c.denconcaj ".$this->postgres_ilike."LIKE('%".$param['denconcaj']."%') ".$criterio;
					    break;
				
			    case "por_codigo":				        
						$campos = " * , (SELECT max(denominacion) FROM spg_cuentas spg  WHERE spg.spg_cuenta = c.spg_cuenta) AS denominacion ";		
					    $sql_criterio = "  WHERE c.codconcaj ='".$param['codconcaj']."' ";
					    break;
				
				
			}
								   
			$query_rs = "SELECT ".$campos." FROM scc_conceptos c ".$sql_criterio ." ORDER BY c.codconcaj";			
			
			//echo $query_rs.'<br>';
			$clase = get_class($this);
			$metodo = 'ConsultaConceptos';
			$param['arreglo'] = 'arreglo';
			$param['ajax'] = '0';
			$param['imprimir'] = '1';	
			$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;	
			$respuesta = $this->io_conexiones->conexion($query_rs,$param,$msj);	
			return $respuesta;
	
	}
   
   function  InsertarConcepto($datos=array()){
				
				if(!$datos['codconcaj'] or !$datos['denconcaj'] or !$datos['spg_cuenta'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de insert. 				           			    
							    <br><b>METODO:</b> InsertarConcepto ';
					$this->io_conexiones->mensajes_ajax($mensaje);																	
					return false;
				}
				 
	
				$param = $datos;
				$param['criterio'] = 'por_codigo';
				$resp = $this->ConsultaConceptos($param);
				if($resp===false){return false;}
				if($resp['rs']->RecordCount()){
				        $mensaje = "<b>ERROR:</b> El código de concepto ya existe !";
						$this->io_conexiones->mensajes_ajax($mensaje);
						return false;
				}
				
				
				$ls_sql = "INSERT INTO scc_conceptos( codconcaj, denconcaj, spg_cuenta)
						   VALUES (  '".$datos['codconcaj']."', ".
									"'".$datos['denconcaj']."', ".
									"'".$datos['spg_cuenta']."' ".
									" ); ";
									  		        
				
				$rs_data=$this->io_sql->select($ls_sql);	
				if($rs_data==false){				
					$metodo = 'InsertarConcepto';
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;				
				}
						
				$this->seguridad["evento"]="INSERT";
				$this->seguridad["descripcion"]="Se creo el Concepto ".$datos['codconcaj']." Descripción: ".$datos['denconcaj'];
				$this->guardar_seguridad();
				
				return true;
	
	}
   
   function ModificarConcepto($datos=array()){
		
		   $metodo = 'ModificarConcepto';
		   
		   if(!$datos['codconcaj'] or !$datos['denconcaj'] or !$datos['spg_cuenta'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Actualización. 				           			    
							<br><b>METODO:</b> '.$metodo;
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}			
			
		   $ls_sql = "  UPDATE scc_conceptos
						 SET denconcaj='".$datos['denconcaj']."',
						     spg_cuenta='".$datos['spg_cuenta']."'						  
					   WHERE codconcaj ='".$datos['codconcaj']."'";
		
						
			$rs_data=$this->io_sql->select($ls_sql);			
			//echo $ls_sql;
			if($rs_data==false)
			{				
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;
				
			}
			
			if($this->io_sql->conn->Affected_Rows()<1){			       
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
			}				
			
			$this->seguridad["evento"]="UPDATE";
			$this->seguridad["descripcion"]="Se Modifico el Concepto de Caja Chica ".$datos['codconcaj']." Descripción: ".$datos['denconcaj'];
			$this->guardar_seguridad();
				
			return true;
	
	}
	
	function  EliminarConcepto($datos=array()){
				
				$metodo = 'EliminarConcepto';
				
				if(!$datos['codconcaj'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Eliminación. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}
				
				
				
				$datos['criterio'] = 'por_concepto';
				$resp = $this->ConsultaMovCajaChica($datos);
				if($resp===false){return false;}
				if($resp['rs']->RecordCount())
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> El concepto posee movimientos. NO se puede eliminar. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}
				
				
				
				$ls_sql = "DELETE FROM scc_conceptos
				           WHERE codconcaj  = '".$datos['codconcaj']."'";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;					
				}
				
				if($this->io_sql->conn->Affected_Rows()<1){
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
				}		
				
				$this->seguridad["evento"]="DELETE";
				$this->seguridad["descripcion"]="Se Eliminó el Concepto ".$datos['codconcaj']." Descripción: ".$datos['denconcaj'];
				$this->guardar_seguridad();
				
				$mensaje = 'Se eliminaron '.$this->io_sql->conn->Affected_Rows().' Conceptos(s)';			
				$this->io_conexiones->dato_js('hfilas_afectadas',$mensaje);
				return true;
	
	}
   
   function  FormatDatosCaja($datos=array()){
		    
			$datos = $this->FormatLonCodEstPro($datos);
			if($datos['codmon']){
				$datos['monext'] = $datos['monto']*$datos['tascam'];
				$datos['monext'] = number_format($datos['monext'],2,',','.');
			}
			$datos['monto'] = number_format($datos['monto'],2,',','.');
			$datos['feccaj'] = $this->io_conexiones->formatea_fecha_normal($datos['feccaj']);	
			
			return $datos;
	}
   
   function ConsultaCajaChica($param=array()){
	
			$campos = " * ";
			$criteriosql='';
			$criterio="";
			$param['criterio'] = $param['criterio']?$param['criterio']:'por_listado';
						
			switch($param['criterio']){
								
				case "por_listado":	
				
						$campos = " cc.*,b.nombene,b.apebene,scg.denominacion,scg.status,td.dentipdoc,tdr.dentipdoc AS dentipdocrep,m.denmon  ";													
					    $sql_criterio = "  LEFT JOIN rpc_beneficiario b ON b.codemp = cc.codemp 
						                                                AND  b.ced_bene = cc.ced_bene
										   LEFT JOIN scg_cuentas scg ON scg.codemp = cc.codemp 
						                                            AND scg.sc_cuenta = cc.sc_cuenta
										   LEFT JOIN cxp_documento td ON td.codtipdoc = cc.codtipdoc 
										   LEFT JOIN cxp_documento tdr ON tdr.codtipdoc = cc.codtipdocrep 
										   LEFT JOIN sigesp_moneda m ON m.codmon = cc.codmon
										   INNER JOIN sss_permisos_internos sss ON sss.codemp = cc.codemp 
																				AND sss.codintper = cc.codcaj
																				AND sss.codsis = 'SCC'
																				AND sss.codusu = '".$_SESSION["la_logusr"]."'
																				AND sss.enabled = '1'	
						                   WHERE cc.codemp ='".$this->ls_codemp."'  
						                     AND cc.codcaj ".$this->postgres_ilike."LIKE('%".$param['codcaj']."%') 
						                     AND cc.dencaj ".$this->postgres_ilike."LIKE('%".$param['dencaj']."%') ".$criterio;
					    break;
				
			    case "por_codigo":		 		        
						$campos = " cc.*,b.nombene,b.apebene,scg.denominacion,scg.status,td.dentipdoc,tdr.dentipdoc AS dentipdocrep,m.denmon  ";		
					    $sql_criterio = " LEFT JOIN rpc_beneficiario b ON b.codemp = cc.codemp 
						                                                AND  b.ced_bene = cc.ced_bene
										  LEFT JOIN scg_cuentas scg ON scg.codemp = cc.codemp 
						                                           AND scg.sc_cuenta = cc.sc_cuenta
										  LEFT JOIN cxp_documento td ON td.codtipdoc = cc.codtipdoc 
										  LEFT JOIN cxp_documento tdr ON tdr.codtipdoc = cc.codtipdocrep 
										  LEFT JOIN sigesp_moneda m ON m.codmon = cc.codmon										  
										  WHERE cc.codemp ='".$this->ls_codemp."'  
						                    AND cc.codcaj ='".$param['codcaj']."'";
					    break;
				
			}
								   
			$query_rs = "SELECT ".$campos." FROM scc_cajachica cc ".$sql_criterio ."  ";			
			
			//echo $query_rs.'<br>';
			$clase = get_class($this);
			$metodo = 'ConsultaCajaChica';
			$param['arreglo'] = 'arreglo';
			$param['ajax'] = '0';
			$param['imprimir'] = '1';	
			$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;	
			$respuesta = $this->io_conexiones->conexion($query_rs,$param,$msj);	
			return $respuesta;
	
	}
	
	function  InsertarCajaChica($datos=array()){
				
				if(!$datos['codcaj'] or !$datos['dencaj'] or !$datos['sc_cuenta'] or !$datos['ced_bene'] or !$datos['codtipdoc'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de insert. 				           			    
							    <br><b>METODO:</b> InsertarCajaChica ';
					$this->io_conexiones->mensajes_ajax($mensaje);																	
					return false;
				}
				
				if(!$datos['monto'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Debe existir un monto de apertura. 
					            <br> El monto de Apertura no puede ser 0 o nulo. 				           			    
							    <br><b>METODO:</b> InsertarCajaChica ';
					$this->io_conexiones->mensajes_ajax($mensaje);																	
					return false;
				}
						
				$param = $datos;
				$param['criterio'] = 'por_codigo';
				$resp = $this->ConsultaCajaChica($param);
				if($resp===false){return false;}
				if($resp['rs']->RecordCount()){
				        $mensaje = "<b>ERROR:</b> El código de la caja chica ya existe !";
						$this->io_conexiones->mensajes_ajax($mensaje);
						return false;
				}
				
				$datos['tascam'] = $datos['tascam']?$datos['tascam']:1;
				$datos['monto'] = $datos['monto']?$datos['monto']:0;
				
				$ls_sql = "INSERT INTO scc_cajachica( codemp, codcaj, dencaj, sc_cuenta, ced_bene, 
													  codtipdoc, monto, feccaj, codmon,tascam,codtipdocrep)
						   VALUES (  '".$this->ls_codemp."', ".
								    "'".$datos['codcaj']."', ".
									"'".$datos['dencaj']."', ".
									"'".$datos['sc_cuenta']."', ".
									"'".$datos['ced_bene']."', ".
									"'".$datos['codtipdoc']."', ".
									"'".$datos['monto']."', ".
									"'".$datos['feccaj']."', ".
									"'".$datos['codmon']."', ".
									"'".$datos['tascam']."', ".
									"'".$datos['codtipdocrep']."' ".
									" ); ";
									  		        
				
				$rs_data=$this->io_sql->select($ls_sql);	
				if($rs_data==false){				
					$metodo = 'InsertarCajaChica';
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;				
				}				
				
				$datos['fecha'] = $datos['feccaj'];
				$resp=$this->InsertarMovCajaChica($datos);
				if($resp===false){return false;} 
				
				$datos['numrecdoc'] = "SCC-APR0000".$datos['codcaj'];
				$datos['procede'] = "SCCAPR";
				$datos['dencondoc'] = "APERTURA DE CAJA CHICA: ".$datos['dencaj'];
				$this->DatosRD = $datos;
				$resp=$this->procesar_recepcion_documento_apr($datos);
				if($resp===false){return false;} 
				
				
				$this->seguridad["evento"]="INSERT";
				$this->seguridad["descripcion"]="Se creo la caja chica ".$datos['codcaj']." Descripción: ".$datos['dencaj'];
				$this->guardar_seguridad();
				
				return true;
	
	}
	
	function procesar_recepcion_documento_apr($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: procesar_recepcion_documento_apr
		//         Access: public  
		//      Argumento: $datos
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de generar la recepción de documentos 
		//	   Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 18/01/2013							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
		$resp=$this->scc_validar_recepcion_documentos($datos);
		if($resp===false){return false;} 
		if($resp->RecordCount()){$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> Ya existe una Recepción de Documento para esta Caja Chica !");}

		$resp=$this->insert_recepcion_documento($datos);
		if($resp===false){return false;} 
				
		$datos["debhab"] = 'D';		
		$resp=$this->insert_rd_detalle_contable($datos);
		if($resp===false){return false;}
		
		$datos["debhab"] = 'H';
		$datos["sc_cuenta"] = $this->cuenta_beneficiario($datos);
		if($datos["sc_cuenta"]===false){return false;}
		$resp=$this->insert_rd_detalle_contable($datos);
		if($resp===false){return false;}
	
		$this->seguridad["evento"]="INSERT";
		$this->seguridad["descripcion"]="Generó la Recepción de Documento de Caja Chica <b>".$datos['codcaj']."</b>, ".
						                "Comprobante <b>".$datos['numrecdoc']."</b>";
		$this->guardar_seguridad();
		
		return true;
	
	}
	
	function procesar_recepcion_documento_rep($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: procesar_recepcion_documento_rep
		//         Access: public  
		//      Argumento: $datos
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de generar la recepción de documentos compromete causa de reposición 
		//	   Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 18/01/2013							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		if(!$datos['codcaj'] or !$this->codrep or !$this->corelcaj)
		{				
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder generar la recepción de documento de reposición. 				           			    
						<br><b>METODO:</b> procesar_recepcion_documento_rep ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;
		}
		
		$datos['numrecdoc'] = "SCC-R".$this->codrep;
		$datos['procede'] = "SCCREP";
		$datos['dencondoc'] = "REPOSICIÓN DE CAJA CHICA: ".$this->DatosCaja['dencaj']." - CÓDIGO DE CAJA: ".$this->DatosCaja['codcaj']." - NÚMERO DE REPOSICIÓN: ".(integer)$this->corelcaj;
		$datos['codtipdoc'] = $this->DatosCaja['codtipdocrep'];
		$datos['ced_bene'] = $this->DatosCaja['ced_bene'];		 
		$this->DatosRD = $datos;
		
		
		$resp=$this->scc_validar_recepcion_documentos($datos);
		if($resp===false){return false;} 
		if($resp->RecordCount()){$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> Ya existe la Recepción de Documento de reposición para esta Caja Chica !");}
		//INSERTA LA RECEPCIÓN DE DOCUMENTO (CABECERA)
		$resp=$this->insert_recepcion_documento($datos);
		if($resp===false){return false;} 
		
		//INSERTA EL DETALLE PRESUPUESTARIO DE LA RECEPCIÓN DE DOCUMENTO		
		$spg=$this->BuscarDtSpgRep($datos);
		if($spg===false){return false;}
		foreach($spg as $DatosSPG){			    	
			$resp=$this->insert_rd_detalle_gasto($DatosSPG);
			if($resp===false){return false;}
		
		}
		
		//INSERTA EL DETALLE CONTABLE DE LA RECEPCIÓN DE DOCUMENTO POR EL DEBE	
		$scg=$this->BuscarDtScgRep($datos);
		if($scg===false){return false;}
		foreach($scg as $DatosSCG){		
			$DatosSCG["debhab"] = 'D';		
			$resp=$this->insert_rd_detalle_contable($DatosSCG);
			if($resp===false){return false;}			
		}
		//INSERTA EL DETALLE CONTABLE DE LA RECEPCIÓN DE DOCUMENTO POR EL HABER	
		$datos["debhab"] = 'H';
		$datos["sc_cuenta"] = $this->cuenta_beneficiario($datos);
		if($datos["sc_cuenta"]===false){return false;}
		$resp = $this->VerificaCuentaSCG($datos);
		if($resp===false){return false;}
		$resp=$this->insert_rd_detalle_contable($datos);
		if($resp===false){return false;}
		
		$this->seguridad["evento"]="INSERT";
		$this->seguridad["descripcion"]="Generó la Recepción de Documento de Reposición de Caja Chica <b>".$datos['codcaj']."</b>, ".
						                "Comprobante <b>".$datos['numrecdoc']."</b>";
		$this->guardar_seguridad();
		
		return true;
	
	}
	
	
	function BuscaSpgCierre($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación				           			    
							<br><b>METODO:</b> BuscaSpgCierre ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			$ls_sql =   "   SELECT denconcaj,c.spg_cuenta,mc.coduniadm,
								   mc.estcla,mc.codestpro1,mc.codestpro2,mc.codestpro3,mc.codestpro4,mc.codestpro5,
								   mc.codfuefin,sum(mc.monto) as monto
							FROM scc_mov_caja mc						
							INNER JOIN scc_conceptos c ON mc.codconcaj = c.codconcaj 
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov
							WHERE mc.codemp = '".$this->ls_codemp."'
							AND mc.codcaj = '".$datos['codcaj']."'
							AND mc.codtipmov = 'MOV'
							AND estrepo = '0'
							GROUP BY mc.codestpro1,mc.codestpro2,mc.codestpro3,
							         mc.codestpro4,mc.codestpro5,mc.estcla,
									 c.spg_cuenta,denconcaj,mc.coduniadm, 
									 mc.codfuefin
			            ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'BuscaSpgCierre';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			
			return $rs_data;
	}
	
	function BuscaScgCierre($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación			           			    
							<br><b>METODO:</b> BuscaSpgCierre ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
									
			$ls_sql =   "   SELECT spg.sc_cuenta, sum(mc.monto) AS monto,denconcaj
							FROM scc_mov_caja mc						
							INNER JOIN scc_conceptos c ON mc.codconcaj = c.codconcaj 
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov
							LEFT JOIN spg_cuentas spg ON spg.spg_cuenta = c.spg_cuenta
													  AND spg.codestpro1 = mc.codestpro1
													  AND spg.codestpro2 = mc.codestpro2
													  AND spg.codestpro3 = mc.codestpro3
													  AND spg.codestpro4 = mc.codestpro4
													  AND spg.codestpro5 = mc.codestpro5
													  AND spg.estcla = mc.estcla
							LEFT JOIN scg_cuentas scg ON scg.sc_cuenta = spg.sc_cuenta     
							WHERE mc.codemp = '".$this->ls_codemp."'
							AND mc.codcaj = '".$datos['codcaj']."'
							AND mc.codtipmov = 'MOV'
							AND estrepo = '0'
							GROUP BY spg.sc_cuenta,denconcaj
							ORDER BY spg.sc_cuenta	
			            ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'BuscaScgCierre';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			return $rs_data;
	}
	
	function ProcesarCmpCierre($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: ProcesarCmpCierre
		//         Access: public  
		//      Argumento: $datos
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de generar los comprobantes de Cierre presupuestario y Contable
		//	   Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 27/02/2013							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		if(!$datos['codcaj'])
		{				
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder generar la recepción de documento de reposición. 				           			    
						<br><b>METODO:</b> ProcesarCmpCierre ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;
		}		
		
		if(!$this->MontoRep){return true;}
		
		$ls_codemp=$this->ls_codemp;
		$ls_operacion="CCP";
		$ls_comprobante="SCC-CIERRE0".$datos['codcaj'];		
		$_SESSION["fechacomprobante"]=$this->io_conexiones->formatea_fecha_normal($datos['fecha']);
		$ld_fecha = $_SESSION["fechacomprobante"];
		$ls_procedencia="SCCCIE";
		$ls_descripcion="CIERRE DE CAJA CHICA: ".$this->DatosCaja['dencaj']." - CÓDIGO DE CAJA: ".$this->DatosCaja['codcaj'];
		$ls_tipo="B";
		$this->classcmp->io_int_int->is_tipo=$ls_tipo;
		$this->classcmp->io_int_int->is_cod_prov="----------";
		$this->classcmp->io_int_int->is_ced_ben=$this->DatosCaja['ced_bene'];		
		$this->classcmp->io_int_int->ib_procesando_cmp=false;
		$this->classcmp->io_int_int->id_fecha=$datos['fecha'];
		$ls_codban     = "---";
		$ls_ctaban     = "-------------------------";
		$ls_rendfon    =0;
		$ls_fuentefin="--";
		
		$resp=$this->io_fecha->uf_valida_fecha_periodo($_SESSION["fechacomprobante"],$this->ls_codemp);
		if($resp===false){				
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> '.$this->is_msg_error. 				           			    
						'<br><b>METODO:</b> ProcesarCmpCierre ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;
		}
		
		
	    $this->classcmp->io_int_spg->io_sql->begin_transaction();	
		$existe=$this->classcmp->uf_verificar_comprobante($ls_codemp,$ls_procede,$ls_comprobante);
		if($existe){				
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> El comprobante '.$ls_comprobante.' de contabilización ya existe 				           			    
						<br><b>METODO:</b> ProcesarCmpCierre ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;
		}
		//INSERTA EL COMPROBANTE (CABECERA)
		$resp=$this->classcmp->uf_guardar_automatico($ls_comprobante,$ld_fecha,$ls_procedencia,$ls_descripcion,
												     $this->classcmp->io_int_int->is_cod_prov,
												     $this->classcmp->io_int_int->is_ced_ben,$ls_tipo,1,
												     $ls_codban,$ls_ctaban, $ls_rendfon, $ls_fuentefin, 'N');
		
		if($resp===false){$this->classcmp->io_int_spg->io_sql->rollback(); return false;}
			
		$arr_cmp["comprobante"]=$ls_comprobante;
		$ls_documento=$ls_comprobante;
		$ld_fecdb=$datos['fecha'];
		$arr_cmp["fecha"]      	 = $ld_fecdb;
		$arr_cmp["procedencia"]	 = $ls_procedencia;
		$arr_cmp["descripcion"]	 = $ls_descripcion;
		$arr_cmp["proveedor"]  	 = $this->classcmp->io_int_int->is_cod_prov;
		$arr_cmp["beneficiario"] = $this->classcmp->io_int_int->is_ced_ben;
		$arr_cmp["tipo"]         = $ls_tipo;
		$arr_cmp["codemp"]       = $ls_codemp;
		$arr_cmp["tipo_comp"]    = 1;			
		
		
		//INSERTA EL DETALLE PRESUPUESTARIO DEL COMPROBANTE	
		$spg = $this->BuscaSpgCierre($datos);
		if($spg===false){return false;}
		if(!$spg->RecordCount()){return true;}
		
		foreach($spg as $DatosSPG){			    	
			
			$ld_disponible=0;			
			$resp=$this->classcmp->uf_guardar_movimientos($arr_cmp,$DatosSPG['codestpro1'],$DatosSPG['codestpro2'],
													   $DatosSPG['codestpro3'],$DatosSPG['codestpro4'],$DatosSPG['codestpro5'],
													   $DatosSPG['spg_cuenta'],$ls_procede,$DatosSPG['denconcaj'],$ls_documento,
													   $ls_operacion,0,$DatosSPG['monto'],"C",$ls_codban,$ls_ctaban,
													   $DatosSPG['estcla']);
			
			if($resp===false){$this->classcmp->io_int_spg->io_sql->rollback(); return false;}
			$this->Saldos->AjaxConect = 1;
			$resp=$this->Saldos->ReprocesarSaldosCtas($DatosSPG);
			if($resp===false){$this->classcmp->io_int_spg->io_sql->rollback(); return false;}
			
		}//FIN FOREACH
		
		
		
		
		//INSERTA EL DETALLE CONTABLE DE LA RECEPCIÓN DE DOCUMENTO POR EL DEBE	
		$scg=$this->BuscaScgCierre($datos);
		
		if($scg===false){return false;}	
		foreach($scg as $DatosSCG){		
			$DatosSCG["debhab"] = 'D';
			$resp = $this->VerificaCuentaSCG($DatosSCG);
			if($resp===false){$this->classcmp->io_int_spg->io_sql->rollback(); return false;}		
			$resp=$this->classcmp->uf_guardar_movimientos_contable($arr_cmp,$DatosSCG['sc_cuenta'],$ls_procedencia,$DatosSCG['denconcaj'],
																$ls_documento,$DatosSCG["debhab"],$DatosSCG["monto"],
																$ls_codban,$ls_ctaban,$ld_fecdb);
			if($resp===false){$this->classcmp->io_int_spg->io_sql->rollback(); return false;}			
		}
		
		//INSERTA EL DETALLE CONTABLE DE LA RECEPCIÓN DE DOCUMENTO POR EL HABER	
		$datos["debhab"] = 'H';
		$datos["sc_cuenta"] = $this->DatosCaja['sc_cuenta'];		
		$resp = $this->VerificaCuentaSCG($datos);
		if($resp===false){return false;}
		$resp=$this->classcmp->uf_guardar_movimientos_contable($arr_cmp,$datos['sc_cuenta'],$ls_procedencia,$this->DatosCaja['denominacion'],
															$ls_documento,$datos["debhab"],$this->MontoRep,
															$ls_codban,$ls_ctaban,$ld_fecdb);
		if($resp===false){$this->classcmp->io_int_spg->io_sql->rollback(); return false;}
		$this->classcmp->io_int_spg->io_sql->commit(); 
		
		
		$this->seguridad["evento"]="INSERT";
		$this->seguridad["descripcion"]="Generó el comprobante de cierre de Caja Chica <b>".$datos['codcaj']."</b>, ".
						                "Comprobante <b>".$ls_comprobante."</b>";
		$this->guardar_seguridad();
		
		return true;
	
	}
	
	function EliminarCmpCierre($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: EliminarCmpCierre
		//         Access: public  
		//      Argumento: $datos
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de generar los comprobantes de Cierre presupuestario y Contable
		//	   Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 04/03/2013							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		if(!$datos['codcaj'] or !$datos['fecha'])
		{				
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder generar la recepción de documento de reposición. 				           			    
						<br><b>METODO:</b> ProcesarCmpCierre ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;
		}
		
		$ls_codemp=$this->ls_codemp;
		$ls_operacion="CCP";
		$ls_comprobante="SCC-CIERRE0".$datos['codcaj'];		
		$_SESSION["fechacomprobante"]=$this->io_conexiones->formatea_fecha_normal($datos['fecha']);
		$ld_fecha = $_SESSION["fechacomprobante"];
		$ls_procedencia="SCCCIE";
		$ls_descripcion="CIERRE DE CAJA CHICA: ".$this->DatosCaja['dencaj']." - CÓDIGO DE CAJA: ".$this->DatosCaja['codcaj'];
		$ls_tipo="B";
		$this->classcmp->io_int_int->is_tipo=$ls_tipo;
		$this->classcmp->io_int_int->is_cod_prov="----------";
		$this->classcmp->io_int_int->is_ced_ben=$this->DatosCaja['ced_bene'];		
		$this->classcmp->io_int_int->ib_procesando_cmp=false;
		$this->classcmp->io_int_int->id_fecha=$datos['fecha'];
		$ls_codban     = "---";
		$ls_ctaban     = "-------------------------";
		$ls_rendfon    =0;
		$ls_fuentefin="--";
		
		$resp=$this->classcmp->io_int_int->uf_init_delete($ls_codemp,$ls_procedencia,$ls_comprobante, 
		                                                  $this->classcmp->io_int_int->id_fecha,$ls_tipo,
														  $this->classcmp->io_int_int->is_ced_ben,
														  $this->classcmp->io_int_int->is_cod_prov,
														  false,$ls_codban,$ls_ctaban);

		if(!$resp){		   
		   $mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Comprobante no existe'. 				           			    
					   '<br><b>METODO:</b> EliminarCmpCierre ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;	
		}	
				
	    $resp = $this->classcmp->io_int_int->uf_int_init_transaction_begin();
		if(!$resp){
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br>'.$this->classcmp->io_int_int->is_msg_error. 				           			    
					   '<br><b>METODO:</b> EliminarCmpCierre ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;
			
		}	
		
		$resp = $this->classcmp->io_int_int->uf_init_end_transaccion_integracion($this->seguridad);
		if (!$resp){			
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br>'.$this->classcmp->io_int_int->is_msg_error. 				           			    
					   '<br><b>METODO:</b> EliminarCmpCierre ';
			$this->io_conexiones->mensajes_ajax($mensaje);
			$this->classcmp->io_int_int->io_sql->rollback();																	
			return false;
			
		}
		
		$this->classcmp->io_int_int->io_sql->commit();
		return true;
	
	}
	
	function procesar_recepcion_documento_inc($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: procesar_recepcion_documento_inc
		//         Access: public  
		//      Argumento: $datos
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de generar la recepción de documento de Incremento de Caja
		//	   Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 18/01/2013							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
		if(!$datos['codcaj'] or !$this->codcoreltipmov)
		{				
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder generar la recepción de documento de Incremento de Caja Chica. 				           			    
						<br><b>METODO:</b> procesar_recepcion_documento_inc ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;
		}
		
		$datos['numrecdoc'] = "SCC-I".$this->codcoreltipmov;
		$datos['procede'] = "SCCINC";
		$datos['dencondoc'] = "INCREMENTO DE CAJA CHICA: ".$this->DatosCaja['dencaj']." - CÓDIGO DE CAJA: ".$this->DatosCaja['codcaj']." - NÚMERO DE INCREMENTO: ".(integer)$this->codcoreltipmov;
		$datos['codtipdoc'] = $this->DatosCaja['codtipdoc'];
		$datos['ced_bene'] = $this->DatosCaja['ced_bene'];		 
		$this->DatosRD = $datos;
		
		$resp=$this->scc_validar_recepcion_documentos($datos);
		if($resp===false){return false;} 
		if($resp->RecordCount()){$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> Ya existe una Recepción de Documento para esta Caja Chica ! <br> Número: ".$datos['numrecdoc']);}

		$resp=$this->insert_recepcion_documento($datos);
		if($resp===false){return false;} 
				
		$datos["debhab"] = 'D';
		$datos["sc_cuenta"] = $this->DatosCaja['sc_cuenta'];
		$resp = $this->VerificaCuentaSCG($datos);	
		if($resp===false){return false;}	
		$resp=$this->insert_rd_detalle_contable($datos);
		if($resp===false){return false;}
		
		$datos["debhab"] = 'H';
		$datos["sc_cuenta"] = $this->cuenta_beneficiario($datos);
		if($datos["sc_cuenta"]===false){return false;}
		$resp = $this->VerificaCuentaSCG($datos);
		if($resp===false){return false;}
		$resp=$this->insert_rd_detalle_contable($datos);
		if($resp===false){return false;}
	
		$this->seguridad["evento"]="INSERT";
		$this->seguridad["descripcion"]="Generó la Recepción de Documento de Caja Chica <b>".$datos['codcaj']."</b>, ".
						                "Comprobante <b>".$datos['numrecdoc']."</b>";
		$this->guardar_seguridad();
		
		return true;
	
	}
	
	function scc_validar_recepcion_documentos($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: scc_validar_recepcion_documentos
		//         Access: public  
		//      Argumento: $datos
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 18/01/2013							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		
		$ls_sql="SELECT numrecdoc,estprodoc,estaprord
				  FROM cxp_rd
				 WHERE codemp='".$this->ls_codemp."'
				   AND numrecdoc='".$datos['numrecdoc']."'
				   AND codtipdoc='".$datos['codtipdoc']."'
				   AND ced_bene='".$datos['ced_bene']."'
				   AND cod_pro='----------' ".$criterio;
		$rs_data=$this->io_sql->select($ls_sql);
		//echo $ls_sql;
		if($rs_data==false){				
			$metodo = 'scc_validar_recepcion_documentos';
			$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
			$this->io_mensajes->message($mensaje);
			return false;				
		}
		
		return $rs_data;
				
	}

	function cuenta_beneficiario($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: cuenta_beneficiario
		//         Access: public  
		//      Argumento: $datos
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 18/01/2013							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ls_sql="SELECT *
				  FROM rpc_beneficiario
				 WHERE ced_bene='".$datos['ced_bene']."'";				 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false){				
			$metodo = 'cuenta_beneficiario';
			$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
			$this->io_mensajes->message($mensaje);
			return false;				
		}
		
		if(!$rs_data->RecordCount()){
			$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> El Beneficiario no posee cuenta contable !");
			return false;
		}
		
		return $rs_data->fields['sc_cuenta'];
				
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function insert_recepcion_documento($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: insert_recepcion_documento
		//		   Access: private
		//	    Arguments: $datos
		//	      Returns: $lb_valido True si se genero la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Lic. Edgar A. Quintero U.
		// Fecha Creación: 18/07/2013 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$datos['feccaj'] = $datos['feccaj']?$datos['feccaj']:$datos['fecha'];
		
		if(!$datos['codcaj'] or !$datos['dencondoc'] or !$datos['feccaj'] or !$datos['ced_bene'] or !$datos['numrecdoc'] or !$datos['monto'] or !$datos['procede'])
		{				
			$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de insert. 				           			    
						<br><b>METODO:</b> insert_recepcion_documento ';
			$this->io_conexiones->mensajes_ajax($mensaje);																	
			return false;
		}		
		
		$ls_codrecdoc=$this->io_keygen->uf_generar_numero_nuevo("CXP","cxp_rd","codrecdoc","CXPRCD",15,"","","");
		$datos['codfuefin'] = $datos['codfuefin']?$datos['codfuefin']:'--';
		$datos['repcajchi'] = $datos['repcajchi']?$datos['repcajchi']:0;
		//Nota de OFIMATICA DE VENEZUELA se agrega a la consulta el campo repcajchi para determinar si la recepcion de documento generada por el viatico corresponde a una reposicion de caja chica
		$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,
 		                            montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,
				                    fecaprord,usuaprord,estimpmun,codcla,codfuefin,codrecdoc,repcajchi)
				     VALUES ('".$this->ls_codemp."','".$datos['numrecdoc']."','".$datos['codtipdoc']."','".$datos['ced_bene']."',
				             '----------','".$datos['dencondoc']."','".$datos['feccaj']."','".$datos['feccaj']."','".$datos['feccaj']."',"
				               .$datos['monto'].",0,0,'B','".$datos['numrecdoc']."','R','".$datos['procede']."',0,0,'1900-01-01','".$_SESSION["la_logusr"]."',0,'--','".
							    $datos['codfuefin']."','".$ls_codrecdoc."',".$datos['repcajchi'].")";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{  			
			$metodo = 'insert_recepcion_documento';
			$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
			$this->io_mensajes->message($mensaje);
			return false;
			
		}
		
		return true;
	}  // end function uf_scv_procesar_recepcion_documento_viatico
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function insert_rd_detalle_contable($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: insert_rd_detalle_contable
		//		   Access: private
		//	    Arguments: $datos
		//	      Returns: True si se inserto los detalles contables en la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Lic. Edgar A. Quintero U.
		// Fecha Creación: 18/07/2013 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $as_codtipdoc= $this->DatosRD["codtipdoc"];
		$ls_sccuenta= $datos["sc_cuenta"];
		$ls_debhab=     $datos["debhab"];				
		$ls_documento=  $this->DatosRD["numrecdoc"];								 
		$ls_cedbene=    $this->DatosRD["ced_bene"];								 
		$ls_codpro=     '----------';								 
		$ls_monto=  $datos["monto"];		
		$ls_documento= str_pad($ls_documento, 15, "0", STR_PAD_LEFT);
		
		$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,
										 sc_cuenta,monto)
				     VALUES ('".$this->ls_codemp."','".$ls_documento."','".$as_codtipdoc."','".$ls_cedbene."',
				             '".$ls_codpro."','".$ls_procede."','".$ls_documento."','".$ls_debhab."',
				             '".$ls_sccuenta."',".$ls_monto.")";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$metodo = 'insert_recepcion_documento';
			$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
			$this->io_mensajes->message($mensaje);
			return false;
		}
		
		$ls_sql="INSERT INTO scc_dt_scg (codemp, codmov, codcom, sc_cuenta, 
		                                 debhab, cod_pro, ced_bene, 
										 tipo_destino, descripcion, monto)
				     VALUES ('".$this->ls_codemp."','".$this->codmov."','".$ls_documento."','".$ls_sccuenta."',
				             '".$ls_debhab."','".$ls_codpro."','".$ls_cedbene."',
							 'B','".$this->DatosRD['dencondoc']."',".$ls_monto.")";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$metodo = 'insert_recepcion_documento';
			$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
			$this->io_mensajes->message($mensaje);
			return false;
		}			
		
		
		return true;
    } // end function uf_insert_recepcion_documento_contable
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function insert_rd_detalle_gasto($datos=array())
	{
	   

		$codestpro=$datos["codestpro1"].$datos["codestpro2"].$datos["codestpro3"].$datos["codestpro4"].$datos["codestpro5"];
		$estcla=$datos["estcla"];
		$spg_cuenta= $datos["spg_cuenta"];
		$numrecdoc=  $this->DatosRD["numrecdoc"];								 
		$ced_bene=    $this->DatosRD["ced_bene"];
		$procede=    $this->DatosRD["procede"];
		$codtipdoc=    $this->DatosRD["codtipdoc"];								 
		$codpro=     '----------';
		$codfuefin=  $datos["codfuefin"];								 
		$monto=  $datos["monto"];							 
		
		$ls_sql="INSERT INTO cxp_rd_spg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,codestpro,
										 spg_cuenta,monto,estcla,codfuefin)
				     VALUES ('".$this->ls_codemp."','".$numrecdoc."','".$codtipdoc."',
				             '".$ced_bene."','".$codpro."','".$procede."','".$this->codmov."','".$codestpro."',
				             '".$spg_cuenta."',".$monto.",'".$estcla."','".$codfuefin."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->insert_rd_detalle_gasto MÉTODO->".get_class($this)." ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			return false;
			
		}	
		

		
		$ls_sql="INSERT INTO scc_dt_spg (codemp, codmov, codcom, spg_cuenta, 
									     codestpro1, codestpro2, codestpro3,codestpro4, codestpro5, estcla, 
										 operacion, cod_pro, ced_bene, 
										 tipo_destino, descripcion, monto, codfuefin)
				     VALUES ('".$this->ls_codemp."','".$this->codmov."','".$numrecdoc."','".$spg_cuenta."',
				             '".$datos["codestpro1"]."','".$datos["codestpro2"]."','".$datos["codestpro3"]."','".$datos["codestpro4"]."','".$datos["codestpro5"]."','".$estcla."',
				             'OC','".$codpro."','".$ced_bene."'
							 ,'B','".$this->DatosRD['dencondoc']."','".$monto."','".$codfuefin."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->insert_rd_detalle_gasto MÉTODO->".get_class($this)." ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			return false;
			
		}	
			
		return true;
    } // end function uf_insert_recepcion_documento_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------

	
	function ModificarCajaChica($datos=array()){
		
		   $metodo = 'ModificarCajaChica';
		   
		   if(!$datos['codcaj'] or !$datos['dencaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Actualización. 				           			    
							<br><b>METODO:</b> '.$metodo;
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}			
			// CAÑIZALES
		   $ls_sql = "  UPDATE scc_cajachica
						 SET dencaj='".$datos['dencaj']."',
						   ced_bene='".$datos['ced_bene']."'
					   WHERE codemp ='".$this->ls_codemp."'  
						 AND codcaj ='".$datos['codcaj']."'";
						
		
						
			$rs_data=$this->io_sql->select($ls_sql);			
			//echo $ls_sql;
			if($rs_data==false)
			{				
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;
				
			}
			
			if($this->io_sql->conn->Affected_Rows()<1){			       
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
			}				
			
			$this->seguridad["evento"]="UPDATE";
			$this->seguridad["descripcion"]="Se Modifico la Caja Chica ".$datos['codcaj']." Descripción: ".$datos['dencaj'];
			$this->guardar_seguridad();
				
			return true;
	
	}
	
	function  EliminarCajaChica($datos=array()){
				
				$metodo = 'EliminarCajaChica';
				
				if(!$datos['codcaj'] or !$datos['codtipdoc'] or !$datos['ced_bene'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Eliminación. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}
				
				$resp = $this->ValidaMovimientosCaja($datos);
				if($resp===false){return false;}
				
				$datos['numrecdoc'] = "SCC-APR0000".$datos['codcaj'];
								
				$resp = $this->scc_validar_recepcion_documentos($datos);
				if($resp===false){return false;}
				echo $resp->RecordCount();
				if(($resp->fields['estprodoc']!='R' or $resp->fields['estaprord']=='1') and $resp->RecordCount()){
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> La Recepción de Documentos ya fue procesada. 
                                <br> No se puede eliminar la Caja Chica.	
                                <br><b>ESTATUS RECEPCIÓN:</b> '.$resp->fields['estprodoc'].'	
								<br><b>ESTATUS APROBACIÓN:</b> '.(($resp->fields['estprodoc'])?'APROBADA':'NO APROBADA').'								
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');
					return false;
				
				}
				
				$resp = $this->EliminarDetalleScgRD($datos);
				if($resp===false){return false;}
				
				$resp = $this->EliminarRD($datos);
				if($resp===false){return false;}
				
				$resp = $this->EliminarMovCajaChica($datos);
				if($resp===false){return false;}
				
				
				$ls_sql = "DELETE FROM scc_cajachica
				           WHERE codcaj  = '".$datos['codcaj']."'";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;					
				}
				
				if($this->io_sql->conn->Affected_Rows()<1){
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
				}		
				
				$this->seguridad["evento"]="DELETE";
				$this->seguridad["descripcion"]="Se Eliminó el Objetivo Estratégico Institucional ".$datos['codtiporg']." Descripción: ".$datos['destiporg'];
				$this->guardar_seguridad();
				
				$mensaje = 'Se eliminaron '.$this->io_sql->conn->Affected_Rows().'Caja(s) Chica(s)';			
				$this->io_conexiones->dato_js('hfilas_afectadas',$mensaje);
				return true;
	
	}
	
	function  EliminarDetalleSpgRD($datos=array()){
				
				$metodo = 'EliminarDetalleSpgRD';
				
				if(!$datos['numrecdoc'] or !$datos['codtipdoc'] or !$datos['ced_bene'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Eliminación. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}
				
			    $ls_sql = " DELETE FROM cxp_rd_spg
				             WHERE codemp='".$this->ls_codemp."'
							   AND numrecdoc='".$datos['numrecdoc']."'
							   AND codtipdoc='".$datos['codtipdoc']."'
							   AND ced_bene='".$datos['ced_bene']."'
							   AND cod_pro='----------' ";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;					
				}
				/*
				if($this->io_sql->conn->Affected_Rows()<1){
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
				}		
				*/
				
				 $ls_sql = " DELETE FROM scc_dt_spg
				             WHERE codemp='".$this->ls_codemp."'
							   AND codcom='".$datos['numrecdoc']."'
							   AND codmov='".$this->codmov."'; ";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;					
				}
				
				if($this->io_sql->conn->Affected_Rows()<1){
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
				}		
				
				return true;
	
	}
	
	function  EliminarDetalleScgRD($datos=array()){
				
				$metodo = 'EliminarDetalleScgRD';
				
				if(!$datos['numrecdoc'] or !$datos['codtipdoc'] or !$datos['ced_bene'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Eliminación. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}
				
			    $ls_sql = " DELETE FROM cxp_rd_scg
				             WHERE codemp='".$this->ls_codemp."'
							   AND numrecdoc='".$datos['numrecdoc']."'
							   AND codtipdoc='".$datos['codtipdoc']."'
							   AND ced_bene='".$datos['ced_bene']."'
							   AND cod_pro='----------' ";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				//echo $ls_sql;
				if($this->rs_data==false)
				{
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;					
				}
				/*
				if($this->io_sql->conn->Affected_Rows()<1){
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
				}		
				*/
				
				$ls_sql = " DELETE FROM scc_dt_scg
				             WHERE codemp='".$this->ls_codemp."'
							   AND codcom='".$datos['numrecdoc']."'
							   AND codmov='".$this->codmov."'; ";
				
				if(!$this->codmov){
				 $ls_sql = " DELETE FROM scc_dt_scg
				             WHERE codemp='".$this->ls_codemp."'
							   AND codcom='".$datos['numrecdoc']."'; ";
				}
				
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				//echo $ls_sql;
				if($this->rs_data==false)
				{
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;					
				}
				
				if($this->io_sql->conn->Affected_Rows()<1){
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
				}		
				
				return true;
	
	}
	
	function  EliminarRD($datos=array()){
				
				$metodo = 'EliminarRD';
				
				if(!$datos['numrecdoc'] or !$datos['codtipdoc'] or !$datos['ced_bene'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Eliminación. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}
				
			    $ls_sql = " DELETE FROM cxp_rd
				             WHERE codemp='".$this->ls_codemp."'
							   AND numrecdoc='".$datos['numrecdoc']."'
							   AND codtipdoc='".$datos['codtipdoc']."'
							   AND ced_bene='".$datos['ced_bene']."'
							   AND cod_pro='----------'
							   AND estprodoc='R' ";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;					
				}
				/*
				if($this->io_sql->conn->Affected_Rows()<1){
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
				}		
				*/
				$this->seguridad["evento"]="DELETE";
				$this->seguridad["descripcion"]="Se Eliminó la Recepción de Documentos ".$datos['numrecdoc']." Ced. Beneficiario: ".$datos['ced_bene'];
				$this->guardar_seguridad();
				
				return true;
	
	}
	
	
	function  EliminarMovCajaChica($datos=array()){
				
				$metodo = 'EliminarMovCajaChica';
				
				if(!$datos['codcaj'] or !$datos['codtipmov'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Eliminación. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}
				
				$this->codcaj = $datos['codcaj'];
				$this->codmov = $datos['codmov'];
				
				if($datos['codmov']){$criterio .= " AND codmov = '".$datos['codmov']."' ";}
				
				
				if(!$datos['codmov'] and $datos['codtipmov']!="APR")
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Falta el Código del Movimiento para poder realizar la operación de Eliminación. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}
				
				if($datos['codtipmov']=='MOV'){$criterio .= " AND estrepo='0' ";}
				
				
				if($datos['codtipmov']=='REP'){	
				   
				    $datos['criterio']='por_codigo';
					$resp = $this->ConsultaCajaChica($datos);
					if($resp===false){return false;}				
					if(!$resp['rs']->RecordCount()){
						$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> No se encontraron los datos de configuración de la Caja Chica !");
						return false;
					}
					$this->DatosCaja = 	$resp['rs']->fields;
								
					$datos['criterio']='por_codmov';
					$resp = $this->ConsultaRep($datos);
					if($resp===false){return false;}				
					if(!$resp['rs']->RecordCount()){
						$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> No se encontraron los datos de la Reposición !");
						return false;
					}
					$this->DatosRep = 	$resp['rs']->fields;
					
					$this->codrep = $this->DatosRep['codrep'];
										
					$resp = $this->EliminarRep($datos);			
					if($resp===false){return false;}
															
				}
				
				if($datos['codtipmov']=='INC'){	
				   
				    $datos['criterio']='por_codigo';
					$resp = $this->ConsultaCajaChica($datos);
					if($resp===false){return false;}				
					if(!$resp['rs']->RecordCount()){
						$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> No se encontraron los datos de configuración de la Caja Chica !");
						return false;
					}
					$this->DatosCaja = 	$resp['rs']->fields;
					$this->codcoreltipmov = $datos['codcoreltipmov'];			
													
					$resp = $this->EliminarInc($datos);			
					if($resp===false){return false;}
															
				}
				
				if($datos['codtipmov']=='CIE'){	
				   
				    $datos['criterio']='por_codigo';
					$resp = $this->ConsultaCajaChica($datos);
					if($resp===false){return false;}				
					if(!$resp['rs']->RecordCount()){
						$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> No se encontraron los datos de configuración de la Caja Chica !");
						return false;
					}
					$this->DatosCaja = 	$resp['rs']->fields;
					$this->codcoreltipmov = $datos['codcoreltipmov'];			
					
					$datos['criterio']='por_codigo';
					$rscie = $this->ConsultaMovCajaChica($datos);
					if($rscie===false){return false;}				
					if(!$rscie['rs']->RecordCount()){
						$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> No se encontraron los datos del movimiento de cierre de la Caja Chica !");
						return false;
					}
					$this->DatosMovCaja = 	$rscie['rs']->fields;
													
					$resp = $this->EliminarCmpCierre($this->DatosMovCaja);			
					if($resp===false){return false;}
															
				}
				
			    $ls_sql = "  DELETE FROM scc_mov_caja
				              WHERE codemp ='".$this->ls_codemp."' 
								AND codcaj ='".$datos['codcaj']."'								
								AND codtipmov ='".$datos['codtipmov']."'						       
						  ".$criterio;
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;					
				}
				
				if($this->io_sql->conn->Affected_Rows()<1){
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
				}		
				
				
				
				$this->seguridad["evento"]="DELETE";
				$this->seguridad["descripcion"]="Se Eliminó el Movimiento de Caja Chica Código de Caja: ".$datos['codcaj']." Tipo: ".$datos['codtipmov']." Código de Movimiento: ".$datos['codmov'];
				$this->guardar_seguridad();
				
				return true;
	
	}
	
	
	function  EliminarRep($datos=array()){
				
				$metodo = 'EliminarRep';
				
				if(!$datos['codcaj'] or !$this->codrep or !$this->DatosCaja['codtipdocrep'] or !$this->DatosCaja['ced_bene'] )
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Eliminación. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}
				
				$datos['numrecdoc'] = "SCC-R".$this->codrep;
				$datos['procede'] = "SCCREP";
				$datos['dencondoc'] = "REPOSICIÓN DE CAJA CHICA: ".$this->DatosCaja['dencaj']." CÓDIGO DE CAJA: ".$this->DatosCaja['codcaj'];
				$datos['codtipdoc'] = $this->DatosCaja['codtipdocrep'];
				$datos['ced_bene'] = $this->DatosCaja['ced_bene'];		 
				$this->DatosRD = $datos;
												
				$resp = $this->scc_validar_recepcion_documentos($datos);
				if($resp===false){return false;}
				if(($resp->fields['estprodoc']!='R' or $resp->fields['estaprord']=='1') and $resp->RecordCount()){
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> La Recepción de Documentos ya fue procesada. 
                                <br> No se puede eliminar la Reposición.	
                                <br><b>ESTATUS RECEPCIÓN:</b> '.$resp->fields['estprodoc'].'	
								<br><b>ESTATUS APROBACIÓN:</b> '.(($resp->fields['estprodoc'])?'APROBADA':'NO APROBADA').'								
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');
					return false;
				
				}
				
				$resp = $this->EliminarDetalleSpgRD($datos);
				if($resp===false){return false;}
				
				$resp = $this->EliminarDetalleScgRD($datos);
				if($resp===false){return false;}
				
				$resp = $this->EliminarRD($datos);
				if($resp===false){return false;}				
				
				
			    $ls_sql = " UPDATE scc_mov_caja SET estrepo = '0' 
				            WHERE codmov IN (   SELECT dr.codmovdet
												FROM scc_dt_reposiciones dr
												INNER JOIN  scc_reposiciones r	ON r.codemp = dr.codemp
																AND r.codrep = dr.codrep
												INNER JOIN scc_mov_caja mc ON mc.codmov = r.codmovrep 
																		   AND mc.codcaj = r.codcaj 										
												WHERE dr.codcaj = '".$datos['codcaj']."'
												AND dr.codrep = '".$this->codrep."'
											  )
							  AND  codcaj = '".$datos['codcaj']."'; 
							
				
							DELETE FROM scc_dt_reposiciones
								  WHERE codemp ='".$this->ls_codemp."' 
									AND codcaj ='".$datos['codcaj']."'								
									AND codrep ='".$this->codrep."';
									
							DELETE FROM scc_reposiciones
								  WHERE codemp ='".$this->ls_codemp."' 
									AND codcaj ='".$datos['codcaj']."'								
									AND codrep ='".$this->codrep."';
						  ";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;					
				}
				
				if($this->io_sql->conn->Affected_Rows()<1){
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
				}		
				
				$this->seguridad["evento"]="DELETE";
				$this->seguridad["descripcion"]="Se Eliminó la reposición de Caja Chica Código de Caja: ".$datos['codcaj']." codigo de movimiento: ".$datos['codmov']." Código de Reposición: ".$datos['codrep'];
				$this->guardar_seguridad();
				
				return true;
	
	}
	
	function  EliminarInc($datos=array()){
				
				$metodo = 'EliminarInc';
				
				if(!$datos['codcaj'] or !$this->DatosCaja['codtipdoc'] or !$this->DatosCaja['ced_bene'] or !$this->codcoreltipmov )
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Eliminación. 				           			    
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');																	
					return false;
				}

	
				$datos['numrecdoc'] = "SCC-I".$this->codcoreltipmov;
				$datos['procede'] = "SCCINC";
				$datos['dencondoc'] = "INCREMENTO DE CAJA CHICA: ".$this->DatosCaja['dencaj']." - CÓDIGO DE CAJA: ".$this->DatosCaja['codcaj']." - NÚMERO DE INCREMENTO: ".(integer)$this->codcoreltipmov;
				$datos['codtipdoc'] = $this->DatosCaja['codtipdoc'];
				$datos['ced_bene'] = $this->DatosCaja['ced_bene'];		 
				$this->DatosRD = $datos;
												
				$resp = $this->scc_validar_recepcion_documentos($datos);
				if($resp===false){return false;}
				if($resp->fields['estprodoc']!='R' or $resp->fields['estaprord']=='1'){
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> La Recepción de Documentos ya fue procesada. 
                                <br> No se puede eliminar el Incremento de Caja Chica.	
                                <br><b>ESTATUS RECEPCIÓN:</b> '.$resp->fields['estprodoc'].'	
								<br><b>ESTATUS APROBACIÓN:</b> '.(($resp->fields['estprodoc'])?'APROBADA':'NO APROBADA').'								
							    <br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje,'error');
					return false;
				
				}
										
				$resp = $this->EliminarDetalleScgRD($datos);
				if($resp===false){return false;}
				
				$resp = $this->EliminarRD($datos);
				if($resp===false){return false;}				

				
				return true;
	
	}
	
	 function ConsultaMovCajaChica($param=array()){
	
			$campos = " * ";
			$criteriosql='';
			$criterio="";
			$param['criterio'] = $param['criterio']?$param['criterio']:'por_listado';
						
			switch($param['criterio']){
								
				case "por_listado":	
				
						$campos = " mc.*,cc.dencaj,cc.feccaj,cc.monto AS moncaj,cc.codmon AS codmoncaj,cc.tascam AS tascamcaj,
									td.*,tm.*,ua.denuniadm,m.denmon,m.abrmon  ";		
						if($param['codtipmov']){$criterio .= "  AND mc.codtipmov ".$this->postgres_ilike."LIKE('%".$param['codtipmov']."%') ";}			
						if($param['estrepo']!=""){$criterio .= "  AND mc.estrepo ='".$param['estrepo']."' ";}
						if($param['conciliado']!=""){$criterio .= "  AND mc.conciliado ='".$param['conciliado']."' ";}
																	
					    $sql_criterio = "  INNER JOIN scc_cajachica cc ON cc.codemp = mc.codemp
																	 AND cc.codcaj = mc.codcaj
										   LEFT JOIN scc_conceptos td ON td.codconcaj = mc.codconcaj
										   INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov 						 
										   LEFT JOIN spg_unidadadministrativa ua ON ua.coduniadm = mc.coduniadm
										   LEFT JOIN sigesp_moneda m ON m.codmon = mc.codmon	
										   WHERE mc.codcaj = '".$param['codcaj']."'
						                     AND cc.dencaj ".$this->postgres_ilike."LIKE('%".$param['dencaj']."%') ".$criterio;
					    break;
				
			    case "por_codigo":				        
						$campos = " mc.*,cc.dencaj,cc.feccaj,cc.monto AS moncaj,cc.codmon AS codmoncaj,cc.tascam AS tascamcaj,
									td.*,tm.*,ua.denuniadm,m.denmon,m.abrmon  ";	
					    $sql_criterio = "  INNER JOIN scc_cajachica cc ON cc.codemp = mc.codemp
																	 AND cc.codcaj = mc.codcaj
										   LEFT JOIN scc_conceptos td ON td.codconcaj = mc.codconcaj
										   INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov 						 
										   LEFT JOIN spg_unidadadministrativa ua ON ua.coduniadm = mc.coduniadm
										   LEFT JOIN sigesp_moneda m ON m.codmon = mc.codmon
										   WHERE mc.codemp ='".$this->ls_codemp."' 
						                     AND mc.codcaj ='".$param['codcaj']."'
						                     AND mc.codmov ='".$param['codmov']."'";
					    break;
				
				 case "por_tipomov":				        
						$campos = " *  ";		
					    $sql_criterio = " WHERE mc.codemp ='".$this->ls_codemp."' 
						                    AND mc.codcaj ='".$param['codcaj']."'
						                    AND mc.codtipmov ='".$param['codtipmov']."'";
					    break;
						
						
				  case "por_concepto":				        
						$campos = " *  ";		
					    $sql_criterio = " WHERE mc.codemp ='".$this->ls_codemp."' 
						                    AND mc.codconcaj ='".$param['codconcaj']."'";
					    break;
				
			}
								   
			$query_rs = "SELECT ".$campos." FROM scc_mov_caja mc ".$sql_criterio ." ORDER BY mc.fecha,mc.codmov";			
			
			//echo $query_rs.'<br>';
			$clase = get_class($this);
			$metodo = 'ConsultaCajaChica';
			$param['arreglo'] = 'arreglo';
			$param['ajax'] = '0';
			$param['imprimir'] = '1';	
			$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;	
			$respuesta = $this->io_conexiones->conexion($query_rs,$param,$msj);	
			echo $respuesta;
			exit();
			return $respuesta;
	
	}
	
	function  InsertarMovCajaChica($datos=array()){
				
				if(!$datos['codcaj'] or !$datos['codtipmov'])
				{				
					$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de insertar movimiento de caja chica. 				           			    
							    <br><b>METODO:</b> InsertarMovCajaChica ';
					$this->io_conexiones->mensajes_ajax($mensaje);																	
					return false;
				}
				
				$this->codcaj = $datos['codcaj'];
				$this->codmov = $datos['codmov'];
				
				if($datos['codtipmov']=='APR' or $datos['codtipmov']=='CIE'){
						$param = $datos;
						$param['criterio'] = 'por_tipomov';
						$resp = $this->ConsultaMovCajaChica($param);
						if($resp===false){return false;}
						if($resp['rs']->RecordCount()){
								$mensaje = "<b>ERROR:</b> El movimiento de caja chica ya existe !";
								$this->io_conexiones->mensajes_ajax($mensaje);
								return false;
						}
				}
				
				
				if($datos['codtipmov']=='INC'){		
				    
					$datos['criterio']='por_codigo';
					$resp = $this->ConsultaCajaChica($datos);
					if($resp===false){return false;}				
					if(!$resp['rs']->RecordCount()){
						$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> No se encontraron los datos de configuración de la Caja Chica !");
						return false;
					}
					$this->DatosCaja = 	$resp['rs']->fields;
					 			
					$resp = $this->ValidaAperturaCaja($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaCierreCaja($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaFechaMov($datos);
					if($resp===false){return false;}
									
				}
				
				
				if($datos['codtipmov']=='MOV'){					
					$resp = $this->ValidaAperturaCaja($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaFechaMov($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaSaldoCaja($datos);
					if($resp===false){return false;}					
				}
				
				
				if($datos['codtipmov']=='REP'){	
				   
				    $datos['criterio']='por_codigo';
					$resp = $this->ConsultaCajaChica($datos);
					if($resp===false){return false;}				
					if(!$resp['rs']->RecordCount()){
						$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> No se encontraron los datos de configuración de la Caja Chica !");
						return false;
					}
					$this->DatosCaja = 	$resp['rs']->fields;
								
					$resp = $this->ValidaAperturaCaja($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaCierreCaja($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaFechaMov($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaFechaRep($datos);
					if($resp===false){return false;}
					
					$datos['monto'] = $this->BuscaMontoReposicion($datos);
					if($datos['monto']===false){return false;}
					
										
															
				}
				
				
				if($datos['codtipmov']=='CIE'){	
				   
				    $datos['criterio']='por_codigo';
					$resp = $this->ConsultaCajaChica($datos);
					if($resp===false){return false;}				
					if(!$resp['rs']->RecordCount()){
						$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> No se encontraron los datos de configuración de la Caja Chica !");
						return false;
					}
					$this->DatosCaja = 	$resp['rs']->fields;
								
					$resp = $this->ValidaAperturaCaja($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaCierreCaja($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaFechaMov($datos);
					if($resp===false){return false;}
					
					$resp = $this->ValidaFechaRep($datos);
					if($resp===false){return false;}
													
					$datos['monto'] = $this->BuscaMontoReposicion($datos);
					if($datos['monto']===false){return false;}	
					$this->MontoRep = $datos['monto'];
					
					
					if($datos['monto']>$this->DatosCaja['monto']){
						$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> El monto de los movimientos para cerrar es mayor al saldo de apertura de la Caja Chica !");
						return false;
					}
					
					//SALDO DE CAJA  (SALDO = MONTO_APR - MONTO_REP)
					$datos['monto'] = $this->DatosCaja['monto'] - $datos['monto'];
									
															
				}
				
				$datos['codmov'] = $this->GeneraCodMov($datos);
				$this->codmov = $datos['codmov'];
				if($datos['codmov']===false){return false;}
				$datos['conciliado'] = $datos['conciliado']?$datos['conciliado']:'N';				
				$datos['mediopag'] = $datos['mediopag']?$datos['mediopag']:'EFEC';
				$datos['codfuefin'] = $datos['codfuefin']?$datos['codfuefin']:'--';
				$datos['estcla'] = $datos['estcla']?$datos['estcla']:'-';
				$datos['tascam'] = $datos['tascam']?$datos['tascam']:1;
				if($datos['codtipmov']=='MOV'){$datos['estrepo']=0; $datos['conciliado']="S";}				
				if($datos['codtipmov']!='MOV'){$datos['estrepo']=1; $datos['conciliado']="N";}
				if($datos['codtipmov']=='CIE'){$datos['estrepo']=0; $datos['conciliado']="S";}
				$datos['codcoreltipmov'] = $this->GeneraCodCorrelativoTipMov($datos);
				if($datos['codcoreltipmov']===false){return false;}
				$this->codcoreltipmov = $datos['codcoreltipmov'];				
				$datos['fecdep'] = $datos['fecdep']?"'".$datos['fecdep']."'":"NULL";
				
				
				$ls_sql = "INSERT INTO scc_mov_caja( codemp, codcaj, codtipmov, codmov, mediopag, codconcaj, monto, fecha, 
													 codmon, tascam, cedresp, nomresp, codsop, jusmov, obsmov, conciliado, 
													 coduniadm, codestpro1, codestpro2, codestpro3, 
													 codestpro4, codestpro5, estcla, codfuefin, estrepo,codcoreltipmov,
													 bancodep, nroctadep, nrodep, fecdep)
						   VALUES (  '".$this->ls_codemp."', ".
								    "'".$datos['codcaj']."', ".
									"'".$datos['codtipmov']."', ".
									"'".$datos['codmov']."', ".
									"'".$datos['mediopag']."', ".
									"'".$datos['codconcaj']."', ".
									"'".$datos['monto']."', ".
									"'".$datos['fecha']."', ".									
									"'".$datos['codmon']."', ".
									"'".$datos['tascam']."', ".
									"'".$datos['cedresp']."', ".
									"'".$datos['nomresp']."', ".
									"'".$datos['codsop']."', ".
									"'".$datos['jusmov']."', ". 
									"'".$datos['obsmov']."', ".
									"'".$datos['conciliado']."', ".	
									"'".$datos['coduniadm']."', ".
									"'".$datos['codestpro1']."', ".
									"'".$datos['codestpro2']."', ".
									"'".$datos['codestpro3']."', ".
									"'".$datos['codestpro4']."', ".
									"'".$datos['codestpro5']."', ".
									"'".$datos['estcla']."', ".
									"'".$datos['codfuefin']."', ".
									"'".$datos['estrepo']."', ".
									"'".$datos['codcoreltipmov']."', ".									
									"'".$datos['bancodep']."', ".
									"'".$datos['nroctadep']."', ".
									"'".$datos['nrodep']."', ".
									"".$datos['fecdep']." ".
									" ); ";									  		        
				
				$rs_data=$this->io_sql->select($ls_sql);	
				if($rs_data==false){				
					$metodo = 'InsertarMovCajaChica';
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
					$this->io_mensajes->message($mensaje);
					return false;				
				}
				
				
				if($datos['codtipmov']=='REP'){					
					
					$resp = $this->ProcesarReposicion($datos);
					if($resp===false){return false;}					
					
					$resp=$this->procesar_recepcion_documento_rep($datos);
					if($resp===false){return false;} 
										
					$resp = $this->ActualizaEstatusReposicion($datos);
					if($resp===false){return false;}
					
				}
				
				if($datos['codtipmov']=='INC'){					
					$resp=$this->procesar_recepcion_documento_inc($datos);
					if($resp===false){return false;} 
									
				}
				
				if($datos['codtipmov']=='CIE'){					
					$resp=$this->ProcesarCmpCierre($datos);
					if($resp===false){return false;} 
									
				}
				
				
				
				
    			$this->seguridad["evento"]="INSERT";
				$this->seguridad["descripcion"]="Se insertó un movimiento de la caja chica ".$datos['codcaj']." Descripción: ".$datos['dencaj']." Código de movimiento: ".$datos['codmov'];
				$this->guardar_seguridad();
				
				return true;
	
	}
	
	function BuscaMontoReposicion($datos=array()){
			
			if(!$datos['codcaj'] and !$datos['codtipmov'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de validación de Fecha de Movimiento 				           			    
							<br><b>METODO:</b> BuscaMontoReposicion ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			$ls_sql =   "   SELECT sum(monto) AS montorep
							FROM scc_mov_caja mc
							INNER JOIN scc_conceptos c ON mc.codconcaj = c.codconcaj 
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov
							WHERE mc.codemp = '".$this->ls_codemp."'
							AND mc.codcaj = '".$datos['codcaj']."'
							AND mc.codtipmov = 'MOV'
							AND estrepo = '0'
			            ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'BuscaMontoReposicion';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
						
			if($rs_data->fields['montorep']==0 and $datos['codtipmov']=='REP'){				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b> No existe monto que reponer. 				           			    
							<br><b>METODO:</b> BuscaMontoReposicion ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			return $rs_data->fields['montorep'];
	}
	
	
	
	function ValidaFechaMov($datos=array()){
			
			if(!$datos['codcaj'] or !$datos['fecha'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de validación de Fecha de Movimiento 				           			    
							<br><b>METODO:</b> ValidaFechaMov ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			$ls_sql =   "SELECT feccaj 
			             FROM scc_cajachica cc
						 WHERE cc.codemp ='".$this->ls_codemp."' 
						   AND cc.codcaj ='".$datos['codcaj']."'						   
						 ORDER BY feccaj DESC LIMIT 1 ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'ValidaFechaMov';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			$feccaj = $rs_data->fields['feccaj'];
			
			$resp = $this->io_fecha->uf_comparar_fecha($feccaj,$datos['fecha']);
			
			if($resp===false){				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b> Fecha Inválida<br> La fecha del movimiento es anterior a la de Apertura de Caja 				           			    
							<br><b>METODO:</b> ValidaFechaMov ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			return $resp;
	}
	
	function ValidaFechaRep($datos=array()){
			
			if(!$datos['codcaj'] or !$datos['fecha'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de validación de Fecha de Movimiento 				           			    
							<br><b>METODO:</b> ValidaFechaRep ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			if($datos['codtipmov']!='CIE'){$estrep = " AND mc.estrepo ='0' ";}
			
			$ls_sql =   "SELECT fecha 
			             FROM scc_mov_caja mc
						 WHERE mc.codemp ='".$this->ls_codemp."' 
						   AND mc.codcaj ='".$datos['codcaj']."'
						   ".$estrep."						   
						 ORDER BY fecha DESC LIMIT 1 ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'ValidaFechaRep';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			//echo $ls_sql;
			$fecmov = $rs_data->fields['fecha'];
			
			$resp = $this->io_fecha->uf_comparar_fecha($fecmov,$datos['fecha']);
			
			if($resp===false){				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b> Fecha Inválida<br> La fecha de la reposición o cierre es anterior a la del último movimiento a reponer 				           			    
							<br><b>METODO:</b> ValidaFechaRep ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			return $resp;
	}
	
	function GeneraCodMov($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder generar el código de movimiento. 				           			    
							<br><b>METODO:</b> GeneraCodMov ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
				
			$ls_sql =   "SELECT codmov 
			             FROM scc_mov_caja mc 
						 WHERE mc.codemp ='".$this->ls_codemp."' 						  
						 ORDER BY codmov DESC LIMIT 1 ";			
			
			//echo $query_rs.'<br>';
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'GeneraCodMov';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			$codmov = '0000000001';
			if(!$rs_data->RecordCount()){return $codmov;}
						
			$codmov = $rs_data->fields['codmov'];
			$codmov = (integer)$codmov;
			$codmov++;
			$codmov = str_pad($codmov, 10, "0", STR_PAD_LEFT);
			return $codmov;
	
	}
	
	
	function GeneraCodCorrelativoTipMov($datos=array()){
			
			if(!$datos['codcaj'] or !$datos['codtipmov'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder generar el código de movimiento. 				           			    
							<br><b>METODO:</b> GeneraCodCorrelativoTipMov ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
				
			$ls_sql =   "SELECT codcoreltipmov 
			             FROM scc_mov_caja mc 
						 WHERE mc.codemp = '".$this->ls_codemp."' 
						   AND mc.codcaj = '".$datos['codcaj']."' 
						   AND mc.codtipmov = '".$datos['codtipmov']."' 					  
						 ORDER BY codmov DESC LIMIT 1 ";			
			
			//echo $ls_sql.'<br>';
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'GeneraCodCorrelativoTipMov';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			$codmov = '0000000001';
			if(!$rs_data->RecordCount()){return $codmov;}
						
			$codmov = $rs_data->fields['codcoreltipmov'];
			$codmov = (integer)$codmov;
			$codmov++;
			$codmov = str_pad($codmov, 10, "0", STR_PAD_LEFT);
			return $codmov;
	
	}
	
	function ValidaSaldoCaja($datos=array()){
		    
			if(!$datos['codcaj'] or !$datos['monto'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder Validar el saldo. 				           			    
							<br><b>METODO:</b> ValidaSaldoCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			$saldocaj = $this->SaldoCaja($datos);
			if($saldocaj===false){return false;}
			
			if(abs($saldocaj)<abs($datos['monto'])){
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> El Saldo en Caja es Menor al Monto del Movimiento. 				           			    
							<br><b>METODO:</b> ValidaSaldoCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);				
				return false;
			}
			
			return true;
			
	}
	
	function ValidaAperturaCaja($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder Validar la Apertura de Caja. 				           			    
							<br><b>METODO:</b> ValidaAperturaCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
				
			$ls_sql =   "   SELECT *
							FROM scc_mov_caja mc
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov 
						    AND mc.codcaj ='".$datos['codcaj']."' 
							AND mc.codtipmov='APR'";			
			
			//echo $query_rs.'<br>';
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data===false){				
				$metodo = 'ValidaAperturaCaja';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			if(!$rs_data->RecordCount()){
			    $mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> La caja no posee movimiento de Apertura. 				           			    
							<br><b>METODO:</b> ValidaAperturaCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			return true;
	
	}
	
	function ValidaCierreCaja($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder Validar el Cierre de Caja. 				           			    
							<br><b>METODO:</b> ValidaCierreCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
				
			$ls_sql =   "   SELECT *
							FROM scc_mov_caja mc
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov 
						    AND mc.codcaj ='".$datos['codcaj']."' 
							AND mc.codtipmov='CIE'";			
			
			//echo $query_rs.'<br>';
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data===false){				
				$metodo = 'ValidaCierreCaja';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			if($rs_data->RecordCount()){
			    $mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> La caja ya se encuentra cerrada. 				           			    
							<br><b>METODO:</b> ValidaCierreCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			return true;
	
	}
	
	function SaldoCaja($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder calcular el saldo. 				           			    
							<br><b>METODO:</b> SaldoCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
				
			$ls_sql =   "   SELECT sum( CASE WHEN signo='D' THEN -(monto)            
										ELSE monto
										END ) AS saldo
							FROM scc_mov_caja mc
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov 
						    WHERE mc.codcaj ='".$datos['codcaj']."'
							  AND mc.conciliado='S' ";			
			
			//echo $query_rs.'<br>';
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data===false){				
				$metodo = 'SaldoCaja';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			
			if(!$rs_data->RecordCount()){return 0;}
						
			$saldo = $rs_data->fields['saldo'];
			return $saldo;
	
	}
	
	
	function ValidaMovimientosCaja($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder Validar los Movimiento. 				           			    
							<br><b>METODO:</b> ValidaMovimientosCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
				
			$ls_sql =   "   SELECT *
							FROM scc_mov_caja mc
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov
							WHERE mc.codcaj ='".$datos['codcaj']."'
							  AND mc.codtipmov!='APR' ";			
			
			//echo $query_rs.'<br>';
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data===false){				
				$metodo = 'ValidaMovimientosCaja';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			
			if($rs_data->RecordCount()){
			    $mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> La caja posee Movimientos. 				           			    
							<br><b>METODO:</b> ValidaMovimientosCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}		
			
			return true;
	
	}
	
	
	function ConsultaTipoMov($param=array(),$criteriox){
	
			$campos = " * ";
			$criteriosql='';
			$criterio="";
			$param['criterio'] = $param['criterio']?$param['criterio']:'por_listado';
			
			switch($param['criterio']){
								
				case "por_listado":	
				
						$campos = " *  ";													
					    $sql_criterio = "  ".$criterio;
					    break;
				
			    case "por_codigo":				        
						$campos = " *  ";		
					    $sql_criterio = " WHERE tm.codtipmov ='".$param['codtipmov']."' ";
					    break;
				
				
			}
								   
			$query_rs = "SELECT ".$campos." FROM scc_tipmov tm ".$sql_criterio ." ORDER BY dentipmov";			
			
			//echo $query_rs.'<br>';
			$clase = get_class($this);
			$metodo = 'ConsultaTipoMov';
			$param['arreglo'] = 'arreglo';
			$param['ajax'] = '0';
			$param['imprimir'] = '1';	
			$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;	
			$respuesta = $this->io_conexiones->conexion($query_rs,$param,$msj);	
			return $respuesta;
	
	}
	
	function ComboTipoMov($opciones=array()){

				if(!$opciones['nombre_combo']){$nombre_combo = 'sel_tipmov';}else{$nombre_combo = $opciones['nombre_combo'];}
				if(!$opciones['codtipmov']){$carga = ' Seleccione '; $id_carga = '';}
				else{	
				    $opciones['criterio'] = 'por_codigo';
					$ofic = $this->ConsultaTipoMov($opciones,'por_codigo');
					if($ofic===false){return false;}
					if(!$ofic['cantidad']){
						$mensaje = '<b>ERROR DE DATOS: </b> No existe el Código: '.$opciones['codtipmov'].
						   		   '<br><b>METODO:</b> ComboTipoMov ';					
						$this->io_conexiones->mensajes_ajax($mensaje);																	
						return false;	
					}
					$carga = $ofic['fila']['dentipmov'];				  
					$id_carga = $opciones['codtipmov'];
				}			
				
				$opciones['criterio'] = 'por_listado';							
				$resp = $this->ConsultaTipoMov($opciones,'por_listado');
				if($resp===false){return false;}
				
				$combo = '<select name="'.$nombre_combo.'" id="'.$nombre_combo.'" onChange="'.$opciones['funcion_js'].'" '.$this->DisableCombo.' >
				              <option value="'.$id_carga.'">- '.$carga.' -</option>';
				
				foreach($resp['rs'] as $dato) {                   			
					$combo .= '<option value="'.$dato["codtipmov"].'" '.$selected.' >'.$dato["dentipmov"].'</option>';								
				}
				$combo .= '</select>';
																							
				return $combo;
	}
	
	function  FormatDatosMov($datos=array()){
		    
			//$datos = $this->FormatLonCodEstPro($datos);
			if($datos['codmon']){
				$datos['monext'] = $datos['monto']/$datos['tascam'];
				$datos['monext'] = number_format($datos['monext'],2,',','.');
				$datos['tascam'] = number_format($datos['tascam'],2,',','.');
			}
			$datos['monto'] = number_format($datos['monto'],2,',','.');
						
			$datos['feccaj'] = $this->io_conexiones->formatea_fecha_normal($datos['feccaj']);	
			$datos['fecha'] = $this->io_conexiones->formatea_fecha_normal($datos['fecha']);
			$datos['fecdep'] = $this->io_conexiones->formatea_fecha_normal($datos['fecdep']);
			
			
			return $datos;
	}
	
	function ModificarMovCajaChica($datos=array()){
		
		   $metodo = 'ModificarMovCajaChica';
		   
		   if(!$datos['codcaj'] or !$datos['codmov'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Actualización. 				           			    
							<br><b>METODO:</b> '.$metodo;
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}			
			
			$datos['fecdep'] = $datos['fecdep']?"'".$datos['fecdep']."'":"NULL";
			
		   $ls_sql = "  UPDATE scc_mov_caja
						 SET cedresp='".$datos['cedresp']."',
							 nomresp='".$datos['nomresp']."',
							 codsop='".$datos['codsop']."',
							 jusmov='".$datos['jusmov']."',
							 obsmov='".$datos['obsmov']."',
							 conciliado='".$datos['conciliado']."',							 
							 bancodep='".$datos['bancodep']."',
							 nroctadep='".$datos['nroctadep']."',
							 nrodep='".$datos['nrodep']."',
							 fecdep=".$datos['fecdep']."							 
					   WHERE codemp ='".$this->ls_codemp."'  
						 AND codcaj ='".$datos['codcaj']."'
						 AND codmov ='".$datos['codmov']."'
						 ";
		
						
			$rs_data=$this->io_sql->select($ls_sql);			
			//echo $ls_sql;
			if($rs_data==false)
			{				
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;
				
			}
			
			if($this->io_sql->conn->Affected_Rows()<1){			       
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
			}				
			
			$this->seguridad["evento"]="UPDATE";
			$this->seguridad["descripcion"]="Se Modifico el Movimiento de Caja Chica ".$datos['codcaj']." Código de Movimiento: ".$datos['codmov'];
			$this->guardar_seguridad();
				
			return true;
	
	}
	
	
	function ValidarCuentasEstrucura($datos=array(),$spg=array()){
			
			if(!$spg['codestpro1'] or !$spg['codestpro2'] or !$spg['codestpro3'] or !$spg['codestpro4'] or !$spg['codestpro5'] or !$spg['estcla'] or !$datos['spg_cuenta'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder Validar la Cuenta en la Estructura. 				           			    
							<br><b>METODO:</b> ValidarCuentasEstrucura ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
				
			$ls_sql =   "   SELECT spg.spg_cuenta
							FROM spg_cuentas spg
							WHERE spg.spg_cuenta = '".$datos['spg_cuenta']."'
							  AND spg.codestpro1 = '".$spg['codestpro1']."'
							  AND spg.codestpro2 = '".$spg['codestpro2']."'
							  AND spg.codestpro3 = '".$spg['codestpro3']."'
							  AND spg.codestpro4 = '".$spg['codestpro4']."'
							  AND spg.codestpro5 = '".$spg['codestpro5']."'
							  AND spg.estcla = '".$spg['estcla']."' ";			
			
			//echo $query_rs.'<br>';
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data===false){				
				$metodo = 'ValidarCuentasEstrucura';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			
			if(!$rs_data->RecordCount()){			   																
				return "NO";
			}		
			
			return "SI";
	
	}
	
	function ActualizaEstatusReposicion($datos=array()){
		
		   $metodo = 'ActualizaEstatusReposicion';
		   
		   if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de Actualización. 				           			    
							<br><b>METODO:</b> '.$metodo;
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}			
			
		   $ls_sql = "  UPDATE scc_mov_caja
						 SET estrepo='1'						  
					   WHERE codemp ='".$this->ls_codemp."'  
						 AND codcaj ='".$datos['codcaj']."'
						 AND codtipmov = 'MOV'
						 AND estrepo = '0' ";
		
						
			$rs_data=$this->io_sql->select($ls_sql);			
			//echo $ls_sql;
			if($rs_data==false)
			{				
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;
				
			}
			
			if($this->io_sql->conn->Affected_Rows()<1){			       
					$mensaje = '<b>ADVERTENCIA:</b> Ninguna fila fué afectada ! <br> La operación no se pudo realizar.
								<br><b>METODO:</b> '.$metodo;
					$this->io_conexiones->mensajes_ajax($mensaje);				
					return false;
			}
				
			return true;
	
	}
	
	function ProcesarReposicion($datos=array()){
				  
		  $resp = $this->InsertarRepo($datos);
		  if($resp===false){return false;} 
		  
		  $resp = $this->InsertarDtRepo($datos);
		  if($resp===false){return false;}
		  
	
	}
	
	
	function ConsultaRep($param=array()){
	
			$campos = " * ";
			$criteriosql='';
			$criterio="";
			$param['criterio'] = $param['criterio']?$param['criterio']:'por_listado';
						
			switch($param['criterio']){
								
				case "por_listado":	
				
						$campos = " r.* ";													
					    $sql_criterio = "  ";
					    break;
				
				 case "por_codmov":				        
						$campos = " r.*  ";		
					    $sql_criterio = " WHERE r.codemp ='".$this->ls_codemp."' 
						                    AND r.codcaj ='".$param['codcaj']."'
						                    AND r.codmovrep = '".$param['codmov']."' ";
					    break;
				
			}
								   
			$query_rs = "SELECT ".$campos." FROM scc_reposiciones r	 ".$sql_criterio ." ORDER BY r.codmovrep ";			
			
			//echo $query_rs.'<br>';
			$clase = get_class($this);
			$metodo = 'ConsultaRep';
			$param['arreglo'] = 'arreglo';
			$param['ajax'] = '0';
			$param['imprimir'] = '1';	
			$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;	
			$respuesta = $this->io_conexiones->conexion($query_rs,$param,$msj);	
			return $respuesta;
	
	}
	
	function ConsultaDetRep($param=array()){
	
			$campos = " * ";
			$criteriosql='';
			$criterio="";
			$param['criterio'] = $param['criterio']?$param['criterio']:'por_listado';
						
			switch($param['criterio']){
								
				case "por_listado":	
				
						$campos = " r.* ";													
					    $sql_criterio = "  ";
					    break;
				
				 case "por_codmov":				        
						$campos = " *,(   SELECT sum(monto)FROM scc_dt_reposiciones dr 
										INNER JOIN scc_reposiciones r ON r.codemp = dr.codemp AND r.codrep = dr.codrep 
										INNER JOIN scc_mov_caja mc ON mc.codmov = dr.codmovdet AND mc.codcaj = r.codcaj  
										 WHERE dr.codemp ='".$this->ls_codemp."' 
						                   AND dr.codcaj ='".$param['codcaj']."'
						                   AND r.codmovrep = '".$param['codmov']."'
									   ) AS total  ";		
					    $sql_criterio = "   INNER JOIN  scc_reposiciones r ON r.codemp = dr.codemp
															  AND r.codrep = dr.codrep
											INNER JOIN scc_mov_caja mc ON mc.codmov = dr.codmovdet 
															AND mc.codcaj = r.codcaj 
											LEFT JOIN scc_conceptos td ON td.codconcaj = mc.codconcaj
						                    WHERE dr.codemp ='".$this->ls_codemp."' 
						                      AND dr.codcaj ='".$param['codcaj']."'
						                      AND r.codmovrep = '".$param['codmov']."' ";
					    break;
				
			}
								   
			$query_rs = "SELECT ".$campos." FROM scc_dt_reposiciones dr	 ".$sql_criterio ." ORDER BY dr.codmovdet ";			
			
			//echo $query_rs.'<br>';
			$rs_data=$this->io_sql->select($query_rs);	
			if($rs_data==false){				
				$metodo = 'ConsultaDetRep';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			return $rs_data;	
	
	}
	
	function InsertarRepo($datos=array()){
			
			if(!$datos['codcaj'] or !$datos['codmov'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de validación de Fecha de Movimiento 				           			    
							<br><b>METODO:</b> InsertarRepo ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			$this->codrep = $this->GeneraCodRep($datos);
			if($resp===false){return false;}
			
			$this->corelcaj = $this->GeneraCodRepCorel($datos);
			if($resp===false){return false;}
			
			
			$ls_sql =   "   INSERT INTO scc_reposiciones(codemp, codrep, codcaj, codmovrep,corelcaj )
							VALUES('".$this->ls_codemp."','".$this->codrep."','".$datos['codcaj']."','".$datos['codmov']."','".$this->corelcaj."')
					    ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'InsertarRepo';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			
			
			return true;
	}
	
	function InsertarDtRepo($datos=array()){
			
			if(!$datos['codcaj'] or !$this->codrep)
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de validación de Fecha de Movimiento 				           			    
							<br><b>METODO:</b> InsertarDtRepo ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			
			$ls_sql =   "   INSERT INTO scc_dt_reposiciones(codemp, codrep, codcaj, codmovdet)							
							SELECT mc.codemp,'".$this->codrep."','".$datos['codcaj']."',mc.codmov
							FROM scc_mov_caja mc
							INNER JOIN scc_conceptos c ON mc.codconcaj = c.codconcaj 
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov
							WHERE mc.codemp = '".$this->ls_codemp."'
							AND mc.codcaj = '".$datos['codcaj']."'
							AND mc.codtipmov = 'MOV'
							AND estrepo = '0'
			            ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'InsertarDtRepo';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			
			
			return true;
	}
	
	function GeneraCodRep($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder generar el código de movimiento. 				           			    
							<br><b>METODO:</b> GeneraCodRep ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
							
			$ls_sql =   "SELECT r.codrep 
			             FROM scc_reposiciones r  
						 WHERE r.codemp ='".$this->ls_codemp."' 
						 ".$criteriosql."						  
						 ORDER BY codrep DESC LIMIT 1 ";			
			
			//echo $query_rs.'<br>';
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'GeneraCodRep';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			$codrep = '0000000001';
			if(!$rs_data->RecordCount()){return $codrep;}
						
			$codrep = $rs_data->fields['codrep'];
			$codrep = (integer)$codrep;
			$codrep++;
			$codrep = str_pad($codrep, 10, "0", STR_PAD_LEFT);
			return $codrep;
	
	}
	
	function GeneraCodRepCorel($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder generar el código de movimiento. 				           			    
							<br><b>METODO:</b> GeneraCodRep ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
							
			$ls_sql =   "SELECT r.corelcaj 
			             FROM scc_reposiciones r  
						 WHERE r.codemp ='".$this->ls_codemp."' 
						 AND r.codcaj = '".$datos['codcaj']."'					  
						 ORDER BY codrep DESC LIMIT 1 ";			
			
			//echo $query_rs.'<br>';
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'GeneraCodRepCorel';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			$codrep = '0000000001';
			if(!$rs_data->RecordCount()){return $codrep;}
						
			$codrep = $rs_data->fields['codrep'];
			$codrep = (integer)$codrep;
			$codrep++;
			$codrep = str_pad($codrep, 10, "0", STR_PAD_LEFT);
			return $codrep;
	
	}
	
	function ConsultaBenefCaja($param=array()){
	
										   
			if(!$this->codcaj)
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la consulta del beneficiario responsable de la Caja Chica.				           			    
							<br><b>METODO:</b> ConsultaBenefCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			
			$ls_sql =   "   
							SELECT ced_bene FROM scc_cajachica WHERE codcaj = '".$this->codcaj."'
			            ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'ConsultaBenefCaja';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			if(!$rs_data->RecordCount()){				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> No se encontro información 				           			    
							<br><b>METODO:</b> ConsultaBenefCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;		
			}
			
			
			return $rs_data->fields['ced_bene'];
	
	}
	
	
	
	
	function BuscarDtSpgRep($datos=array()){
			
			if(!$this->codcaj or !$this->codrep)
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de validación de Fecha de Movimiento 				           			    
							<br><b>METODO:</b> BuscarDtSpgRep ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			
			$ls_sql =   "   
							SELECT distinct spg.spg_cuenta,mc.estcla,mc.codestpro1,mc.codestpro2,mc.codestpro3,
								   mc.codestpro4,mc.codestpro5, sum(mc.monto) AS monto
							FROM scc_dt_reposiciones dtr
							INNER JOIN scc_mov_caja mc ON mc.codmov = dtr.codmovdet
							INNER JOIN scc_conceptos c ON mc.codconcaj = c.codconcaj 
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov
							INNER JOIN spg_cuentas spg ON spg.spg_cuenta = c.spg_cuenta
													  AND spg.codestpro2 = mc.codestpro2
													  AND spg.codestpro3 = mc.codestpro3
													  AND spg.codestpro4 = mc.codestpro4
													  AND spg.codestpro5 = mc.codestpro5
													  AND spg.estcla = mc.estcla 
													  AND spg.codestpro1 = mc.codestpro1                       
							WHERE  mc.codcaj = '".$this->codcaj."'
							AND dtr.codrep = '".$this->codrep."'
							GROUP BY spg.spg_cuenta,mc.estcla,mc.codestpro1,mc.codestpro2,
									 mc.codestpro3,mc.codestpro4,mc.codestpro5
							ORDER BY mc.estcla,mc.codestpro1,mc.codestpro2,
									 mc.codestpro3,mc.codestpro4,mc.codestpro5,
									 spg.spg_cuenta
			            ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'BuscarDtSpgRep';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			if(!$rs_data->RecordCount()){				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> No se encontro información presupuestaria. 				           			    
							<br><b>METODO:</b> BuscarDtSpgRep ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;		
			}
			
			
			return $rs_data;
	}
	
	function BuscarDtScgRep($datos=array()){
			
			if(!$this->codcaj or !$this->codrep)
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación de validación de Fecha de Movimiento 				           			    
							<br><b>METODO:</b> BuscarDtScgRep ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
			
			
			$ls_sql =   "   
							SELECT spg.sc_cuenta, sum(mc.monto) AS monto
							FROM scc_dt_reposiciones dtr
							INNER JOIN scc_mov_caja mc ON mc.codmov = dtr.codmovdet
							INNER JOIN scc_conceptos c ON mc.codconcaj = c.codconcaj 
							INNER JOIN scc_tipmov tm ON tm.codtipmov = mc.codtipmov
							LEFT JOIN spg_cuentas spg ON spg.spg_cuenta = c.spg_cuenta
													  AND spg.codestpro1 = mc.codestpro1
													  AND spg.codestpro2 = mc.codestpro2
													  AND spg.codestpro3 = mc.codestpro3
													  AND spg.codestpro4 = mc.codestpro4
													  AND spg.codestpro5 = mc.codestpro5
													  AND spg.estcla = mc.estcla
							INNER JOIN scg_cuentas scg ON scg.sc_cuenta = spg.sc_cuenta                  
							WHERE  mc.codcaj = '".$this->codcaj."'
							AND dtr.codrep = '".$this->codrep."'
							AND scg.codemp='".$this->ls_codemp."' 
							GROUP BY spg.sc_cuenta
							ORDER BY spg.sc_cuenta			           
						 ";
						 
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'BuscarDtScgRep';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			if(!$rs_data->RecordCount()){				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> No se encontro información Contable. 				           			    
							<br><b>METODO:</b> BuscarDtScgRep ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;		
			}
			
			
			return $rs_data;
	}
	
	
	function VerificaCuentaSCG($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: VerificaCuentaSCG
		//         Access: public  
		//      Argumento: $datos
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de verificar que una Cuenta Contable Exista en el Plan de Cuentas
		//	   Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 22/01/2013							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ls_sql="SELECT sc_cuenta
				  FROM scg_cuentas
				 WHERE sc_cuenta='".$datos['sc_cuenta']."'";				 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false){				
			$metodo = 'VerificaCuentaSCG';
			$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
			$this->io_mensajes->message($mensaje);
			return false;				
		}
		
		if(!$rs_data->RecordCount()){
			$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> La Cuenta contable ".$datos['sc_cuenta']." no existe en el Plan de Cuentas !");
			return false;
		}
		
		return true;
				
	}
	
	function VerificaCuentaSPG($datos=array())
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: VerificaCuentaSPG
		//         Access: public  
		//      Argumento: $datos
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de verificar que una Cuenta Contable Exista en el Plan de Cuentas
		//	   Creado Por: Lic. Edgar A. Quintero
		// Fecha Creación: 22/01/2013							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ls_sql="SELECT spg_cuenta,sc_cuenta
				  FROM spg_cuentas spg
				 WHERE spg.spg_cuenta = '".$datos['spg_cuenta']."'
				   AND spg.codestpro1 = '".$datos['codestpro1']."'
				   AND spg.codestpro2 = '".$datos['codestpro2']."'
				   AND spg.codestpro3 = '".$datos['codestpro3']."'
				   AND spg.codestpro4 = '".$datos['codestpro4']."'
				   AND spg.codestpro5 = '".$datos['codestpro5']."'
				   AND spg.estcla = '".$datos['estcla']."' ";				 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false){				
			$metodo = 'VerificaCuentaSPG';
			$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
			$this->io_mensajes->message($mensaje);
			return false;				
		}
		
		$datSPG = $this->FormatLonCodEstPro($datos);
		
		if(!$rs_data->RecordCount()){
			$this->io_conexiones->mensajes_ajax("<b>ERROR:</b> La Cuenta preupuestaria ".$datos['spg_cuenta']." no existe en la estructura ".$this->CODESTPRO." !");
			return false;
		}
		
		return true;
				
	}
	
	
	
	function EncabezadoDtContable($parametro=array()){		
		
		$encabezado = '<p  style="text-align:center;"><table cellspacing="0" cellpadding="1" border="1">
					<thead>
						<tr bgcolor="'.$this->RepParam['encabezado2']['color_fondo'].'" color="'.$this->RepParam['encabezado2']['color_letra'].'" >
						  <td colspan="4" align="center">
							<font size="12">					            
								<b>DETALLE CONTABLE</b>								
							</font>
						  </td>
					   </tr>
						<tr bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" color="'.$this->RepParam['encabezado']['color_letra'].'" height="6">						
							<td align="center" colspan="2" width="'.$this->RepParam['ancho'][1].'" bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" ><font size="'.$this->RepParam['encabezado']['tamaño'].'"> COMPROMISO</font></td>
							<td align="center" colspan="2" width="'.$this->RepParam['ancho'][2].'" bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" ><font size="'.$this->RepParam['encabezado']['tamaño'].'"> CUENTA </font></td>
							<td align="center" colspan="2" width="'.$this->RepParam['ancho'][3].'" bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" ><font size="'.$this->RepParam['encabezado']['tamaño'].'"> DEBE </font></td>
							<td align="center" colspan="2" width="'.$this->RepParam['ancho'][4].'" bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" ><font size="'.$this->RepParam['encabezado']['tamaño'].'"> HABER </font></td>
						</tr>							
					</thead>';
					
		return $encabezado; 
	}
	
	function FinTablaDtContable($datos=array()){
			
			$fin_tabla = '   <tr>
							   <td colspan="2"  width="'.($this->RepParam['ancho'][1]+$this->RepParam['ancho'][2]).'" align="right">
								 <font size="'.$this->RepParam['encabezado']['tamaño'].'"> <b>TOTALES:</b></font>
							   </td>
							   <td  width="'.($this->RepParam['ancho'][3]).'" align="right">
								 <font size="'.$this->RepParam['encabezado']['tamaño'].'"> '.$datos['totdeb'].'</font>
							   </td>
							   <td width="'.($this->RepParam['ancho'][4]).'" align="right">
								 <font size="'.$this->RepParam['encabezado']['tamaño'].'"> '.$datos['tothab'].'</font>
							   </td>
							 </tr>							 
						   </table></p>';
			return $fin_tabla; 
	}
	
	function FilaDtContable($datos){			
	
			$filas .= '<tr bgcolor="'.$this->RepParam['fila']['color_fondo'].'" color="'.$this->RepParam['fila']['color_letra'].'">																							
							<td width="'.$this->RepParam['ancho'][1].'" align="center" bgcolor="'.$this->RepParam['fila']['color_fondo'].'"><font size="'.$this->RepParam['fila']['tamaño'].'">'.$datos['codcom'].'</font></td>
							<td width="'.$this->RepParam['ancho'][2].'" align="center" bgcolor="'.$this->RepParam['fila']['color_fondo'].'"><font size="'.$this->RepParam['fila']['tamaño'].'">'.$datos['sc_cuenta'].'</font></td>
							<td width="'.$this->RepParam['ancho'][3].'" align="right" bgcolor="'.$this->RepParam['fila']['color_fondo'].'"><font size="'.$this->RepParam['fila']['tamaño'].'">'.$datos['mondeb'].'</font></td>
							<td width="'.$this->RepParam['ancho'][4].'" align="right" bgcolor="'.$this->RepParam['fila']['color_fondo'].'"><font size="'.$this->RepParam['fila']['tamaño'].'">'.$datos['monhab'].'</font></td>
					 </tr>';
			
			return $filas;
	}
	
	
	function RepInfoContableMov($datos=array()){
			
			if(!$this->codcaj or !$this->codmov)
			{				
				$mensaje = 'VALIDACIÓN DE DATOS:'.'\n'.' Faltan datos para poder generar la información contable '.'\n'.
				           'METODO: RepInfoContableMov ';
				$this->io_mensajes->message($mensaje);																	
				return false;
			}
			
			$scg = $this->ConsultaScgMov($datos);		
			if($scg===false){return false;}			
			if(!$scg->RecordCount()){return 0;}
			$filasSGC = "";
			
			foreach($scg as $datosSCG){			
			        $datosSCG['mondeb'] = number_format($datosSCG['mondeb'],2,',','.');		
					$datosSCG['monhab'] = number_format($datosSCG['monhab'],2,',','.');		
					$datosSCG['totdeb'] = number_format($datosSCG['totdeb'],2,',','.');	
					$datosSCG['tothab'] = number_format($datosSCG['tothab'],2,',','.');		
					$filasSGC .= $this->FilaDtContable($datosSCG);			
			}
			
			$InfoContable = $this->EncabezadoDtContable().$filasSGC.$this->FinTablaDtContable($datosSCG);
			
			return $InfoContable;
	
	}
	
	
	function ConsultaScgMov($datos=array()){
	
		    if(!$this->codmov)
			{				
				$mensaje = 'VALIDACIÓN DE DATOS:'.'\n'.' Faltan datos para poder generar la información contable'.'\n'.
				           'METODO: ConsultaScgMov ';
				$this->io_mensajes->message($mensaje);																	
				return false;
			}
			
			$ls_sql =   "   
							   SELECT
							   ( CASE WHEN debhab='D' THEN monto            
								 ELSE NULL
								 END ) AS mondeb,
								( CASE WHEN debhab='H' THEN monto            
								 ELSE NULL
								 END ) AS monhab,
								scg.sc_cuenta,scg.codcom,scg.debhab,descripcion,
								(   SELECT sum(monto)
								FROM scc_dt_scg scg
								where scg.codmov = '".$this->codmov."'
								AND debhab = 'D') AS totdeb,
								(   SELECT sum(monto)
								FROM scc_dt_scg scg
								where scg.codmov = '".$this->codmov."'
								AND debhab = 'H') AS tothab
								FROM scc_dt_scg scg
								where scg.codmov = '".$this->codmov."'
								order by debhab,sc_cuenta;			
							
			            ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'ConsultaScgMov';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			return $rs_data;	
			
	
	}
	
	function ConsultaSpgMov($datos=array()){
	
		    if(!$this->codmov)
			{				
				$mensaje = 'VALIDACIÓN DE DATOS:'.'\n'.' Faltan datos para poder generar la información Presupuestaria'.'\n'.
				           'METODO: ConsultaSpgMov ';
				$this->io_mensajes->message($mensaje);																	
				return false;
			}
			
			$ls_sql =   "     SELECT *,(   SELECT sum(monto)
									FROM scc_dt_spg spg
									where spg.codmov = '".$this->codmov."') AS total
								FROM scc_dt_spg spg
								where spg.codmov = '".$this->codmov."'
								order by estcla,codestpro1, codestpro2, codestpro3, 
										 codestpro4, codestpro5, spg_cuenta
			            ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'ConsultaSpgMov';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			return $rs_data;	
			
	
	}
	
	function EncabezadoDtSpg($parametro=array()){		
		
		$encabezado = '<p  style="text-align:center;"><table cellspacing="0" cellpadding="1" border="1">
					<thead>
						<tr bgcolor="'.$this->RepParam['encabezado2']['color_fondo'].'" color="'.$this->RepParam['encabezado2']['color_letra'].'" >
						  <td colspan="4" align="center">
							<font size="12">					            
								<b>DETALLE DE PRESUPUESTO</b>								
							</font>
						  </td>
					   </tr>
						<tr bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" color="'.$this->RepParam['encabezado']['color_letra'].'" height="6">						
							<td align="center" colspan="2" width="'.$this->RepParam['ancho'][1].'" bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" ><font size="'.$this->RepParam['encabezado']['tamaño'].'"> COMPROMISO</font></td>
							<td align="center" colspan="2" width="'.$this->RepParam['ancho'][2].'" bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" ><font size="'.$this->RepParam['encabezado']['tamaño'].'"> ESTRUCTURA </font></td>
							<td align="center" colspan="2" width="'.$this->RepParam['ancho'][3].'" bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" ><font size="'.$this->RepParam['encabezado']['tamaño'].'"> CUENTA </font></td>
							<td align="center" colspan="2" width="'.$this->RepParam['ancho'][4].'" bgcolor="'.$this->RepParam['encabezado']['color_fondo'].'" ><font size="'.$this->RepParam['encabezado']['tamaño'].'"> TOTAL </font></td>
						</tr>							
					</thead>';
					
		return $encabezado; 
	}
	
	function FinTablaDtSpg($datos=array()){
			
			$fin_tabla = '   <tr>
							   <td colspan="2"  width="'.($this->RepParam['ancho'][1]+$this->RepParam['ancho'][2]+$this->RepParam['ancho'][3]).'" align="right">
								 <font size="'.$this->RepParam['encabezado']['tamaño'].'"> <b>TOTAL:</b></font>
							   </td>							  
							   <td width="'.($this->RepParam['ancho'][4]).'" align="right">
								 <font size="'.$this->RepParam['encabezado']['tamaño'].'"> '.$datos['total'].'</font>
							   </td>
							 </tr>							 
						   </table></p>';
			return $fin_tabla; 
	}
	
	function FilaDtSpg($datos){			
			
			$datos = $this->FormatLonCodEstPro($datos);
			
			$filas .= '<tr bgcolor="'.$this->RepParam['fila']['color_fondo'].'" color="'.$this->RepParam['fila']['color_letra'].'">																							
							<td width="'.$this->RepParam['ancho'][1].'" align="center" bgcolor="'.$this->RepParam['fila']['color_fondo'].'"><font size="'.$this->RepParam['fila']['tamaño'].'">'.$datos['codcom'].'</font></td>
							<td width="'.$this->RepParam['ancho'][2].'" align="center" bgcolor="'.$this->RepParam['fila']['color_fondo'].'"><font size="'.$this->RepParam['fila']['tamaño'].'">'.$this->CODESTPRO.'</font></td>
							<td width="'.$this->RepParam['ancho'][3].'" align="right" bgcolor="'.$this->RepParam['fila']['color_fondo'].'"><font size="'.$this->RepParam['fila']['tamaño'].'">'.$datos['spg_cuenta'].'</font></td>
							<td width="'.$this->RepParam['ancho'][4].'" align="right" bgcolor="'.$this->RepParam['fila']['color_fondo'].'"><font size="'.$this->RepParam['fila']['tamaño'].'">'.$datos['monto'].'</font></td>
					 </tr>';
			
			return $filas;
	}
	
	
	function RepInfoSpgMov($datos=array()){
			
			if(!$this->codcaj or !$this->codmov)
			{				
				$mensaje = 'VALIDACIÓN DE DATOS:'.'\n'.' Faltan datos para poder generar la información Presupuestaria '.'\n'.
				           'METODO: RepInfoSpgMov ';
				$this->io_mensajes->message($mensaje);																	
				return false;
			}
			
			$spg = $this->ConsultaSpgMov($datos);		
			if($spg===false){return false;}			
			if(!$spg->RecordCount()){return 0;}
			$filasSPC = "";
			
			foreach($spg as $datosSPG){			        
					$datosSPG['monto'] = number_format($datosSPG['monto'],2,',','.');		
					$datosSPG['total'] = number_format($datosSPG['total'],2,',','.');				
					$filasSPG .= $this->FilaDtSpg($datosSPG);			
			}
			
			$InfoSPG = $this->EncabezadoDtSPG().$filasSPG.$this->FinTablaDtSPG($datosSPG);
			
			return $InfoSPG;
	
	}
	
	function EncabezadoDtConcepto($parametro=array()){		
		
		$encabezado = '<p  style="text-align:center;"><table cellspacing="0" cellpadding="1" border="1">
					<thead>
						<tr bgcolor="'.$this->RepParamConc['encabezado2']['color_fondo'].'" color="'.$this->RepParamConc['encabezado2']['color_letra'].'" >
						  <td colspan="4" align="center">
							<font size="12">					            
								<b>DETALLE DE CONCEPTOS</b>								
							</font>
						  </td>
					   </tr>
						<tr bgcolor="'.$this->RepParamConc['encabezado']['color_fondo'].'" color="'.$this->RepParamConc['encabezado']['color_letra'].'" height="6">						
							<td align="center" colspan="2" width="'.$this->RepParamConc['ancho'][1].'" bgcolor="'.$this->RepParamConc['encabezado']['color_fondo'].'" ><font size="'.$this->RepParamConc['encabezado']['tamaño'].'"> CONCEPTO</font></td>
							<td align="center" colspan="2" width="'.$this->RepParamConc['ancho'][2].'" bgcolor="'.$this->RepParamConc['encabezado']['color_fondo'].'" ><font size="'.$this->RepParamConc['encabezado']['tamaño'].'"> ESTRUCTURA </font></td>
							<td align="center" colspan="2" width="'.$this->RepParamConc['ancho'][3].'" bgcolor="'.$this->RepParamConc['encabezado']['color_fondo'].'" ><font size="'.$this->RepParamConc['encabezado']['tamaño'].'"> CUENTA </font></td>
							<td align="center" colspan="2" width="'.$this->RepParamConc['ancho'][4].'" bgcolor="'.$this->RepParamConc['encabezado']['color_fondo'].'" ><font size="'.$this->RepParamConc['encabezado']['tamaño'].'"> MONTO </font></td>
						</tr>							
					</thead>';
					
		return $encabezado; 
	}
	
	function FinTablaDtConcepto($datos=array()){
			
			$fin_tabla = '   <tr>
							   <td colspan="2"  width="'.($this->RepParamConc['ancho'][1]+$this->RepParamConc['ancho'][2]+$this->RepParamConc['ancho'][3]).'" align="right">
								 <font size="'.$this->RepParamConc['encabezado']['tamaño'].'"> <b>TOTAL:</b></font>
							   </td>							  
							   <td width="'.($this->RepParamConc['ancho'][4]).'" align="right">
								 <font size="'.$this->RepParamConc['encabezado']['tamaño'].'"> '.$datos['total'].'</font>
							   </td>
							 </tr>							 
						   </table></p>';
			return $fin_tabla; 
	}
	
	function FilaDtConcepto($datos){			
			
			$datos = $this->FormatLonCodEstPro($datos);
			
			$filas .= '<tr bgcolor="'.$this->RepParamConc['fila']['color_fondo'].'" color="'.$this->RepParamConc['fila']['color_letra'].'">																							
							<td width="'.$this->RepParamConc['ancho'][1].'" align="left" bgcolor="'.$this->RepParamConc['fila']['color_fondo'].'"><font size="'.$this->RepParamConc['fila']['tamaño'].'">'."(".$datos['codconcaj'].') - '.$datos['denconcaj'].'</font></td>
							<td width="'.$this->RepParamConc['ancho'][2].'" align="center" bgcolor="'.$this->RepParamConc['fila']['color_fondo'].'"><font size="'.$this->RepParamConc['fila']['tamaño'].'">'.$this->CODESTPRO.'</font></td>
							<td width="'.$this->RepParamConc['ancho'][3].'" align="center" bgcolor="'.$this->RepParamConc['fila']['color_fondo'].'"><font size="'.$this->RepParamConc['fila']['tamaño'].'">'.$datos['spg_cuenta'].'</font></td>
							<td width="'.$this->RepParamConc['ancho'][4].'" align="right" bgcolor="'.$this->RepParamConc['fila']['color_fondo'].'"><font size="'.$this->RepParamConc['fila']['tamaño'].'">'.$datos['monto'].'</font></td>
					 </tr>';
			
			return $filas;
	}
	
	
	function RepInfoConceptoMov($datos=array()){
			
			if(!$this->codcaj or !$this->codmov)
			{				
				$mensaje = 'VALIDACIÓN DE DATOS:'.'\n'.' Faltan datos para poder generar la información Presupuestaria '.'\n'.
				           'METODO: RepInfoSpgMov ';
				$this->io_mensajes->message($mensaje);																	
				return false;
			}
			$datos['criterio'] = "por_codmov";
			$rep = $this->ConsultaDetRep($datos);		
			if($rep===false){return false;}			
			if(!$rep->RecordCount()){return 0;}
			$filasREP = "";
			
			foreach($rep as $datosREP){
			        $datosREP['monto'] = number_format($datosREP['monto'],2,',','.');		
					$datosREP['total'] = number_format($datosREP['total'],2,',','.');				
					$filasREP .= $this->FilaDtConcepto($datosREP);			
			}
			
			$InfoREP = $this->EncabezadoDtConcepto().$filasREP.$this->FinTablaDtConcepto($datosREP);
			
			return $InfoREP;
	
	}
	
	function BuscaCierreCaja($datos=array()){
			
			if(!$datos['codcaj'])
			{				
				$mensaje = '<b>VALIDACIÓN DE DATOS:</b><br> Faltan datos para poder realizar la operación			           			    
							<br><b>METODO:</b> BuscaCierreCaja ';
				$this->io_conexiones->mensajes_ajax($mensaje);																	
				return false;
			}
									
			$ls_sql =   "   SELECT *
							FROM scc_mov_caja mc							
							WHERE mc.codemp = '".$this->ls_codemp."'
							AND mc.codcaj = '".$datos['codcaj']."'
							AND mc.codtipmov = 'CIE'							
			            ";
			
			$rs_data=$this->io_sql->select($ls_sql);	
			if($rs_data==false){				
				$metodo = 'BuscaCierreCaja';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR-></b><br>'.$this->io_sql->message;					
				$this->io_mensajes->message($mensaje);
				return false;				
			}
			
			if($rs_data->RecordCount()){return true;}
			
			return false;
	}
	
}//////////////////////////////////////////////////////////////******* FIN CLASE CAJA CHICA *******/////////////////////////////////////////////////////////


?>
