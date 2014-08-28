
var members = {
    
        path:"/providers/search/",
    
        field:'city',
    
        search:function() {
        
            var val = $("#js-search-members").val();
            var rgx = $('input:radio[name=js-regex]:checked').val();
            members.path = $("#js-search-members").attr('data-path');
            
            if (val.trim() != '') {
                
                    content.load(members.path, {field:members.field, id:val, regex:rgx},
                        function(data){
                            $("#js-search-results").html(data);
                        }, 'html');
            }
 
        },
        
        
        lightBox:function(id) {
            
           var address = $("#member-address-"+id).html();
           var city = $("#member-city-"+id).html();
           lightBox.show('mainModal', 'Member Details', {'remote':'/members/view/id/'+id+'',
            
            callback:function(data){
            
            
            
                    $('#mainModalBody').html(data);
                    $('#mainModal').modal('show');
            
                    var loc = address.trim()+" "+city.trim();
                        loc = loc.replace("  ", "");
            
                     members.map( loc );
            
            }          
            
            });
            
            
        },
        
        
 
        map:function(add) {
        var address = add.trim();    
        var mygc = new google.maps.Geocoder();
            mygc.geocode({'address' : address}, function(results, status){
                var lat = results[0].geometry.location.lat();
                var lng = results[0].geometry.location.lng();
                var url = "http://maps.google.com/maps/api/staticmap?center="+lat+","+lng+"&zoom=14&markers="+lat+","+lng+"&size=500x300&sensor=false"
                $("#map").html("<img src="+url+" >");
 
                });
       
        }   

    };

$(function(){
    

    $( "body" ).delegate(".member-item-click","mouseenter", function () {
        $(this).find("div.attach-to").append($("<p class='pos-abs pos-right pos-top'><i class='icon-search'></i></p>"));
    });
        
    $( "body" ).delegate(".member-item-click","mouseleave", function () {
        $(this).find("p:first").remove();
    });       
    
   
    $( "body" ).delegate( ".js-search-by", "click", function(){
       var sel = $(this).html().trim().toLowerCase();   
      
         $(".js-letters").hide();
         $("."+sel+"-letter-search").show();
           
           

        
    });
   
   $(".js-regex").click(function(){
        members.search();
    });
   
   $(".js-alpha-sort").click(function(){
    
        var letter =  $(this).html();
        $("#js-search-members").val(letter);
        $('#js-regex-starts').prop('checked', true);
        $("#js-regex-contains").removeAttr('checked');
        
        members.search();
    
   });
   
    $("#js-search-members").keyup(function(){
        members.search();
    });    
    
    $(".js-search-by").click(function(){
           var sel = $(this).html().trim();   
           members.field = sel.toLowerCase();
           $("#js-current-search-field").html( sel );
           
           
           
    });
    
    $('.data-table').dataTable( {
		"sDom": "<'row'<'span4'l><'span4'f>r>t<'row'<'span3'i><'span4'p>>",
		"sPaginationType": "bootstrap",
		"oLanguage": {
			"sLengthMenu": "_MENU_ per page"
		}
     } );
   
    
});
