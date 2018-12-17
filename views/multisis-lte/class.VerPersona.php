<?php
/**
 * Implementation of MyDocuments view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx> DMS with modifications of José Mario López Leiva
 * @copyright  Copyright (C) 2017 José Mario López Leiva
 *             marioleiva2011@gmail.com    
 				San Salvador, El Salvador, Central America

 *             
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");


/**
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");



/**
 * Class which outputs the html page for MyDocuments view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
 /**
 Función que muestra los documentos próximos a caducar de todos los usuarios
 mostrarTodosDocumentos(lista_usuarios,dias)
 -dias: documentos que van a caducar dentro de cúantos días
 */
 function getDatoPersona($dms,$campo,$idPersona)
	 {
	 	$res=true;
		$db = $dms->getDB();
		$consultar = "SELECT $campo FROM app_invitado WHERE id=$idPersona;";
		//echo "Consultar: ".$consultar;
		$res1 = $db->getResultArray($consultar);
		return $res1[0][$campo];
	 }
class SeedDMS_View_VerPersona extends SeedDMS_Bootstrap_Style 
{
 /**
 Método que muestra los documentos próximos a caducar sólo de 
 **/
	

	function show() 
	{ /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$orderby = $this->params['orderby'];
		$showInProcess = $this->params['showinprocess'];
		$cachedir = $this->params['cachedir'];
		$workflowmode = $this->params['workflowmode'];
		$previewwidth = $this->params['previewWidthList'];
		$timeout = $this->params['timeout'];
		$idPersona = $this->params['idPersona'];

		$db = $dms->getDB();
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);

		$consultar = "SELECT * FROM app_invitado where id=$idPersona;";
        $res1 = $db->getResultArray($consultar);
        $nombrecito=$res1[0]['nombre'];
		$this->htmlStartPage("Viendo datos de $nombrecito", "skin-blue sidebar-mini  sidebar-collapse");

		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		//$this->contentContainerStart("hoa");
		$this->contentStart();
		
          
		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
      

    <?php
    //en este bloque php va "mi" código
  
  $this->startBoxPrimary("Viendo perfil de persona "."<b>".$nombrecito."</b>");
//$this->contentContainerStart();
//////INICIO MI CODIGO
?>
<div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-info"></i> En esta pantalla puede los datos generales de la persona</h4>
               Así como editar esos datos, ver los grupos a los que la persona pertenece y los eventos organizados por la ENAFOP a los cuales ha asistido.
                <br>
              </div>
<?php
 //////FIN MI CODIGO                 
//$this->contentContainerEnd();
?>

 <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab">General</a></li>
              <li><a href="#tab_2" data-toggle="tab">Contacto</a></li>
              <li><a href="#tab_3" data-toggle="tab">Eventos en que ha participado</a></li>
              <li><a href="#tab_4" data-toggle="tab">Historial de edición</a></li>

            </ul>

            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                 <div class="row">
        <div class="col-md-6">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Datos generales</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            		<b>Nombre completo: </b> 
                <p><?php print getDatoPersona($dms,"nombre",$idPersona) ?></p>

                 <b>Sexo: </b> 
                <p><?php print getDatoPersona($dms,"sexo",$idPersona) ?></p>    

                <b>Cargo: </b> 
                <p><?php print getDatoPersona($dms,"cargo",$idPersona) ?></p>  

                <b>Entidad: </b> 
                <p><?php print getDatoPersona($dms,"entidad",$idPersona) ?></p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col (left) -->
        <div class="col-md-6">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Grupos a los que pertenece</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
 				<?php  
 				$consultarGrupos = "SELECT * FROM app_agrupamiento where id_persona=$idPersona;";
 				//echo "Consultar grupos_ ".$consultarGrupos;
        		$res2 = $db->getResultArray($consultarGrupos);
        		echo "<ul>";
        		foreach ($res2 as $grupo) 
        		{
        			$idGrupo=$res2[0]['id_grupo'];
        			$consultarNombreGrupo = "SELECT nombre FROM app_grupo where id=$idGrupo;";
        			$res3 = $db->getResultArray($consultarNombreGrupo);
        			echo "<li>";
        			print $res3[0]['nombre'];
        			echo "</li>";
        		}
        		echo "</ul>";
 				?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col (right) -->
      </div>
      <!-- /.row -->
      


              </div>
              <!-- /.CORREOS -->
              <div class="tab-pane" id="tab_2">
              	<?php  
 				$consultarCorreos = "SELECT * FROM app_correos where id_persona=$idPersona;";
 				//echo "Consultar grupos_ ".$consultarGrupos;
        		$res4 = $db->getResultArray($consultarCorreos);
        		echo "<ul>";
        		$cont=1;
        		foreach ($res4 as $mail) 
        		{
        			echo "<li>";
        			print "Correo $cont: ".$mail['correo'];
        			echo "</li>";
        			$cont++;
        		}
        		echo "</ul>";
 				?>
               
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_3">
              
              </div>

              	<!-- /.HISTORIAL DE EDICIÓN -->
                 <div class="tab-pane" id="tab_4">
                 		<?php  
 				$consultarHisto= "SELECT * FROM app_historial where id_invitado=$idPersona;";
 				//echo "Consultar grupos_ ".$consultarGrupos;
        		$res5 = $db->getResultArray($consultarHisto);
        		echo '<ul class="timeline">';
        		foreach ($res5 as $histo) 
        		{
        			//fecha del cambio
        			 echo '<li class="time-label">';
				         echo '<span class="bg-red">';
				            echo $histo['fecha'];
				         echo '</span>';
   					 echo ' </li>';
        			////////INICIO ITEM

        			echo '<li>      
        <i class="fa fa-envelope bg-blue"></i>
        <div class="timeline-item">
            <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>';
            $nombreUsuario=$dms->getUser($histo['id_usuario'])->getFullName();
            $usuario=$dms->getUser($histo['id_usuario'])->getLogin();
            echo "<h3 class=\"timeline-header\"><a href=\"#\">".$nombreUsuario." (el usuario es  <b> ".$usuario.") </b></a></h3>";
            echo '<div class="timeline-body">';
            /////aqui va el nucleo
        			print "Cambio realizado: ".$histo['cambio'];              
           echo '</div>

        </div>
    </li>';

        			//////FIN ITEM TIMELINE
        		}
        		echo "</ul>";
 				?>
              
                  </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->


<?php

$this->endsBoxPrimary();
     ?>
	     </div>
		</div>
		</div>

		<?php	
		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		//$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
