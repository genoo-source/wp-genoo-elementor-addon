jQuery(document).on("ready", function () {
  var lead_folder_id;
  //function call for select leadtype on page load
  leadcreate();
  //leadfolder on change for show leadtypes.
  jQuery(document).on(
    "change",
    ".elementor-control-SelectLeadFolder > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select",
    function () {
      lead_folder_id = jQuery(this).val();
      var url = jQuery(".getadminurl").val();
      jQuery.ajax({
        url: url,
        type: "POST",
        cache: false,
        data: {
          action: "sendleadfolder",
          lead_folder_id: lead_folder_id
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
  jQuery(document).on("click", ".createleadsave", function (evt) {
    var lead = jQuery(".createlead").val();
    if (lead !== "") {
      lead_folder_id = jQuery(
        ".elementor-control-SelectLeadFolder > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
      ).val();
      var url = jQuery(".getadminurl").val();
      jQuery.ajax({
        url: url,
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
          folder_id: lead_folder_id
        },
        success: function (data) {
          jQuery(
            ".elementor-control-SelectLeadType > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
          ).append(
            '<option value="' + data + '">' + lead + '</option>');
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
        }
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
      var url = jQuery(".getadminurl").val();
      jQuery.ajax({
        url: url,
        type: "POST",
        cache: false,
        data: {
          action: "emaildata",
          selectid: selectid
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
            ).append(
              '<option value="0" selected="selected">Select email</option>'
            );

             jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").val(0).trigger("change");

            jQuery.each(data, function (key, value) {
              jQuery(
                ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
              ).append(
                '<option value="' + value.id + '">' + value.name + '</option>'
              );
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
              jQuery(
                ".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select"
              ).removeAttr("style");
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
        }
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
	  if(jQuery(this).val() === "1")
      {
      jQuery("#elementor-panel-saver-button-publish").attr("disabled", true);  
      }
      else
      {
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
    if (selectemail != "" || selectemail != 0) {
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
