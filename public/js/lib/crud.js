var Crud =  {
      scope:null,
      template:"",
      callback:false,
      Events:function(  ) {        
        //////////////////////////////////////////////
       //Crud New/Edit Events
       /////////////////////////////////////////////
       $("body").delegate(".crud-create-update", "click", function() {
            var crud = Crud.Info($(this));
               $.proxy(lightBox.show('mainModal', crud.title,
                                          {'remote':crud.full_path,
                                           'params': crud.params,
                                           'callback':function(data) {
                                              $("#"+crud.form).append(crud.btn);
                                              if (crud.callback != undefined) {
                                               helpers.strFunction(crud.callback, window, data);
                                              }
                                              
                                        }},'get'), crud);
       });  

    //Crud Create
    $("body").delegate( ".crud-create", "click", function() {
        var el = $("#"+$(this).attr('data-bind'));
        var crud = Crud.Info(el, 'create');
         Crud.Update(crud.path+"/create/", crud.form,  $.proxy(function(data) {
              
              if (data.success != false) {
              
                var template = _.template($("#"+crud.template).html());
                $("#"+crud.target).prepend(template(data)).promise(
				
				function(){
					
					$("#"+crud.target+" .crud-row-"+data.id).css({'display':'none'}).fadeIn(1000,function() {
					
					});
                
				if (crud.onCreate != undefined) {
					helpers.strFunction(crud.onCreate, window, crud);
				}
				
					}												   
																   
				);
				
				
				lightBox.close('mainModal');
                
				
              }else{
              
                Crud.FormErrors(crud.form, data.errors);
              
              }
              
          },crud), 'json' );
         
    });

    //Crud Update
    $("body").delegate( ".crud-update", "click", function() {
          
          var el = $("#"+$(this).attr('data-bind'));
          var crud = Crud.Info(el, 'update');
          
          Crud.Update(crud.path+"/update/", crud.form,  $.proxy(function(data) {
              if (data.success != false) {

              
                if (crud.onUpdate !== undefined) {
                    helpers.strFunction(crud.onUpdate, window, data);
                }
              
                var template = _.template($("#"+crud.template).html());
                $("#"+crud.target+" .crud-row-"+data.id).html(template(data)).css({'display':'none'}).fadeIn(1000,function() {});          
                
                lightBox.close('mainModal');
              
              }else{
                
                
                Crud.FormErrors(crud.form, data.errors);
                
              }

              
          },crud), 'json' );
    });

    //Curd Delete
    $("body").delegate(".crud-delete", "click", function() {
  
     var el = $("#"+$(this).attr('data-bind'));   
     var crud = Crud.Info(el, 'delete');
     var params = Crud.paramsString($(this).attr('data-params'));
     
     Crud.Confirm({ url: crud.path+"/delete/",
                              params: params,
                              title: 'Please confirm delete',
                              text:  'The record will be completelly removed! Is it ok?',
                              ok: 'Remove',
                              cancel: 'Cancel'
       }).done(function(data) {
        
            if (data.success == true) { 
             var el = $("#"+crud.target+" .crud-row-"+data.id).css({'color':'red'});
                 el.fadeOut(1000, function() { $(this).remove(); });
                 
                  if (crud.onDelete !== undefined) {
                    helpers.strFunction(crud.onDelete, window, data);
                  }
                 
                 
                 
            }else{
                
                if (_.isArray(data.errors) ) {
                    var er = '';
                    $.each(data.errors, function(index, e){  er += e+"\n";  });
                    alert(er);
                    
                }else{
                    
                    alert('Error deleting item');
                }            
            }
       
       }).fail(function() {
      });
    });
            
    } ,
      
      Info:function(el, act) {
    
      var params  = (el.attr('data-params') != undefined ) ? el.attr('data-params') : '';
      var action  = (el.attr('data-action') != undefined ) ?  el.attr('data-action') : act;
      var element = (el.attr('data-bind') != undefined ) ? $("#"+el.attr('data-bind')) : el;
      
      var info = { path: element.attr('data-path'),
                   form: element.attr('data-form'),
                   title: element.attr('data-title'),
                   action: action,
                   callback:element.attr('data-callback'),
                   target: element.attr('data-target'),
                   element: element.attr('id'),
                   template: element.attr('data-template'),
                   onUpdate:element.attr('data-onupdate'),
		   onCreate:element.attr('data-oncreate'),
                   onDelete:element.attr('data-ondelete'),
                   request:Math.round(new Date().getTime() / 1000),
                   params:Crud.paramsString(params)};  

                   
        if(info.path.indexOf('.:') != -1) {
            info.path = info.path.replace(/.:/gi, '/');   
         }            
        
        info.btn = '<input type="button" value="Submit" data-bind="'+info.element+'" class="crud-'+info.action+'" />';
        info.full_path = info.path+"/"+info.action+'/crud/'+info.request+'/';
        
        return info;
                        
        },
      
	Create:function(path, data, callback, format) {	
		var params = Crud.params(data);
		$.post(path,
			   params,
			   callback,format).error(Crud.error);
      },

    Read:function(path, params, scope, el, template, callback, format ) {
        Crud.attr = scope;
        Crud.template = template;
        Crud.el = $("#"+el);
        Crud.callback = callback;
        Crud.el.html("");
        $.post(path, params, Crud.populate, format).error(Crud.error);
      },
      
      Update:function(path, data, callback, format) {
        var params = Crud.params(data);
        $.post(path,
	       params,
	       callback,format).error(Crud.error);
     
      },
      
      Delete:function(path, params, callback, format) {
        $.post(path,
	       params,
	       callback,format).error(Crud.error);
      },
      
      
      
    FormErrors:function(form, errors){
        var f = $("#"+form);
        $.each( errors, function( key, err ) {
            if (err.length > 0) {
                f.find("#"+key).css( 'border' , '1px solid red' ).parent().append( "<div class='error'>"+err.join("<br />")+"</div>" );
            }
        });
    },

     Confirm:function(options) {
	if (options == undefined || !options) { options = {}; }
 
	var show = function(el, text) {
	    if (text) { el.html(text); el.show(); } else { el.hide(); }
	}
 
	var url = options.url ? options.url : '';
	var data = options.params ? options.params : '';
	var ok = options.ok ? options.ok : 'Ok';
	var cancel = options.cancel ? options.cancel : 'Cancel';
	var title = options.title
	var text = options.text;
	var dialog = $('#confirm-dialog');
	var header = dialog.find('.modal-header');
	var footer = dialog.find('.modal-footer');
 
	show(dialog.find('.modal-body'), text);
	show(dialog.find('.modal-header h3'), title);
	footer.find('.btn-danger').unbind('click').html(ok);
	footer.find('.btn-cancel').unbind('click').html(cancel);
	dialog.modal('show');
 
	var $deferred = $.Deferred();
	var is_done = false;
	
	footer.find('.btn-danger').on('click', function(e) {
	    is_done = true;
	    dialog.modal('hide');
	    if (url) {	
		$.ajax({
		    url: url,
		    data: data,
		    type: 'POST',
		    }).done(function(result) {
		    $deferred.resolve(result);
		    }).fail(function() {
		    $deferred.reject();	
		});
	    } else {
		$deferred.resolve();
	    }
    });

    dialog.on('hide', function() {
	if (!is_done) { $deferred.reject(); }
    })
     
    return $deferred.promise();
    },      
      
      populate:function( data ) {
        
	var template = _.template(Crud.template);
	var attr = data;
	if (Crud.attr !== false) {
	  attr = data[Crud.attr];    
	}
	
	for (key in attr) {
	    Crud.el.append(template(attr[key]));
	}
	
	if (typeof Crud.callback == 'function') {
	    Crud.callback(data);
	}
	
      },
    
      params:function(data){
            
            
            /*get html input form wysisyg*/
      
            
            if (typeof data == 'object') {
            
                return data;
            
            }else{
              
              
            if ($("#"+data+" .note-editable").size() > 0) {
                $("#"+data).find('textarea.wysiwyg').val($("#"+data+" .note-editable").html());
             }
            
              
              
              return $("#"+data).serialize();
            }
      },
      
      
      paramsString:function(string) {
            
            
            var array = string != undefined && string.length > 0 ?  string.split("&") : false; 
            var values = {};
            
            if (array == false) {
                return {};
            }
            
            for(var key in array) {
            
                var v = array[key];
                
                if (v != '') {
                    var p = v.split("=");
                    values[p[0]] = p[1];
                }
            };
            
            return values; 
        
      },
    
      error:function(e) {
	console.log(e);
      }
    
    
};
$(function(){
    
Crud.Events();    
    
});
