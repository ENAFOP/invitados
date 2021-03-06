<?php
/**
 * Implementation of UpdateDocument view
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
 * Class which outputs the html page for UpdateDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UpdateDocument extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$strictformcheck = $this->params['strictformcheck'];
		$dropfolderdir = $this->params['dropfolderdir'];
		header('Content-Type: application/javascript');
		$this->printDropFolderChooserJs("form1");
		$this->printSelectPresetButtonJs();
		$this->printInputPresetButtonJs();
		$this->printCheckboxPresetButtonJs();
?>
function checkForm()
{
	msg = new Array();
<?php if($dropfolderdir) { ?>
	if ($("#userfile").val() == "" && $("#dropfolderfileform1").val() == "") msg.push("<?php printMLText("js_no_file");?>");
<?php } else { ?>
	if ($("#userfile").val() == "") msg.push("<?php printMLText("js_no_file");?>");
<?php } ?>
<?php
	if ($strictformcheck) {
	?>
	if ($("#comment").val() == "") msg.push("<?php printMLText("js_no_comment");?>");
<?php
	}
?>
	if (msg != "")
	{
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			_timeout: 1500,
  	});
		return false;
	}
	else
		return true;
}

$(document).ready( function() {
/*
	$('body').on('submit', '#form1', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
*/
	jQuery.validator.addMethod("alternatives", function(value, element, params) {
		if(value == '' && params.val() == '')
			return false;
		return true;
	}, "<?php printMLText("js_no_file");?>");
	$("#form1").validate({
		invalidHandler: function(e, validator) {
			noty({
				text:  (validator.numberOfInvalids() == 1) ? "<?php printMLText("js_form_error");?>".replace('#', validator.numberOfInvalids()) : "<?php printMLText("js_form_errors");?>".replace('#', validator.numberOfInvalids()),
				type: 'error',
				dismissQueue: true,
				layout: 'topRight',
				theme: 'defaultTheme',
				timeout: 1500,
			});
		},
		rules: {
			userfile: {
				alternatives: $('#dropfolderfileform1')
			},
			dropfolderfileform1: {
				 alternatives: $('#userfile')
			}
		},
		messages: {
			comment: "<?php printMLText("js_no_comment");?>",
		},
		errorPlacement: function( error, element ) {
			if ( element.is( ":file" ) ) {
				error.appendTo( element.parent().parent().parent());
console.log(element);
			} else {
				error.appendTo( element.parent());
			}
		}
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$strictformcheck = $this->params['strictformcheck'];
		$enablelargefileupload = $this->params['enablelargefileupload'];
		$enableadminrevapp = $this->params['enableadminrevapp'];
		$enableownerrevapp = $this->params['enableownerrevapp'];
		$enableselfrevapp = $this->params['enableselfrevapp'];
		$dropfolderdir = $this->params['dropfolderdir'];
		$workflowmode = $this->params['workflowmode'];
		$presetexpiration = $this->params['presetexpiration'];
		$documentid = $document->getId();

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))), "skin-blue sidebar-mini");

		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();		

		echo $this->getDefaultFolderPathHTML($folder, true, $document);

		//// Document content ////
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-12\">";

		$this->startBoxPrimary(getMLText("update_document"));

		if ($document->isLocked()) {

			$lockingUser = $document->getLockingUser();

			print "<div class=\"alert alert-warning\">";
			
			printMLText("update_locked_msg", array("username" => htmlspecialchars($lockingUser->getFullName()), "email" => $lockingUser->getEmail()));
			
			if ($lockingUser->getID() == $user->getID())
				printMLText("unlock_cause_locking_user");
			else if ($document->getAccessMode($user) == M_ALL)
				printMLText("unlock_cause_access_mode_all");
			else
			{
				printMLText("no_update_cause_locked");
				print "</div>";
				$this->contentEnd();
				$this->htmlEndPage();
				exit;
			}

			print "</div>";
		}

		$latestContent = $document->getLatestContent();
		$reviewStatus = $latestContent->getReviewStatus();
		$approvalStatus = $latestContent->getApprovalStatus();
		if($workflowmode == 'advanced') {
			if($status = $latestContent->getStatus()) {
				if($status["status"] == S_IN_WORKFLOW) {
					$this->warningMsg(getMLText("doc_is_in_workflow"));
				}
			}
		}

		$msg = getMLText("max_upload_size").": ".ini_get( "upload_max_filesize");
		/*if($enablelargefileupload) {
			$msg .= "<p>".sprintf(getMLText('link_alt_updatedocument'), "out.AddMultiDocument.php?folderid=".$folder->getID()."&showtree=".showtree())."</p>";
		}
		$this->warningMsg($msg);*/
		$this->contentContainerStart();
