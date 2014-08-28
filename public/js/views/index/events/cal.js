var  Events = {
        
        Colors:null,
        
        Init:function(){
            this.Listeners();
        },
          
        Listeners:function(){

        
         asyncAction.sendPost('/event-colors', {}, function(data){
                        
                        Events.Colors = data;

                      });
        
        
            
        
        
            $("body").delegate(".js-view-event", "click", function() {
                
                var el = $(this);
                var id = el.attr('data-id');
    
                lightBox.show("mainModal",
                              "Event Details",
                              {remote:'/event-detail/?id='+id, params:{}, callback:function(){
                                
                                }},
                              'get');

        });
            
        //on cal next show next month
        $("body").delegate( ".js-calendar-nextprev", "click", function() {
            
               //get the next prev
               var el = $(this);
               
               //get the current date
               var m = el.attr('data-month');
               var y = el.attr('data-year');

                //reload the calendar
                content.load( '/calendar', {month:m, year:y},
                            function(html){  $("#js-calendar-wrapper").html(html);
                            }, 'html' );
               
               
               //reload the event list
               $("#js-events-wrapper").html("Loading...");
                content.load( '/event-by-date', {month:m, year:y},
                            function(html){  $("#js-events-wrapper").html(html);
                            }, 'html' );
  
            });
        },
    
        AddToCal:function(data) {                    
             
             $("td").removeClass('event-on-calendar');
             $("#js-calendar tbody tr").each(function(){
                  var date = $(this).html().trim();
                  $("td[data-day='"+date.substring(10,8)+"']").addClass('event-on-calendar');
             });
        },
   
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
 
   $(document).ready(function(){
        var _cal = $("#js-calendar");
        var _year = _cal.attr('data-year');
        var _month = _cal.attr('data-month');
         Events.Init();
        });
   
   
   
   