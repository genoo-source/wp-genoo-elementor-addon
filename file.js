jQuery(document).on("ready", function () {
  var lead_folder_ids;
//function call for select leadtype on page load
  leadcreate();
  //leadfolder on change for show leadtypes.
  jQuery(document).on(
    "change",
    ".elementor-control-SelectLeadFolder > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select",
    function () {
      
      lead_folder_ids = jQuery(this).val();
      var lead_folder_id = lead_folder_ids.substr(
        lead_folder_ids.indexOf("#") + 1
      );

      if (lead_folder_id == "") {
        var lead_folder_id_value = lead_folder_ids.split("#")[0];
      } else {
        var lead_folder_id_value = lead_folder_ids.substr(
          lead_folder_ids.indexOf("#") + 1
        );
      }

      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        data: {
          action: "sendleadfolder",
          lead_folder_id: lead_folder_id_value,
        },
        success: function (data) {
          jQuery(
            ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
          ).empty("");
          jQuery.each(data, function (key, value) {
            jQuery(
              ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
            ).append('<option value="' + key + '">' + value + " </option>");
          });

          var leadtype = jQuery(
            ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
          ).val();

          if (leadtype == "" || leadtype == 0 || leadtype == 2) {
            jQuery(".elementor-control-SelectLeadType").css(
              "border",
              "2px solid red"
            );
            jQuery("#elementor-panel-saver-button-publish").attr(
              "disabled",
              true
            );
            return false;
          } else {
            jQuery("#elementor-panel-saver-button-publish").attr(
              "disabled",
              false
            );
          }
        }
      });
    }
  );

  //onclick function for create new leadtype api call.
  jQuery(document).on("click", ".leadtypesave", function (evt) {
    jQuery(".checkbox_values").css("display","none");
    var checkboxes = [];

  // var itemid =  jQuery(
    //  ".elementor-control-control-custom_id > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > input"
   // ).val();

    //alert(itemid);
   
    jQuery('.checkbox_values > div > input[type="checkbox"]:checked').each(function() {
   
  var data_Value = {};
    var data = jQuery(this).next();
        
         data_Value.label = data.html();
         data_Value.labelvalue =  jQuery(this).val(); 
         checkboxes[[jQuery(this).val()]] =  data_Value;
    });
   



checkboxes = checkboxes.filter(item => item);


jQuery.each(checkboxes, function (key, value) {

  jQuery('.lead_value_after_save').append('<div><input type="text" class="lead_calss" data-id="'+value.labelvalue+'" value="'+value.label+'"  name="after_save_labels[]" /></div>');
});

jQuery('.lead_value_after_save').append('<button type="button" class="leadtypeupdate" name="leadtypesave">Update Label</button>');
  });

   //onclick function for create new leadtype api call.
   jQuery(document).on("click", ".leadtypeupdate", function (evt) {
    var productIds = [];


    var url = jQuery(location).attr('href');

    var URLVariables = url.split('&')

    //alert(URLVariables.get('post'));

   var check_after_Values = [];

var dataLabelValue=[];
var selectedValues=[];
    jQuery('.lead_calss').each(function(){

      
      var data_Value = {};

     data_Value.label = jQuery(this).val();
     data_Value.labelvalue =  jQuery(this).attr("data-id"); 
     selectedValues[[jQuery(this).attr("data-id")]]=data_Value;
     check_after_Values.push(data_Value);
     dataLabelValue.push(jQuery(this).attr("data-id"));
    
       
   }); 
   selectedValues = selectedValues.filter(item => item);
  
   let searchParams = new URLSearchParams(window.location.search)

   let param = searchParams.get('post');
   

   jQuery.ajax({
    url: ajaxurl,
    type: "POST",
    cache: false,
    data: {
      action: "leadtype_elementor_savelist",
      check_after_Values:selectedValues,
      post_id : param
     
    },
    success: function (data) {

      jQuery('.lead_value_after_save').css('display','none');
      jQuery(".checkbox_values").css("display","block");
      jQuery('.lead_value_after_save').html("");

  
    },

    error: function (errorThrown) {
      console.log(errorThrown);
    },
  });

  });
  jQuery(document).on("click", ".createleadsave", function (evt) {
    var lead = jQuery(".createlead").val();
    if (lead !== "") {
      lead_folder_ids = jQuery(
        ".elementor-control-SelectLeadFolder > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
      ).val();
      lead_folder_ids = jQuery(this).val();
      var lead_folder_id = lead_folder_ids.substr(
        lead_folder_ids.indexOf("#") + 1
      );

      if (lead_folder_id == "") {
        var lead_folder_id_value = lead_folder_ids.split("#")[0];
      } else {
        var lead_folder_id_value = lead_folder_id;
      }
      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        data: {
          action: "btnleadsave",
          leadtypevalue: lead,
          description: lead,
          mngdlistind: false,
          costforall: "",
          costperlead: "",
          sales_ind: "no",
          system_ind: "no",
          blog_commenters: "no",
          blog_subscribers: "no",
          folder_id: lead_folder_id_value,
        },
        success: function (data) {
          jQuery(
            ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
          ).append('<option value="' + data + '">' + lead + "</option>");
          jQuery(
            ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
          ).val(data);
          jQuery(
            ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
          )
            .val(data)
            .trigger("change");

          leadcreate();
        },

        error: function (errorThrown) {
          console.log(errorThrown);
        },
      });
    }
  });

  //select leadtypes on change
  function leadcreate() {
    jQuery(document).on(
      "change",
      ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select",
      function (event) {
        var getvalue = jQuery(this).val();
        if (getvalue != 2) {
          jQuery(".createlead").val("");
          jQuery(".elementor-control-Createleadtype").css("display", "none");
        }

        if (getvalue == 0 || getvalue == "" || getvalue == 2) {
          jQuery(".elementor-control-SelectLeadType").css(
            "border",
            "2px solid red"
          );
          jQuery("#elementor-panel-saver-button-publish").attr(
            "disabled",
            true
          );
          return false;
        } else {
          jQuery(".elementor-control-SelectLeadType").removeAttr("style");
          jQuery("#elementor-panel-saver-button-publish").attr(
            "disabled",
            false
          );
        }
      }
    );
  }

  //on change for email folder to pass folder id into ajax call to show emails

  jQuery(document).on(
    "change",
    ".elementor-control-SelectEmailfolder > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select",
    function () {
      var selectid = jQuery(this).val();
      jQuery(
        ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
      ).show();
      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        data: {
          action: "emaildata",
          selectid: selectid,
        },
        success: function (data) {
          if (selectid && selectid != "0") {
            jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field"
            ).show();
            jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
            ).empty("");
            jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
            ).append('<option value="0">Select email</option>');

            jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
            )
              .val(0)
              .trigger("change");

            jQuery.each(data, function (key, value) {
              jQuery(
                ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
              ).append('<option value="' + key + '">' + value + "</option>");
            });

            var emailvar = jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
            ).val();
            jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
            )
              .val(emailvar)
              .trigger("change");
            if (emailvar == 0) {
              jQuery(".elementor-control-SelectEmail").css(
                "border",
                "2px solid red"
              );
              jQuery("#elementor-panel-saver-button-publish").attr(
                "disabled",
                true
              );
              return false;
            } else {
              jQuery(".elementor-control-SelectEmail").removeAttr("style");
              jQuery("#elementor-panel-saver-button-publish").attr(
                "disabled",
                false
              );
            }
          } else {
            jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
            ).empty("");

            jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field"
            ).hide();
          }
          if (data.length === 0) {
            jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
            ).empty("");
            jQuery(
              ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
            ).append('<option value="0">No Email Here</option>');
          }
        },
        error: function (errorThrown) {
          console.log(errorThrown);
        },
      });
    }
  );

  //select leadtype on change for create leadtype input box view

  jQuery(document).on(
    "change",
    ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select",
    function () {
      if (jQuery(this).val() === "2") {
        jQuery(".elementor-control-Createleadtype").css("display", "block");
      } else {
        jQuery(".elementor-control-Createleadtype").css("display", "none");
      }
      if (jQuery(this).val() === "1" || jQuery(this).val() === "0") {
        jQuery("#elementor-panel-saver-button-publish").attr("disabled", true);
      } else {
        jQuery("#elementor-panel-saver-button-publish").attr("disabled", false);
      }
    }
  );
});

