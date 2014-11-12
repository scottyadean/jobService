var SignIn = {
    Init:function() {  
        this.events();
    },
    events:function() {
        $("body").delegate("#asyncLogin-submit","click", function() {
            var u = $("#log-in-username").val();
            var p = $("#log-in-password").val();  
            $("#asyncLogin-submit").val('sending...').attr('disabled',true);
            content.load('/async/login', {"username":u, "password":p}, SignIn.response, 'json' );                
        });
    },
    response:function(data) {
        if (data.success == false) {
            $('#async-login-info').html("<div class='alert alert-error'>Incorrect Login Credentials</div>");
            $("#asyncLogin-submit").val('Please try again').attr('disabled',false); 
        }else{      
            $("#asyncLogin-submit").val('Success...');
            content.load('/event-qualify', {"event_id":$("#event_id").val()}, SignUp.qualify, 'html' );    
        }
    }
};
  
var SignUp = {
    cap_str:"",
    req:null,

   Init:function(cap){
    
    
     SignIn.Init();
     this.req = $("input.__required");
     this.cap_str = cap;
     this.appendCap(cap);
     
     $("body").delegate(".js-wait-list", "click", function(){
        
        content.load('/event/wait-list', {"id":$("#event_id").val()}, function(html){
            $("#js-event-sign-up-target").html(html);
            }, 'html' );
    });
     
    
     $("body").delegate(".js-wait-list-remove", "click", function(){
        
        content.load('/event/wait-list-remove', {"id":$("#event_id").val()}, function(html){
            $("#js-event-sign-up-target").html(html);
            }, 'html' );
    });
    
     
     
     $("#rsvpform").submit(function(){
        
       var errors = SignUp.validate();
        if (errors.count == 0) {
            var params = $(this).serializeArray();
            $("#sign-up-form").addClass('hidden');
            $("#sign-up-form-info").html('<i class="loading-bar"></i><br /><center><strong>Processing....</strong></center>');
            
               $.post('/join-via-event',
               params,
               
               
               function(data){
                
                if (data.success == true) {
                   $("#sign-up-form-info").html('<div class="alert alert-success">'+data.message+'</div>'
                                                +'<input type="button" style="width:100%;" value="Sign Up for this event." class="btn btn-primary" id="logged-event-sign-up">'); 
                    
                }else{
                  $("#sign-up-form-info").html('<div class="alert alert-error">'+data.message+'</div>'); 
                  $("#sign-up-form").removeClass('hidden');
                }
                
               
               },
               'json').error(function(e){ console.log(e); }); 
            
          

        }   
        return false;  
      });
     
    $("body").delegate("#logged-event-sign-up", "click",function(){
        
        console.log($("#event_id").val());
        
        content.load('/event-qualify', {"event_id":$("#event_id").val()}, SignUp.qualify, 'html' );
    });
     
    $("body").delegate("#eventqualifiers-btn", "click", function() {
        
        $("#eventqualifiers-btn").val('Submit');
        
        var eles = $("#eventqualifiers input.__req");
        var params = {};
        var unchecked = {};
        var errors = 0;             
        
        eles.each(function(){
            var e = $(this);
            var id = e.attr('data-id');
                params[id] =  e.val();
                $("#label-Q-"+id).removeClass('error');             
                if(e.attr('type') == 'checkbox' && !e.prop('checked') ) {
                    $("#label-Q-"+id).addClass('error');
                    errors++;
                }
        });
        if (errors > 0) {
            $(".event-qualifiers-form-info").html("<div class='alert alert-error'>Sorry you do not qualify for this event.</div>");
            return false;
        }
        $.post('/event-sign-up',
               params,
               SignUp.qualifySubmit,
               'json').error(function(e){ console.log(e); }); 
            
        return false;
    });
     
   },
   validate:function() {
       var errors = {};
       var count = 0;
       $(".icon-warning-sign").each( function(){$(this).remove();});
       SignUp.req.each(function () {
                         var el = $(this);
                         var id = el.attr('id');
                             el.removeClass('error');
                           if (helpers.empty(el.val())) {
                               errors[id] = { 'ele':id, 'msg':'Can not be blank'};
                               count++;
                           }});
      
      var cap = $("#robots").val().toUpperCase().replace(/[^A-Za-z0-9]/gi, '');
      if ( cap != SignUp.cap_str ) {
        errors.robots_cap = { 'ele':'robots', 'msg':'Please Enter the value that is displayed in the box.'};
      }
      
      if ($("#email2").val().trim() != $("#email").val().trim()) {
         errors.emails_dont_match = { 'ele':'email2', 'msg':'Emails do not Match, please double check and make sure your email is correct.'};
      }
      
      for (var e in  errors) {
        var info = errors[e];
        $("#"+info.ele).addClass('error');
        $("#"+info.ele+"-element").append( "<i class='icon-warning-sign bg-red' title='"+info.msg+"'></i>" );
         count++;
      }
      
      return {'errors':errors, 'count': count};
    
   },
   
   appendCap:function(cap){
    var  display = jQuery.map((cap).split(''), function(n,i) {
                    return '<i>' + n + '<d'+ n +' class="hidden">abcdefghijklmnopqrstuvwxyz'+i+'</d'+ n +'></i> '})
    $("#robots-element").prepend( "<span class='alert alert-success' > "+  display.join("") +"</span>" );
   },
   
   qualify:function(html) {
        $("#js-event-sign-up-target").html(html);
   },
   
   qualifySubmit:function(data) {
    if(data.success != "0" || data.success != 0) {
    var html = '<h2> Success! </h2>'
            +'<p> You have been signed up to attend this Event.'
            +'  A reminder email has been set to your inbox with more info.'
            +'To view events you are attending or to cancel, please visit '
            +'your <a href="/my/account">account page</a>.</p>';
            
            $("#js-event-sign-up-target").html(html);
    }else{
    
    
    if (data.logged == false) {

        content.load('/event-qualify', {"event_id":$("#event_id").val()}, SignUp.qualify, 'html' );
        
    }else{
    
        var html = '<h2 class="alert alert-error"> Error! </h2>'
                +'<p> Unable to sign-up to this event at this time please try again.</p>';
        }
        
        $("#js-event-sign-up-target").html(html);
    }
    
    
    
    }
       
};

 
    var EventMaps = {
       showStatic:function(address , key) {
        var mygc = new google.maps.Geocoder();
            mygc.geocode({'address' : address}, function(results, status){
                var lat = results[0].geometry.location.lat();
                var lng = results[0].geometry.location.lng();
                var url = "http://maps.google.com/maps/api/staticmap?center="+lat+","+lng+"&zoom=14&markers="+lat+","+lng+"&size=345x200&sensor=false"
                $("#map"+key).html("<img class='img-polaroid' src="+url+" >");
                });
       
        }  
    };