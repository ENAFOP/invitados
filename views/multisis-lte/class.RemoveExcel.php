<?php
/**
 * Implementation of RemoveExcel view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for RemoveExcel view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveExcel extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$excelname = $this->params['excelfile'];

		$this->htmlStartPage(getMLText("admin_tools"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
    <?php 

		$this->startBoxDanger(getMLText("excel_remove"));
?>
<form action="../op/op.RemoveExcel.php" name="form1" method="post">
	<input type="Hidden" name="excelname" value="<?php echo htmlspecialchars($excelname); ?>">
  <?php echo createHiddenFieldWithKey('removeexcel'); ?>
	<p><?php printMLText("confirm_rm_excel", array ("excelname" => htmlspecialchars($excelname)));?></p>
	<p><button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> <?php printMLText("excel_remove");?></button></p>
</form>
<?php
		$this->endsBoxDanger();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