//check leadtype not null while update the post/page.

jQuery(document).on(
  "click",
  "#elementor-panel-saver-button-publish",
  function (event) {
    event.preventDefault();
    var getvalue = jQuery(
      ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
    ).val();
 

    var source_field = jQuery(
      ".elementor-control-Source > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > input"
    ).val();
   if(source_field!='')
   {
    jQuery(".elementor-control-Source > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > input"
    ).addClass("sourceleadfield");
    }

    if (getvalue == "" || getvalue == 0 || getvalue == 2) {
      jQuery(".elementor-control-SelectLeadType").css(
        "border",
        "2px solid red"
      );
      jQuery("#elementor-panel-saver-button-publish").attr("disabled", true);
      event.stopImmediatePropagation();
      return false;
    } else {
      jQuery(".elementor-control-SelectLeadType").removeAttr("style");
      jQuery("#elementor-panel-saver-button-publish").attr("disabled", false);
    }
  }
);
//}
//);
//checkbox onclick for webinars
jQuery(document).on(
  "change",
  ".selectwebinarval > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > label",
  function (event) {
    event.preventDefault();
    var getvalue = jQuery(
      ".elementor-control-SelectWebinar > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
    ).val();
    if (
      (jQuery("input[type=checkbox]").is(":checked") && getvalue == "") ||
      (jQuery("input[type=checkbox]").is(":checked") && getvalue == 0)
    ) {
      jQuery(".elementor-control-SelectWebinar").css("border", "2px solid red");
      jQuery("#elementor-panel-saver-button-publish").attr("disabled", true);
    } else {
      jQuery(".elementor-control-SelectWebinar").removeAttr("style");
      jQuery("#elementor-panel-saver-button-publish").attr("disabled", false);
    }
  }
);

//onchange function  for select email
jQuery(document).on(
  "change",
  ".elementor-control-SelectEmail  > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select",
  function () {
    var selectemail = jQuery(this).val();
    if (selectemail == "" || selectemail == 0) {
      jQuery(".elementor-control-SelectEmail").css("border", "2px solid red");
      jQuery("#elementor-panel-saver-button-publish").attr("disabled", true);
    } else {
      jQuery(".elementor-control-SelectEmail").removeAttr("style");
      jQuery("#elementor-panel-saver-button-publish").attr("disabled", false);
    }
  }
);
//on change function for webinars
jQuery(document).on(
  "change",
  ".elementor-control-SelectWebinar  > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select",
  function () {
    var selectwebinar = jQuery(this).val();
    if (selectwebinar == "" || selectwebinar == 0) {
      jQuery(".elementor-control-SelectWebinar").css("border", "2px solid red");
      jQuery("#elementor-panel-saver-button-publish").attr("disabled", true);
    } else {
      jQuery(".elementor-control-SelectWebinar").removeAttr("style");
      jQuery("#elementor-panel-saver-button-publish").attr("disabled", false);
    }
  }
);
