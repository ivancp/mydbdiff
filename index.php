<?php
/*
 MyDBDiff 
 written by Ivan Cachicatari
 Date: sat dec 25 08:08:24 EST 2010a
 Last-modify: dec 25 14:12:05 PET 2010
 Version 0.1
 
 Please send your comments to ivancp@latindevelopers.com

 This is a pre beta version, use under your own risk.
 */
require "mydbdiff.php";

$diff = new MyDBDiff();
$diff->loadCookieParams();
?>
<html>
<head>
<title>Mydbdiff tool <?=$version?></title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="css/prettify.css" rel="stylesheet">
</head>
<body>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
  <div class="col-md-10"><h2>MyDBdiff <small>beta</small></h2></div>
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
  <div class="col-md-10">




	
    <div id="rootwizard" class="tabbable tabs-left">
    	<ul>
    	  	<li><a href="#tab1" data-toggle="tab">Database settings</a></li>
    		<li><a href="#tab2" data-toggle="tab">Test connection</a></li>
    		<li><a href="#tab3" data-toggle="tab">Review DIFF Result</a></li>
    		<li><a href="#tab4" data-toggle="tab">Preview SQL</a></li>

    	</ul>
    	<div class="tab-content">
    	    <div class="tab-pane" id="tab1">

			<div class="row">
			  <div class="col-md-6"><h4>Source database <small>That we have</small></h4>

			  </div>
			  <div class="col-md-6"><h4>Destination database <small>That will be modified</small></h4>
			  </div>
			</div>

			<form  method="post" id="connection_info">

				<div class="row">
				  <div class="col-md-6">
					  <div class="form-group">
					    <label for="ohost">Host</label>
					    <input type="text" class="form-control" placeholder="" name="ohost" id="ohost" value="<?=$diff->getConfig("ohost")?>">
					  </div>
					  <div class="form-group">
					    <label for="ouser">User</label>
					    <input type="text" class="form-control" placeholder="" name="ouser" id="ouser" value="<?=$diff->getConfig("ouser")?>">
					  </div>
					  <div class="form-group">
					    <label for="opassword">Password</label>
					    <input type="password" class="form-control" placeholder="" name="opassword" id="opassword" value="<?=$diff->getConfig("opassword")?>">
					  </div>
					  <div class="form-group">
					    <label for="odatabase">Database name</label>
					    <input type="text" class="form-control" placeholder="" name="odatabase" id="odatabase" value="<?=$diff->getConfig("odatabase")?>">
					  </div>
				  </div>
				  <div class="col-md-6">
					  <div class="form-group">
					    <label for="mhost">Host</label>
					    <input type="text" class="form-control" placeholder="" name="host" id="mhost" value="<?= $diff->getConfig("mhost") ?>">
					  </div>
					  <div class="form-group">
					    <label for="muser">User</label>
					    <input type="text" class="form-control" placeholder="" name="muser" id="muser" value="<?=$diff->getConfig("muser")?>">
					  </div>
					  <div class="form-group">
					    <label for="mpassword">Password</label>
					    <input type="password" class="form-control" placeholder="" name="mpassword" id="mpassword" value="<?=$diff->getConfig("mpassword")?>">
					  </div>
					  <div class="form-group">
					    <label for="mdatabase">Database name</label>
					    <input type="text" class="form-control" placeholder="" name="mdatabase" id="mdatabase" value="<?=$diff->getConfig("mdatabase")?>">
					  </div>
				  </div>
				</div>
			</form>

    	    </div>
    	    <div class="tab-pane" id="tab2">
    	    	<div class="row">
    	    		&nbsp;
    	    	</div>
				<div role="alert" class="alert alert-success" id="success-msg" style="display:none"> 
					<strong>Great!</strong> Everything looks good, now go to next step to start DIFF process. 
				</div>

				<div role="alert" class="alert alert-warning" id="error-msg" style="display:none"> 
					<strong>Ooops!</strong> Please back to previous step and check the connection parameters. 
				</div>

    	    </div>
    		<div class="tab-pane" id="tab3">
    			<div id="result-pane"></div>
    	    </div>
    		<div class="tab-pane" id="tab4">
   				<textarea id="preview" class="form-control" rows="8"></textarea>
    	    </div>
    		<ul class="pager wizard">
    			<li class="previous first" style="display:none;"><a href="#">First</a></li>
    			<li class="previous"><a href="#">Previous</a></li>
    			<li class="next last" style="display:none;"><a href="#">Last</a></li>
    		  	<li class="next"><a href="#">Next</a></li>
    		</ul>
    	</div>
    </div>


  </div>
  <div class="col-md-1">&nbsp;</div>
