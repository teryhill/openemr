<?php
/* This include is just a HTML and JS *fragment* it CANNOT stand alone
 * 
 * This file is used to include the capability to have a pop-up DIV in which
 * a user can add a new option to a list box. Primarily this is for the 
 * demographics, history, and referral screens. This code could be used elsewhere
 * as long as it had the required supporting HTML and JS
 *
 * 03\24\2015 added code to support postal code add (TLH)
 *
 * REQUIRED to make work:
 *  - jQuery
 *  - form element with the class '.addtolist'
 *  - be used in a file that already includes globals.php
 */
?>


<!-- hidden DIV used for creating new list items -->
<div id="newlistitem" class="body_top" style="border: 2px outset #666; display:none; position:absolute; padding:5px; ">
 <input type="hidden" name="newlistitem_listid" id="newlistitem_listid" value="">
 <input type="hidden" name="newlistitem_fieldid" id="newlistitem_fieldid" value="">
 <div id="specificForm">
 </div>
 <div style = 'text-align:center;'>
  <div style='margin:0.5em 0 0 0;'>
   <input type='button' name='newlistitem_submit' id='newlistitem_submit' value='<?php xl('Add','e'); ?>'>
   <input type='button' name='newlistitem_cancel' id='newlistitem_cancel' value='<?php xl('Cancel','e'); ?>'>
  </div>
 </div>
</div>


<script language="javascript">

    // collect the custom state widget flag
    var stateCustomFlag = "<?php echo $GLOBALS['state_custom_addlist_widget']; ?>"
    var postalCustomFlag = "<?php echo $GLOBALS['postal_custom_addlist_widget']; ?>"
    
    // generic form for input box
    var generic = "<input type='textbox' name='newlistitem_value' id='newlistitem_value' size='20' maxlength='50'>"

    // state form for input box
    var state   = "\
<ul style='list-style-type:none; margin:0; padding:0;'>\
 <li style='font-weight:bold; font-size:120%; text-align:center;'>  <?php echo xlt('Enter new State'); ?> </li>\
 <li style='margin:0.5em 0 0 0; padding:0;'> <?php echo xlt('Full Name'). ':'; ?>\
  <input type='textbox' name='newlistitem_value' id='newlistitem_value' size='20' maxlength='50'></li>\
 <li style='margin:0.5em 0 0 0; padding:0;'> <?php echo xlt('Abbreviation') .':'; ?>\
  <input type='textbox' name='newlistitem_abbr' id='newlistitem_abbr' size='10' maxlength='50'></li>\
</ul>\
"
    // Postal Code form for input box
    var postal   = "\
<ul style='list-style-type:none; margin:0; padding:0;'>\
 <li style='font-weight:bold; font-size:120%; text-align:center;'> <?php echo xlt('Enter new Postal Code'); ?> </li>\
 <li style='margin:0.5em 0 0 0; padding:0;'> <?php echo xlt('Code'). ':'; ?>\
  <input type='textbox' name='newlistitem_abbr' id='newlistitem_abbr' size='5' maxlength='10'></li>\
 <li style='margin:0.5em 0 0 0; padding:0;'> <?php echo xlt('City') .':'; ?>\
  <input type='textbox' name='newlistitem_value' id='newlistitem_value' size='20' maxlength='50'></li>\
 <li style='margin:0.5em 0 0 0; padding:0;'> <?php echo xlt('State') .':'; ?>\
   <input type='textbox' name='newlistitem_city' id='newlistitem_city' size='2' maxlength='2'></li>\
</ul>\
"

// jQuery makes life easier (sometimes)

