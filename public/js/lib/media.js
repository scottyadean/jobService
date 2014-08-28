var imgUpload = {
         complete:function(success, msg){
             if (success) {
                 var timestamp = new Date().getTime();
                $(".js-update-img-focus").each(function(){
                   
                    var el = $(this);
                    var refresh =  el.attr('src')+'/'+timestamp;
                    
                    console.log(refresh);
                    
                                   el.attr('src', refresh);
                 });
             }else{
                 $('#img-upload-form-msg').addClass('alert alert-error');
                 $('#img-upload-form-msg').html( this.error(msg) );
             }         
         },

         error:function(key){             
             var errs = {};
                 errs.fileMimeTypeFalse = 'Allowed file types, gif and jpg';
                 errs.fileUploadErrorNoFile = 'File field can not be empty'; 
             
             if (errs[key] != undefined) {
                 return errs[key];
             }
             return key;
         }
};
     