jQuery(document).on("ready", function () {

  var lead_folder_ids;

//function call for select leadtype on page load

  leadcreate();


jQuery(document).on("change", ".elementor-control-show_leadtypes > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select", function () {
    
     
    
         var objectvalue = jQuery(this).closest('.elementor-repeater-fields');
         
          var selectedvalues = [];
             
       
         var parentDiv=jQuery(objectvalue);
         
          parentDiv.find('.saveleadtypeidset').css('display','block');
          
          parentDiv.find('.lead_value_after_save').html("");
          
          parentDiv.find('.leadtypeupdate').css('display','none');
         
            parentDiv.find(".elementor-control-show_leadtypes > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select > option:selected").each(function(){
                
                  var data_value = {};
    
                 data_value.label = jQuery(this).html();
                
                 data_value.labelvalue =  jQuery(this).val(); 
                 
               selectedvalues.push(data_value);
              
              });
              
             if(selectedvalues.length!=0){
              
             
       let searchParams = new URLSearchParams(window.location.search);
    
       let param = searchParams.get('post');
       
       var item_id = jQuery(objectvalue).find('input').val();
   
       jQuery.ajax({
    
        url: ajaxurl,
    
        type: "POST",
    
        cache: false,
    
        data: {
    
          action: "leadtype_elementor_change_savelist",
    
          check_after_values:selectedvalues,
    
          post_id : param,
    
          item_id : item_id
    
         
    
        },
    
        success: function (data) {
            
            var updatelabelsval =  parentDiv.find(
                ".elementor-control-updated_labels > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").val();
                
       
         jQuery.each(selectedvalues, function (key, value) {
             
           if(jQuery.inArray(value.labelvalue, updatelabelsval) == -1)
           {
            parentDiv.find(
                    ".elementor-control-updated_labels > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").append('<option value="' + value.labelvalue + '" selected> '+ value.label +' </option>');
           }
           
               });
         
               
           jQuery(".elementor-control-updated_labels > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").trigger('change');
    },
    
      error: function (errorThrown) {
            console.log(errorThrown);
          },
           
    });
          
}
else
{
 parentDiv.find('.saveleadtypeidset').css('display','none');
}
  });

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

jQuery(document).on(

    "change",

    ".elementor-control-show_leadfolders > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select",

    function () { 
        
          var select_lead_folder_id =  jQuery(this).val();
          
          var objectvalue = jQuery(this).closest('.elementor-repeater-fields');


          var parentDiv=jQuery(objectvalue);
          
          if(select_lead_folder_id!=''){
          
          parentDiv.find('.elementorleadfolderloader > p').css('display','block');
         parentDiv.find('.elementor-control-show_leadtypes').css('display','none');
        
     
         parentDiv.find('.saveleadtypeidset').css('display','block');
          
         parentDiv.find('.lead_value_after_save').html("");
          
         parentDiv.find('.leadtypeupdate').css('display','none');

       
     var optionExists = parentDiv.find('.elementor-control-show_leadtypes > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select').val();
     var array = "" + optionExists + "";
    if(array.length!=0){
    var  myArray = array.split(',');
    }
    else
    {
      var  myArray = [];
    }
  
   if(myArray.length==0)
   {
      parentDiv.find(".elementor-control-show_leadtypes > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").html(""); 
     }
    jQuery.ajax({
      url: ajaxurl,
      type: "POST",
      cache: false,
      data: {
        action: "select_lead_types_options",
        select_lead_folder_id: select_lead_folder_id
      },
       
      success: function (data) {
      jQuery.each(data, function (key, value) {
         var append_if_not_exists =  parentDiv.find(".elementor-control-show_leadtypes > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select > option[value=" + key + "]").length > 0;
         
         if(append_if_not_exists==''){
         
      parentDiv.find(".elementor-control-show_leadtypes > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").append('<option value="' + key + '">' + value + " </option>");
         
         }
       parentDiv.find('.elementorleadfolderloader > p').css('display','none');
       
        parentDiv.find('.elementor-control-show_leadtypes').css('display','block');
   
   });
       },

  error: function (errorThrown) {
        console.log(errorThrown);
      },
    });
          }   
          else
          {
       parentDiv.find('.elementorleadfolderloader > p').css('display','none');
       
       parentDiv.find('.elementor-control-show_leadtypes').css('display','none');
       parentDiv.find('.saveleadtypeidset').css('display','none');
       
       parentDiv.find('.elementor-control-updated_labels').css('display','none');
        
          }
        
    });

  //on unselect function for leadtype api call.

jQuery(document).on("select2:unselecting", ".elementor-control-show_leadtypes > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select", function(e){
    
        let searchParams = new URLSearchParams(window.location.search);
    
        let param = searchParams.get('post');
       
        var objectvalue = jQuery(this).closest('.elementor-repeater-fields');

        var parentDiv=jQuery(objectvalue);

        var item_id = jQuery(objectvalue).find('input').val();
        
        var delete_lead_type_id = e.params.args.data.id;
   
       jQuery.ajax({
    
        url: ajaxurl,
    
        type: "POST",
    
        cache: false,
    
        data: {
    
          action: "leadtype_delete_option",
    
          delete_lead_type_id:delete_lead_type_id,
    
          post_id : param,
    
          item_id : item_id
    
          },
    
        success: function (data) {
            
                   parentDiv.find(
                    ".elementor-control-updated_labels > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select > option[value=" + delete_lead_type_id + "]").remove();
                    
                 
           
    },
    
      error: function (errorThrown) {
            console.log(errorThrown);
          },
           
    });
    

});
  //on unselect function for lead folder api call.
jQuery(document).on("select2:unselecting", ".elementor-control-show_leadfolders > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select", function(e){
    
        let searchParams = new URLSearchParams(window.location.search);
    
        let param = searchParams.get('post');
       
        var objectvalue = jQuery(this).closest('.elementor-repeater-fields');

        var parentDiv=jQuery(objectvalue);

        var item_id = jQuery(objectvalue).find('input').val();
       
        var delete_lead_folder_id = e.params.args.data.id;
   
       jQuery.ajax({
    
        url: ajaxurl,
    
        type: "POST",
    
        cache: false,
    
        data: {
    
          action: "leadfolder_delete_option",
    
          delete_lead_folder_id:delete_lead_folder_id,
    
          post_id : param,
    
          item_id : item_id
    
          },
    
        success: function (data) {
            if(data.length==0)
            {
           parentDiv.find(
                    ".elementor-control-show_leadtypes > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").html("");     
            }
            
         jQuery.each(data,function(key,value){
             
            
            
                parentDiv.find(
                    ".elementor-control-show_leadtypes > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select > option[value=" + key + "]").remove();
                    
                    
                    
                     jQuery(".elementor-control-updated_labels > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select > option[value=" + key + "]").remove();
                      
                    
            
                      
            });
           
    },
    
      error: function (errorThrown) {
            console.log(errorThrown);
          },
           
    });
    

});

 jQuery(document).on("click", ".leadtypeupdate", function (evt) {

    var productIds = [];

    var object = jQuery(this).closest('.elementor-repeater-fields');

    var formobject = jQuery(object).closest('.entry-content');

  var parentDiv=jQuery(object);
  
 
    
     var item_id = jQuery(object).find('input').val();
   
    var url = jQuery(location).attr('href');



    var URLVariables = url.split('&')


    var check_after_values = [];
    
    var datalabelvalue=[];
    
    var selectedvalues=[];

 jQuery('.lead_calss').each(function(event){

     var data_value = {};
     
      if(jQuery(this).val()=='')
    {
           jQuery(".lead_calss").css(

              "border",

              "2px solid red"

            );
                event.stopPropagation();

        jQuery('.leadtypeupdate').prop('disabled', true);  
    }
     
    

     data_value.label = jQuery(this).val();
     
     data_value.labelvalue =  jQuery(this).attr("data-id"); 

     selectedvalues.push(data_value);
    
    }); 
    
   if(selectedvalues.length!=0) {

   selectedvalues = selectedvalues.filter(item => item);

   let searchParams = new URLSearchParams(window.location.search)

   let param = searchParams.get('post');
 
   jQuery.ajax({

    url: ajaxurl,

    type: "POST",

    cache: false,

    data: {

      action: "leadtype_elementor_savelist",

      check_after_values:selectedvalues,

      post_id : param,

      item_id : item_id

     },

    success: function (data) {
        
         parentDiv.find(
            ".elementor-control-updated_labels > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").html("");
         
       parentDiv.find('.lead_value_after_save').html("");
    
     jQuery.each(selectedvalues, function (key, value) {  
  
      parentDiv.find(
            ".elementor-control-updated_labels > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").append('<option value="' + value.labelvalue + '" selected> '+ value.label +' </option>');

       
});

   parentDiv.find(".elementor-control-updated_labels > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").trigger('change');


 parentDiv.find('.saveleadtypeidset').css('display','block');
          

    
    },



    error: function (errorThrown) {

      console.log(errorThrown);

    },

  });
      
     
}


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




    
 jQuery(document).on("click", ".saveleadtypeidset", function (evt) {

  var checkboxeslead = [];
  
         jQuery(this).css('display','none');
         
  
          var objectvalue = jQuery(this).closest('.elementor-repeater-fields');
          
              var parentDiv=jQuery(objectvalue);
       
       parentDiv.find('.elementorleadloader > p').css('display','block');
   

       let searchParams = new URLSearchParams(window.location.search);
    
       let param = searchParams.get('post');
       
       var item_id = jQuery(objectvalue).find('input').val();
       
   
        jQuery.ajax({
              url: ajaxurl,
              type: "POST",
              cache: false,
              data: {
                action: "get_all_lead_types_options",
                item_id: item_id,
                post_id:param
                
                
              },
              success: function (data) {
                  
                  
        parentDiv.find('.elementorleadloader > p').css('display','none');

                 if(data.length!=0){
                     
            parentDiv.find('.lead_value_after_save').css('display','block');
            
               jQuery.each(data, function (key, value) {
        
          parentDiv.find('.lead_value_after_save').append('<div><input type="text" required class="lead_calss" data-id="'+key+'" value="'+value+'"  name="after_save_labels[]" /></div>');
        
        });



parentDiv.find('.lead_value_after_save').append('<button type="button" class="leadtypeupdate" name="leadtypesave">Update</button>');  
}
else
{
  parentDiv.find('.lead_value_after_save').css('display','none');
            
}
},

  error: function (errorThrown) {
        console.log(errorThrown);
      },
    });

 // checkboxeslead = checkboxeslead.filter(item => item);
  
   parentDiv.find('.lead_value_after_save').html("");
  


 });
 
 


 

