   
    var assignTo = {
     
     update:function(prefix, data, html ){
             
         var par = "#"+prefix+"_"+data.focus;
         var div = $(par);
         var btnOn    = 'js-'+prefix+'-remove-link';
         var btnOff   = 'js-'+prefix+'-assign-link';
         var styleOn  = 'btn-success';
         var styleOff = 'btn-warning';
         
         
         if(data.do == "assign") {
             
             var template = _.template(html);
             var params   = { q:div.attr(prefix+':question'), a:div.attr(prefix+':answer'), id:data.focus, node:prefix};
             $("#event-"+prefix+"-list").append(template(params));
         
         }else{
         
             btnOn   = 'js-'+prefix+'-assign-link';
             btnOff  = 'js-'+prefix+'-remove-link';
             styleOn  = 'btn-warning';
             styleOff = 'btn-success';
             $(".event-"+prefix+"-"+data.focus).remove();
         
         }
         
         $(par+" ."+btnOn).removeClass('hidden');
         $(par+" ."+btnOff).addClass('hidden');
         $(par+" .js-assign-to-event-btn" ).addClass(styleOn);
         $(par+" .js-assign-to-event-btn" ).removeClass(styleOff);    
     
     }
     
 };
 
   
    /*
    admin-users action-link
    */
    var Admin =  {
        
          events:function(){
            
            
            $("body").delegate("form#home-page-updates", "submit", function(){
                var c = $("#HomePageContent").html();
                asyncAction.sendPost('/admin/pages/homepage/', {'HomePageContent':c}, function(data){
                $("#form-message-info").html(data.message);              
            });
        
                
            return false;    
            });
            
            
            $('body').delegate('.admin-link', 'click', Admin.wrangle);
            $('body').delegate('.password-email', 'click', Admin.User.passwordReset);
            
            $( "body" ).delegate( ".js-update-img", "click", function(){
                   var ele = $(this);
                   var id = ele.attr('img:id');
                   var tp = ele.attr('img:type');            
                   lightBox.show('mainModal', 'Add Image', {'remote':'/image/db/create/'+id+'/'+tp+'/callback/imgUpload.complete'});
            });
            
            
            
            //assign event qualifiers. /
            
            $('body').delegate('.js-assign-qualifier','click', function(){
                var ele = $(this);
                var id = ele.attr('event:id');
                lightBox.show('mainModal', 'Assign Service Qualifier Questions', {'remote':'/admin/qualifiers/assign/id/'+id});
            });
            
            
            
          $("body").delegate("#js-event-qualifiers-btns a.js-assign-to-event", "click", function(event){         
            
            var ele = $(this);
            var par = ele.parent();
            var act = ele.attr('event:action');
            var id = par.attr('event:id');
            var qid = par.attr('event:qualifier');
            
            asyncAction.sendPost('/admin/qualifiers/assign/id/'+id, {'qualifier':qid, 'do':act}, function(data){
            
                    if( data.success ){
                        var template = '<li class="event-<%= node %>-<%= id %>"> <%= q %> </li>';
                        assignTo.update('qualifier', data, template);
                    }          
            });
        
        });
            
            
            
          
          $('body').delegate("#eventsformcustom", "submit", function(){
                
                var form = $(this);
                var action = form.attr('action');
                
                var params = form.serializeArray();
                
                content.load( action, params, function(data){
                    
                $(".form-message").html("<div class='alert alert-success'>Event Created successfully.</div>");
                $("html, body").animate({ scrollTop: 0 }, "slow");
                var el = $("#js-calendar");
                var m = el.attr('data-month');
                var y = el.attr('data-year');
                //var d = [data];
                //Admin.Events.Map(d);
                  
                  
                  
                }, 'html' );
                
                return false;});
          
          
          
          
          
          $("body").delegate(".js-events-day", "click", function(){
                $("td").removeClass('focus');
                var cal = $("#js-calendar");
                $(this).addClass('focus');
                var d = $(this).attr('data-day');
                var m = cal.attr('data-month');
                var y = cal.attr('data-year');
                //2014-04-16
                $("#eventsformcustom #created").val(y+"-"+m+"-"+d);
                $("#date-select").html(m+"/"+d+"/"+y);
            
            });
    
            
        //on cal next show next month
        $("body").delegate( ".js-calendar-nextprev", "click", function() {
               
               //get the next prev
               var el = $(this);
               
               //get the current date
               var m = el.attr('data-month');
               var y = el.attr('data-year');
               
            //request the notes form the db that match the date     
            $("#date-select").html( $("#js-calendar").attr("data-date")  );
            $("#date-select-m-y").html(m+"-"+y);
            //reload the calendar
            content.load( '/calendar', {month:m, year:y},
                          
                          function(html){  $("#js-calendar-wrapper").html(html);
                          
                          
                          Crud.Read('/admin/events/bydate/',
                             {'m':m, 'y':y},
                             false,
                             'event-list tbody',
                             $("#js-events-template").html(),
                             Admin.Events.Map,
                             'json')
                          
                          
                          }, 'html' );
            
        });    
            
            
            
          },
          
          
          wrangle:function(){
            
            var el = $(this);
            var action   = el.attr('data-path');
            var params   = helpers.params(el.attr('data-params'));
            var format   = el.attr('data-format');
            var callback = el.attr('data-callback');
            
            if (callback == 'default') {
                callback = Admin.display;
            }else if ('providerEdit'){
                callback = Admin.providerEdit;
            }
            
            $("#content-frame").html("<div class='loading-bar'></div>");
            content.load( action, params, callback, format );
            
          },
          
          display:function(html) {
            $("#content-frame").html(html);
            lightBox.dateFields();
            $('.data-table').dataTable( {
		"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
		"sPaginationType": "bootstrap",
		"oLanguage": {
			"sLengthMenu": "_MENU_ records per page"
		}
                } );
            
          },
          
          providerEdit:function(html) {
            $("#content-frame").html(html);
            helpers.wysiwyg();
            $("#providerform").append(helpers.input('submit',
                                                    'js-providerform-submit',
                                                    'providerform-submit',
                                                    'value="save"'));
            
            
            $("#providerform").submit(function(){
                
                var form = $(this);
                var action = form.attr('action');
                
                var params = form.serializeArray();
                
                content.load( action, params, function(data){
                    
                $(".form-message").html("<div class='alert alert-success'>Update successful.</div>");
                  $("html, body").animate({ scrollTop: 0 }, "slow");
                }, 'html' );
                
                return false;});
            
            
          },
          
          
          programEdit:function() {
            lightBox.dateFields();
            helpers.wysiwyg();
            var d = new Date();
            $("#modifed").val( d.getTime());
          },
          

        Events:{
            
            
            Map:function(data, now) {
                
               var template = _.template($('#js-event-popover').html()); 
               
               var link = "<a title='edit event content' data-callback='default' data-format='html' data-params='id=_id_' data-path='/admin/events/detail/' class='admin-link icon icon-edit'></a>";
               var expt = "<a class='export-attendies' href='/admin/events/export-list/id/_id_' title='Export Attendees List'><i class='icon-wrench'></i></a></p>";
               $("#eventsformcustom #created").val(now);
                
               for(var key in data) {
                 var info = data[key];  
                 var date = info.created;
                 var count = 1;
                 var div =  $("#cal-"+date);
                 var content = info.start_time +" - "+ info.end_time+"<br>" + link +"<br>" + expt;
                 
                 
                 if (div.length > 0) {
                      
                     div.append(template({id:info.id, content:content.replace(/_id_/gi, info.id),title:info.title, date:date}));
                     
                
               }
                 helpers.wysiwyg();
              
                $(".poopover").popover();
              
              
               }   
            },
            
               
            
            
            Update:function()
            {
                helpers.wysiwyg();
                lightBox.dateFieldsById("#eventsform #created", "eventsform-created");
                
            },
            
            
            Delete:function(id){
                $("#cal-info-"+id).remove();
                var cnt = parseInt( $("#events-count").html());
                
                if(cnt > 0) {
                    cnt--
                }
                
                $("#events-count").html( cnt );
                
            }
            
            
            
            
        },
          
        User:{
            
            passwordReset:function() {
                
                var el = $(this);
                var id = el.attr('data-id');

                lightBox.show("mainModal",
                              "Send Password Reset Email",
                              {remote:'/admin/users/reset-password/?id='+id, params:{}, callback:Admin.User.passwordResetForm},
                              'get');
                
            },
            passwordResetForm:function() {
               var form = $('#passwordresetform');
               form.unbind();
               form.submit( function(){
                    var f = $(this);
                    var params = f.serializeArray();
                    content.load( f.attr("action"), params, function(){ lightBox.close("mainModal"); }, 'html' );
                    return false;
                });
            }
            
            
        }
        
        
    };
   
    $(function(){
        
        $.fn.editable.defaults.mode = 'popup';
        $('.editable-fields').editable({selector: 'a.editable'});
        Admin.events();
    });