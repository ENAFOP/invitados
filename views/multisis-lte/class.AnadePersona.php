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
function imprimirGrupos()
{
  //LOS DEPARTAMENTOS LEIDOS DE LA BD
  $settings = new Settings(); //acceder a parámetros de settings.xml con _antes
    $driver=$settings->_dbDriver;
    $host=$settings->_dbHostname;
    $user=$settings->_dbUser;
    $pass=$settings->_dbPass;
    $base=$settings->_dbDatabase;
  $manejador=new SeedDMS_Core_DatabaseAccess($driver,$host,$user,$pass,$base);
  $estado=$manejador->connect();
  //echo "Conectado: ".$estado;
  if($estado!=1)
  {
    echo "out.AnadePersona.php[]Error: no se pudo conectar a la BD";
  } 
  //query de consulta:
  $miQuery="SELECT nombre,id FROM app_grupo";
  //echo "mi query: ".$miQuery;
  $resultado=$manejador->getResultArray($miQuery);

  ////////////////////// EL SELECT PASARÁ EL ID DEL GRUPO COMO VALUE AL PROCESAR PERSONA
  echo " <select class=\"form-control chzn-select\" id=\"idGrupo\" name=\"idGrupo\">";

  echo "<option disabled selected value>Seleccione un grupo de la lista</option>";
  foreach ($resultado as $a) 
  {
  
        $nombreGrupo=$a['nombre']; 
        $idGrupo=$a['id'];
       echo "<option value=\"".$idGrupo."\">".$nombreGrupo."</option>";
  }

  echo "</select>";
}// fin de imprimir departamentos
class SeedDMS_View_AnadePersona extends SeedDMS_Bootstrap_Style 
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

		$db = $dms->getDB();
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);

		$this->htmlStartPage("Añadir persona ", "skin-blue sidebar-mini  sidebar-collapse");
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
  
 $this->startBoxPrimary("Añadir persona nueva");
$this->contentContainerStart();
//////INICIO MI CODIGO
?>
<!-- ***************** UNA FILA TRES COLUMNAS *********************-->
<div class="row">
        <div class="col-md-3">

        </div> <!-- FIN DE COLUMNA 1 -->

        <div class="col-md-6">
        		<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Datos de persona</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
      <form class="form-horizontal" name="formularioPersona" id="formularioPersona" action="../out/out.ProcesarPersona.php" method="POST" enctype="multipart/form-data>
              <div class="box-body">

                <div class="form-group">
                  <label for="nombrePersona" class="col-sm-2 control-label">Nombre</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="nombrePersona" name="nombrePersona" placeholder="Nombre" required>
                  </div>
                </div>

                 <div class="form-group">
                  <label for="sexo" class="col-sm-2 control-label">Sexo</label>

                  <div class="col-sm-10">
                     <select class="form-control" id="sexo" name="sexo">
                    <option>Mujer</option>
                    <option>Hombre</option>
                  </select>
                  </div>
                </div> 
                          
                 <div class="form-group">
                  <label for="entidad" class="col-sm-2 control-label">Entidad</label>

                  <div class="col-sm-10">
                     <textarea class="form-control" id="entidad" name="entidad" rows="3" placeholder="Nombre de su institución" required></textarea>
                  </div>
                </div> 

                <div class="form-group">
                  <label for="cargos" class="col-sm-2 control-label">Cargo</label>

                  <div class="col-sm-10">
                     <textarea class="form-control" id="cargo" name="cargo" rows="3" placeholder="su puesto o cargo" required></textarea>
                  </div>
                </div> 


                <div class="form-group">
                  <label for="grupo" class="col-sm-2 control-label">Grupo al que pertenece</label>

                  <div class="col-sm-10">
                    <?php imprimirGrupos();?>
                  </div>
                </div>

                <div class="form-group">
                  <label for="cargos" class="col-sm-2 control-label">Correos electrónicos</label>

                  <div class="col-sm-10">
                       <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                          <input id="correo1" name="correo1" type="email" class="form-control" placeholder="Correo principal..." required>
                      </div>

                      <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                          <input id="correo2" name="correo2" type="email" class="form-control" placeholder="Correo 2 si tiene...">
                      </div>

                      <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                          <input id="correo3" name="correo3" type="email" class="form-control" placeholder="Correo 3 si tiene...">
                      </div>

                  
                  </div>
                </div> 


              <!-- /.box-body -->
              <div class="box-footer">
                <button type="reset" class="btn btn-default">Borrar campos</button>
                <button type="submit" class="btn btn-info pull-right">Agregar persona</button>
              </div>
              <!-- /.box-footer -->
            </form>
          </div>




        </div> <!-- FIN DE COLUMNA 2 -->


        <div class="col-md-3">

        </div> <!-- FIN DE COLUMNA 3 -->
</div> <!-- FIN DE FILA -->


<?php
 //////FIN MI CODIGO                 
$this->contentContainerEnd();


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