</div>

    <script src="js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="js/jquery.bootstrap.wizard.min.js"></script>
	<script src="js/prettify.js"></script>
	<script>
	$(document).ready(function() {
	  	//$('#rootwizard').bootstrapWizard({'tabClass': 'nav nav-tabs'});
		



	  	$('#rootwizard').bootstrapWizard({
	  		'tabClass': 'nav nav-tabs',
	  		onTabShow: function(tab, navigation, index) {
	  			processTab(tab, navigation, index);
	  			/*
				var $total = navigation.find('li').length;
				var $current = index+1;
				//console.log(index);
				switch(index){
					case 0:

					break;					
					case 1:
					break;

				}
				*/

				//var $percent = ($current/$total) * 100;
				//$('#rootwizard').find('.bar').css({width:$percent+'%'});

			}});		

		window.prettyPrint && prettyPrint()
	});

	var db_init = false;
	var processTab = function(tab,navigation,index){

		switch(index){
			case 0:
			if (!db_init) {
				//$('#rootwizard').bootstrapWizard('disable', 1);
				$('#rootwizard').bootstrapWizard('disable', 2);
				$('#rootwizard').bootstrapWizard('disable', 3);				
			};
			break;
			case 1:

				checkConnection();

			break;

			case 2:

				diffProcess();

			break;

			case 3:
				getSQL();
			break;
		}
	};


	function checkConnection(){
		var form_data = $('#connection_info').serialize();
          $.ajax({
              type: "POST",
              url: 'step1.php',
              data: form_data,
              success: function(response){
                
                switch(response.result){
                    case 'ok':
                        //alert("ok");
                        
                        $('#success-msg').show();
                        $('#error-msg').hide();
                        $('#rootwizard').bootstrapWizard('enable', 1);
                        $('#rootwizard').bootstrapWizard('display', 1);

                        $('#rootwizard').bootstrapWizard('enable', 2);

                    break;
                    case 'fail':
                        //alert(response.message);
                        $('#success-msg').hide();
                        $('#error-msg').show();

                        //$('#rootwizard').bootstrapWizard('disable', 1);
                        $('#rootwizard').bootstrapWizard('disable', 2);
                        //$('#rootwizard').bootstrapWizard('display', 0);

                    break;
                }
              },
              dataType: 'json'
            }).fail(function(jqXHR, textStatus) {
                alert(jqXHR.responseText);                
              })
              .always(function(){
                //$(".ajax-loading").hide();
              });
	}

	function diffProcess(){
          $.ajax({
              type: "POST",
              url: 'step2.php',
              success: function(response){
                $('#result-pane').html(response);
                $('#rootwizard').bootstrapWizard('enable', 3);
              }
            }).fail(function(jqXHR, textStatus) {
                alert(jqXHR.responseText);                
              })
              .always(function(){
                //$(".ajax-loading").hide();
              });
	}

	function showFields(pos){
		if($('#row_' + pos).is(':visible')){
			$('#row_'+pos).hide();
		}else{
			$('#row_'+pos).show();
		}
	}

	function getSQL(){
		var sList = [];

		$('input[type=checkbox]').each(function () {
			if (this.checked) {
				//sList += "(" + $(this).val() + "-" + (this.checked ? "checked" : "not checked") + ")";
				id = $(this).attr('data-id');
				sql = $('#' + id).html();
				sList.push(sql);
				//console.log ( sql);
			};
		});
		$('#preview').val(sList.join('\n'));
		

		//return sList;
	}

var check = false;
	function checkUncheck(){

		$('input[type=checkbox]').each(function () {
			this.checked = !check;
		});
		check =  !check;
	}

	</script>
	<style type="text/css">
	.tab-pane{
		padding: 10px 20px;
	}
	.sql {
	    font-family: courier;
	   
	}

	input[type="checkbox"] {
	    display: block;
	    float: left;
	    margin-right: 5px;
	    margin-top: 3px;
	}

	label {
	    display: block;
	    float: none !important;
	}	
	</style>
</body>
</html>