$(document).ready(function(){

    /********************************************************/
    /************ List-box modification functions ***********/
    /********************************************************/
   
    $("#newlistitem").keypress(function(evt) { if (evt.keyCode == 13) { SaveNewListItem(this, evt); return false; }});
    $(".addtolist").click(function(evt) { AddToList(this, evt); });
    $("#newlistitem_submit").click(function(evt) { SaveNewListItem(this, evt); });
    $("#newlistitem_cancel").click(function(evt) { CancelAddToList(this, evt); });

    // display the 'new list item' DIV at the mouse position
    var AddToList = function(btnObj, e) {
        // capture the ID of the list and specific field being modified from the object's ID
        $('#newlistitem_listid').val($(btnObj).attr("id").replace(/^addtolistid_/g, ""));
        $('#newlistitem_fieldid').val($(btnObj).attr("fieldid"));
		$('#newlistitem_fieldid').val($(btnObj).attr("fieldid"));

        // REMOVE previous form
	$("#specificForm").empty();
	
	// INSERT the selected form
	if (($("#newlistitem_listid").val() == "state") && (stateCustomFlag)) {
	 // insert state form and clear values
         $("#specificForm").append(state);
	 $('#newlistitem_value').val("");
	 $('#newlistitem_abbr').val("");
	}
	else if (($("#newlistitem_listid").val() == "postal_codes") && (postalCustomFlag)) {
	 // insert postal form and clear values
     $("#specificForm").append(postal);
	 $('#newlistitem_abbr').val("");
	 $('#newlistitem_value').val("");
	 $('#newlistitem_city').val("");
	}
	else {
	 // insert generic form and clear values
	 $("#specificForm").append(generic);
	 $('#newlistitem_value').val("");
        }
	
        // make the item visible before setting its x,y location
        $('#newlistitem').css('display', 'inline');
        //getting height and width of the message box
        var height = $('#newlistitem').height();
        var width = $('#newlistitem').width();
        //calculating offset for displaying popup message
        leftVal=e.pageX-(width/2)+"px";
        topVal=e.pageY-(height/2)+"px";
        //show the DIV and set cursor focus
        $('#newlistitem').css({left:leftVal,top:topVal}).show();
        if (($("#newlistitem_listid").val() == "postal_codes") && (postalCustomFlag)) {
		$('#newlistitem_abbr').focus();
		}
		else
		{
        $('#newlistitem_value').focus();
		}
    };
   
    // hide the add-to-list DIV and clear its textbox
    var CancelAddToList = function(btnObj, e) {
        $('#newlistitem').hide();
    }
    
    // save the new list item to the given list
    var SaveNewListItem = function(btnObj, e) {
	if (($("#newlistitem_listid").val() != "postal_codes")) {
	// VALIDATE the selected form
        //  Don't allow a number as first character
        //  Don't allow illegal characters (' and " for now) - still developing
	//   First, validate fields common to all forms
        if ($("#newlistitem_value").val().match(/^\d/)) {
            alert("<?php echo htmlspecialchars(xl('List items can not start with a number.'),ENT_QUOTES); ?>");
            $("#newlistitem_value").focus();
            return false;
        }
        if ($("#newlistitem_value").val().match(/[\'\"]/)) {
            alert("<?php echo htmlspecialchars(xl('List items contains illegal character(s).'),ENT_QUOTES); ?>");
            $("#newlistitem_value").focus();
            return false;
        }
	}	
	//  Second, validate form specific fields
        if (($("#newlistitem_listid").val() == "state") && (stateCustomFlag)) {
            // state forms specific validation
            if ($("#newlistitem_abbr").val().match(/^\d/)) {
                alert("<?php echo htmlspecialchars(xl('List items can not start with a number.'),ENT_QUOTES); ?>");
                $("#newlistitem_abbr").focus();
                return false;
            }
            if ($("#newlistitem_abbr").val().match(/[\'\"]/)) {
                alert("<?php echo htmlspecialchars(xl('List items contains illegal character(s).'),ENT_QUOTES); ?>");
                $("#newlistitem_abbr").focus();
                return false;
            }
        }

        // PROCESS the selected form
        if (($("#newlistitem_listid").val() == "state") && (stateCustomFlag)) {
	    // process state form
	    //  (note we have collected a title and abbreviation)
	    var listid = $("#newlistitem_listid").val();
	    var newitem = $('#newlistitem_value').val().toUpperCase();
	    var newitem_abbr = $('#newlistitem_abbr').val().toUpperCase();
		var newitem_city = "";    
	}	
	else if (($("#newlistitem_listid").val() == "postal_codes") && (postalCustomFlag)) {
	    // process postal form
	    //  (note we have collected a title and abbreviation and city)
	    var listid = $("#newlistitem_listid").val();
		var newitem_abbr = $('#newlistitem_abbr').val();
	    var newitem = $('#newlistitem_value').val().toUpperCase();
	    var newitem_city = $('#newlistitem_city').val().toUpperCase();
        var f = document.demographics_form;
        f.form_city.value = $('#newlistitem_value').val().toUpperCase();
        f.form_state.value = $('#newlistitem_city').val().toUpperCase();    
	}
	else {
	    // process generic form
	    //  (note we have only collected the title, so will make
	    //   abbreviation same as title)
	    var listid = $("#newlistitem_listid").val();
	    var newitem = $('#newlistitem_value').val();
	    var newitem_abbr = $('#newlistitem_value').val();
		var newitem_city = "";
	}

        // make the AJAX call to save the new value to the specified list
        // upon returning successfully, refresh the list box and select 
        // the new list item
        $.getJSON("<?php echo $GLOBALS['webroot']; ?>/library/ajax/addlistitem.php",
                    {listid: listid,
		     newitem: newitem,
		     newitem_city: newitem_city,
		     newitem_abbr: newitem_abbr},
                    function(jsondata, txtresponse) {
		    	 if( jsondata.error == '' ){
			  //process each widget with the same list
			$("select.addtolistclass_"+listid).each(function(){
			    var pointer = $(this);
			    var currentselected = pointer.val();
			    var listboxfield = pointer.attr("id");
			    var listbox = document.getElementById(listboxfield);
                            while (listbox.options.length > 0) { listbox.options[0] = null; }
                            $.each(jsondata.options, function () {
			        if (listboxfield == $('#newlistitem_fieldid').val()) {
				    // build select for the chosen widget field
                                    listbox.options[listbox.options.length] = new Option(this.title, this.id);
                                    if (this.id == newitem_abbr) {
				        pointer.val(this.id);
                                    }
				}
				else {
				    // build select for the other widgets using the same list
				    listbox.options[listbox.options.length] = new Option(this.title, this.id);
				    if (this.id == currentselected) {
                                        pointer.val(currentselected);
				    }
				}
                            }); 
			});
        
                        // now hide the DIV
                        $('#newlistitem').hide();
		     }else{
             	
                 alert( jsondata.error );
             }
                    }
                );

    }  // end SaveNewListItem

}); // end jQuery .ready 
</script>