?>
<div class="table-responsive">
<form action="../op/op.UpdateDocument.php" enctype="multipart/form-data" method="post" name="form1" id="form1">
	<input type="hidden" name="documentid" value="<?php print $document->getID(); ?>">
	<table class="table-condensed">
	
		<tr>
			<td><?php printMLText("local_file");?>:</td>
			<td><!-- input type="File" name="userfile" size="60" -->
<?php
	$this->printFileChooser('userfile', false);
?>
			</td>
		</tr>
<?php if($dropfolderdir) { ?>
		<tr>
			<td><?php printMLText("dropfolder_file");?>:</td>
			<td><?php $this->printDropFolderChooserHtml("form1");?></td>
		</tr>
<?php } ?>
		<!-- <tr>
			<td><?php printMLText("comment");?>:</td>
			<td class="standardText">
				<textarea name="comment" rows="4" cols="80"<?php echo $strictformcheck ? ' required' : ''; ?>></textarea>
			</td>
		</tr> -->
<?php
			if($presetexpiration) {
				if(!($expts = strtotime($presetexpiration)))
					$expts = time();
			} else {
				$expts = time();
			}
?>
		<tr>
			
				  <input type="hidden" name="expires" value="false"><br>
        
			
		</tr>
<?php
	$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_documentcontent, SeedDMS_Core_AttributeDefinition::objtype_all));
	if($attrdefs) {
		foreach($attrdefs as $attrdef) {
			$arr = $this->callHook('editDocumentContentAttribute', null, $attrdef);
			if(is_array($arr)) {
				echo $txt;
				echo "<tr>";
				echo "<td>".$arr[0].":</td>";
				echo "<td>".$arr[1]."</td>";
				echo "</tr>";
			} else {
?>
    <tr>
	    <td><?php echo htmlspecialchars($attrdef->getName()); ?>:</td>
			<td><?php $this->printAttributeEditField($attrdef, '') ?>
<?php
			if($latestContent->getAttributeValue($attrdef)) {
				switch($attrdef->getType()) {
				case SeedDMS_Core_AttributeDefinition::type_string:
				case SeedDMS_Core_AttributeDefinition::type_date:
				case SeedDMS_Core_AttributeDefinition::type_int:
				case SeedDMS_Core_AttributeDefinition::type_float:
					$this->printInputPresetButtonHtml('attributes_'.$attrdef->getID(), $latestContent->getAttributeValue($attrdef), $attrdef->getValueSetSeparator());
					break;
				case SeedDMS_Core_AttributeDefinition::type_boolean:
					$this->printCheckboxPresetButtonHtml('attributes_'.$attrdef->getID(), $latestContent->getAttributeValue($attrdef));
					break;
				}
//				print_r($latestContent->getAttributeValue($attrdef));
			}
?></td>
    </tr>
<?php
			}
		}
	}
	if($workflowmode == 'traditional' || $workflowmode == 'traditional_only_approval') 
	{
		// Retrieve a list of all users and groups that have review / approve
		// privileges.
		$docAccess = $folder->getReadAccessList($enableadminrevapp, $enableownerrevapp);
		if($workflowmode != 'traditional_only_approval') {
?>
		<tr>
			<td colspan="2">
				<?php $this->contentSubHeading(getMLText("assign_reviewers")); ?>
      </td>
    </tr>
    <tr>
      <td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
      </td>
			<td>
        <select id="IndReviewer" class="chzn-select span9" name="indReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_reviewers'); ?>" data-no_results_text="<?php printMLText('unknown_owner'); ?>">
<?php
				$res=$user->getMandatoryReviewers();
				foreach ($docAccess["users"] as $usr) {
					if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 
					$mandatory=false;
					foreach ($res as $r) if ($r['reviewerUserID']==$usr->getID()) $mandatory=true;

					if ($mandatory) print "<option disabled=\"disabled\" value=\"".$usr->getID()."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
					else print "<option value=\"".$usr->getID()."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
				}
?>
				</select>
<?php
				$tmp = array();
				foreach($reviewStatus as $r) {
					if($r['type'] == 0) {
					 	if($res) {
							$mandatory=false;
							foreach ($res as $rr)
								if ($rr['reviewerUserID']==$r['required']) {
									$mandatory=true;
								}
							if(!$mandatory)
								$tmp[] = $r['required'];
						} else {
							$tmp[] = $r['required'];
						}
					}
				}
				if($tmp) {
					$this->printSelectPresetButtonHtml("IndReviewer", $tmp);
				}
				/* List all mandatory reviewers */
				if($res) {
					$tmp = array();
					foreach ($res as $r) {
						if($r['reviewerUserID'] > 0) {
							$u = $dms->getUser($r['reviewerUserID']);
							$tmp[] =  htmlspecialchars($u->getFullName().' ('.$u->getLogin().')');
						}
					}
					if($tmp) {
						echo '<div class="mandatories"><span>'.getMLText('mandatory_reviewers').':</span> ';
						echo implode(', ', $tmp);
						echo "</div>\n";
					}
				}
				/* Check for mandatory reviewer without access */
				foreach($res as $r) {
					if($r['reviewerUserID']) {
						$hasAccess = false;
						foreach ($docAccess["users"] as $usr) {
							if ($r['reviewerUserID']==$usr->getID())
								$hasAccess = true;
						}
						if(!$hasAccess) {
							$noAccessUser = $dms->getUser($r['reviewerUserID']);
							echo "<div class=\"alert alert-warning\">".getMLText("mandatory_reviewer_no_access", array('user'=>htmlspecialchars($noAccessUser->getFullName()." (".$noAccessUser->getLogin().")")))."</div>";
						}
					}
				}
?>
      </td>
    </tr>
    <tr>
      <td>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
      </td>
			<td>
        <select id="GrpReviewer" class="chzn-select span9" name="grpReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_reviewers'); ?>" data-no_results_text="<?php printMLText('unknown_group'); ?>">
<?php
				foreach ($docAccess["groups"] as $grp) {
				
					$mandatory=false;
					foreach ($res as $r) if ($r['reviewerGroupID']==$grp->getID()) $mandatory=true;	

					if ($mandatory) print "<option value=\"".$grp->getID()."\" disabled=\"disabled\">".htmlspecialchars($grp->getName())."</option>";
					else print "<option value=\"".$grp->getID()."\">".htmlspecialchars($grp->getName())."</option>";
				}
?>
			</select>
<?php
				$tmp = array();
				foreach($reviewStatus as $r) {
					if($r['type'] == 1) {
						if($res) {
							$mandatory=false;
							foreach ($res as $rr)
								if ($rr['reviewerGroupID']==$r['required']) {
									$mandatory=true;
								}
							if(!$mandatory)
								$tmp[] = $r['required'];
						} else {
							$tmp[] = $r['required'];
						}
					}
				}
				if($tmp) {
					$this->printSelectPresetButtonHtml("GrpReviewer", $tmp);
				}
				/* List all mandatory groups of reviewers */
				if($res) {
					$tmp = array();
					foreach ($res as $r) {
						if($r['reviewerGroupID'] > 0) {
							$u = $dms->getGroup($r['reviewerGroupID']);
							$tmp[] =  htmlspecialchars($u->getName());
						}
					}
					if($tmp) {
						echo '<div class="mandatories"><span>'.getMLText('mandatory_reviewergroups').':</span> ';
						echo implode(', ', $tmp);
						echo "</div>\n";
					}
				}

				/* Check for mandatory reviewer group without access */
				foreach($res as $r) {
					if ($r['reviewerGroupID']) {
						$hasAccess = false;
						foreach ($docAccess["groups"] as $grp) {
							if ($r['reviewerGroupID']==$grp->getID())
								$hasAccess = true;
						}
						if(!$hasAccess) {
							$noAccessGroup = $dms->getGroup($r['reviewerGroupID']);
							echo "<div class=\"alert alert-warning\">".getMLText("mandatory_reviewergroup_no_access", array('group'=>htmlspecialchars($noAccessGroup->getName())))."</div>";
						}
					}
				}
?>
      </td>
		</tr>
<?php } ?>
    <tr>
			<td colspan=2>
				<?php $this->contentSubHeading(getMLText("assign_approvers")); ?>	
      </td>
    </tr>
    <tr>
      <td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
      </td>
      <td>
        <select id="IndApprover" class="chzn-select span9" name="indApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_approvers'); ?>" data-no_results_text="<?php printMLText('unknown_owner'); ?>">
<?php
				$res=$user->getMandatoryApprovers();
				foreach ($docAccess["users"] as $usr) {
					if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 

					$mandatory=false;
					foreach ($res as $r) if ($r['approverUserID']==$usr->getID()) $mandatory=true;
					
					if ($mandatory) print "<option value=\"". $usr->getID() ."\" disabled='disabled'>". htmlspecialchars($usr->getFullName())."</option>";
					else print "<option value=\"". $usr->getID() ."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
				}
?>
        </select>
<?php
				$tmp = array();
				foreach($approvalStatus as $r) {
					if($r['type'] == 0) {
						if($res) {
							$mandatory=false;
							foreach ($res as $rr)
								if ($rr['approverUserID']==$r['required']) {
									$mandatory=true;
								}
							if(!$mandatory)
								$tmp[] = $r['required'];
						} else {
							$tmp[] = $r['required'];
						}
					}
				}
				if($tmp) {
					$this->printSelectPresetButtonHtml("IndApprover", $tmp);
				}
				/* List all mandatory approvers */
				if($res) {
					$tmp = array();
					foreach ($res as $r) {
						if($r['approverUserID'] > 0) {
							$u = $dms->getUser($r['approverUserID']);
							$tmp[] =  htmlspecialchars($u->getFullName().' ('.$u->getLogin().')');
						}
					}
					if($tmp) {
						echo '<div class="mandatories"><span>'.getMLText('mandatory_approvers').':</span> ';
						echo implode(', ', $tmp);
						echo "</div>\n";
					}
				}

				/* Check for mandatory approvers without access */
				foreach($res as $r) {
					if($r['approverUserID']) {
						$hasAccess = false;
						foreach ($docAccess["users"] as $usr) {
							if ($r['approverUserID']==$usr->getID())
								$hasAccess = true;
						}
						if(!$hasAccess) {
							$noAccessUser = $dms->getUser($r['approverUserID']);
							echo "<div class=\"alert alert-warning\">".getMLText("mandatory_approver_no_access", array('user'=>htmlspecialchars($noAccessUser->getFullName()." (".$noAccessUser->getLogin().")")))."</div>";
						}
					}
				}
?>
      </td>
    </tr>
      <td>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
      </td>
      <td>
        <select id="GrpApprover" class="chzn-select form-control" name="grpApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_approvers'); ?>" data-no_results_text="<?php printMLText('unknown_group'); ?>">
<?php
				foreach ($docAccess["groups"] as $grp) {
				
					$mandatory=false;
					foreach ($res as $r) if ($r['approverGroupID']==$grp->getID()) $mandatory=true;	

					if ($mandatory) print "<option value=\"". $grp->getID() ."\" disabled=\"disabled\">".htmlspecialchars($grp->getName())."</option>";
					else print "<option value=\"". $grp->getID() ."\">".htmlspecialchars($grp->getName())."</option>";

				}
?>
        </select>
<?php
				$tmp = array();
				foreach($approvalStatus as $r) {
					if($r['type'] == 1) {
						if($res) {
							$mandatory=false;
							foreach ($res as $rr)
								if ($rr['approverGroupID']==$r['required']) {
									$mandatory=true;
								}
							if(!$mandatory)
								$tmp[] = $r['required'];
						} else {
							$tmp[] = $r['required'];
						}
					}
				}
				if($tmp) {
					$this->printSelectPresetButtonHtml("GrpApprover", $tmp);
				}
				/* List all mandatory groups of approvers */
				if($res) {
					$tmp = array();
					foreach ($res as $r) {
						if($r['approverGroupID'] > 0) {
							$u = $dms->getGroup($r['approverGroupID']);
							$tmp[] =  htmlspecialchars($u->getName());
						}
					}
					if($tmp) {
						echo '<div class="mandatories"><span>'.getMLText('mandatory_approvergroups').':</span> ';
						echo implode(', ', $tmp);
						echo "</div>\n";
					}
				}

				/* Check for mandatory approver groups without access */
				foreach($res as $r) {
					if ($r['approverGroupID']) {
						$hasAccess = false;
						foreach ($docAccess["groups"] as $grp) {
							if ($r['approverGroupID']==$grp->getID())
								$hasAccess = true;
						}
						if(!$hasAccess) {
							$noAccessGroup = $dms->getGroup($r['approverGroupID']);
							echo "<div class=\"alert alert-warning\">".getMLText("mandatory_approvergroup_no_access", array('group'=>htmlspecialchars($noAccessGroup->getName())))."</div>";
						}
					}
				}
?>
			</td>
			</tr>
		<tr>
			<td colspan="2"><div class="alert"><?php printMLText("add_doc_reviewer_approver_warning")?></div></td>
		</tr>
<?php
	} else {
?>
		<tr>	
     
      <td>
<?php
				$mandatoryworkflows = $user->getMandatoryWorkflows();
				if($mandatoryworkflows) {
					if(count($mandatoryworkflows) == 1) {
?>
				<?php echo htmlspecialchars($mandatoryworkflows[0]->getName()); ?>
				<input type="hidden" name="workflow" value="<?php echo $mandatoryworkflows[0]->getID(); ?>">
<?php
					} else {
?>
        <select class="_chzn-select-deselect form-control" name="workflow" data-placeholder="<?php printMLText('select_workflow'); ?>">
<?php
					$curworkflow = $latestContent->getWorkflow();
					foreach ($mandatoryworkflows as $workflow) {
						print "<option value=\"".$workflow->getID()."\"";
						if($curworkflow && $curworkflow->getID() == $workflow->getID())
							echo " selected=\"selected\"";
						print ">". htmlspecialchars($workflow->getName())."</option>";
					}
?>
        </select>
<?php
					}
				} else {
?>

<?php
				}
?>
      </td>
    </tr>
		<tr>	
      <td colspan="2">
			
      </td>
		</tr>	
<?php
	}
?>
		<tr>
			<td><button type="submit" class="btn btn-info"><i class="fa fa-save"></i> <?php printMLText("update_document")?></button></td>
			<td></td>
		</tr>
	</table>
</form>
</div>
<?php
		$this->endsBoxPrimary();

		echo "</div>"; 
		echo "</div>"; 
		echo "</div>"; 
		echo "</div>"; // Ends row

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
