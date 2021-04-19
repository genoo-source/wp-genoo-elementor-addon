 jQuery(document).on('ready', function() {
 
 jQuery(document).on('click','#elementor-panel-footer-saver-publish', function() {
var emailids = jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").val();
if(emailids==0 || emailids==null)
{
  jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").hide();  
}
else
{
   jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").show();  
}

});
jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").empty("");
jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field").hide();    
jQuery(document).on('change','.elementor-control-Select > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select',function(){
var selectid = jQuery(this).val();

   jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").show();  
var emailid = jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").val();

if(emailid===null)
{
  jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").val("Select emails");
   
}

var url = jQuery(".getadminurl").val();

    jQuery.ajax({
        url: url,
       
          type: "POST",
         cache: false,
           data:{ 
             'action':'datainsert',
              'selectid': selectid,
               },
           success:function(data){
               
               if(selectid && selectid != '0'){
                   
          jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field").show();
          jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").empty("");
          jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").attr('required',true);
          jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").append('<option value="0">Select Email</option>');
          jQuery.each( data, function( key, value ){
          jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").append('<option value="'+value.id+'">'+value.name+' </option>');
      
                        });
               }
               else
               {
        jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").empty("");
        jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").attr("required",false);
        jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field").hide();
               }
                if(data.length === 0) {
                jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").empty("");
                jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").append('<option value="0">No Email Here</option>');    
                jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field").hide();    
                }
               
               
},
   error: function(errorThrown){
               console.log(errorThrown);
             }
  
           
                      }); 
});





	
});


/*jQuery(window).on('load', function () {
 var emailids = jQuery(".elementor-control-SelectEmail > .elementor-control-content > .elementor-control-field > .elementor-control-input-wrapper > select").val();  
 alert(emailids);
  });
*/



