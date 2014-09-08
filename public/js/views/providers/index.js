
var members = {
    
        path:"/providers/search/",
    
        field:'name',
    
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

$(document).ready(function(){
    

    
    
    $( "body" ).delegate(".nav-tabs a","click", function () {
       
       var tab = $(this).attr("href");
       
       content.load('/providers/tab', {id:tab},
                        function(data){
                            //console.log(data);
                        }, 'json');
       
       
    });       
   
    
    
    $( "body" ).delegate(".member-item-click","mouseenter", function () {
        $(this).find("div.attach-to").append($("<p class='pos-abs pos-right pos-top'><i class='icon-search'></i></p>"));
    });
        
    $( "body" ).delegate(".member-item-click","mouseleave", function () {
        $(this).find("p:first").remove();
    });       
   
    /*
    
     $( "body" ).delegate(".search-to-show","click", function () {
        var show = $(this).attr("data-show");
        console.log(show);
        //.append($("<p class='pos-abs pos-right pos-top'><i class='icon-search'></i></p>"));
    });
   */
   
    $( "body" ).delegate( ".js-search-by", "click", function(){
      
    
       var sel =  $(this).attr("data-href");   
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
           members.field =  $(this).attr("data-href");
           var html = $(this).attr("data-label");
           $("#js-current-search-field").html(html);
           members.search();
           //js-search-members

           
    });
    
    /*
    $('.data-table').dataTable( {
		"sDom": "<'row'<'span4'l><'span4'f>r>t<'row'<'span3'i><'span4'p>>",
		"sPaginationType": "bootstrap",
		"oLanguage": {
			"sLengthMenu": "_MENU_ per page"
		}
     } );
   */
    
    
    
    
    
    var __pagination = {tab1:__tabSelected["#tab1"] == 'acitve' ? true : false,
                        tab2:__tabSelected["#tab2"] == 'acitve' ? true : false,
                        tab3:__tabSelected["#tab3"] == 'acitve' ? true : false};
    
    
    
    if (__pagination.tab1 == true) {
        
        var ___inittab = 1;
        
    }else if( __pagination.tab2 == true ) {
        
        var ___inittab = 2;
        
    }else if( __pagination.tab2 == true ){
        
        var ___inittab = 3;
    }else{
        
        var ___inittab = 0;
        
    }
    
    
    if (___inittab  !=  0) {
     
        $('#page'+___inittab).sweetPages({perPage:20, prefix:1});
        var controls = $('.swControls'+___inittab ).detach();
        controls.appendTo('#page'+___inittab);

    }
    
    
    $(".tabContainer1").click(function(){
        if (__pagination.tab1 != true) {
             __pagination.tab1 = true;
            setTimeout(function(){
                $('#page1').sweetPages({perPage:20, prefix:2});
                var controls = $('.swControls1').detach();
                controls.appendTo('#page1');
                },100);
        }
    });
    
    $(".tabContainer2").click(function(){
        if (!__pagination.tab2 != true) {
             __pagination.tab2 = true;
            setTimeout(function(){
                $('#page2').sweetPages({perPage:20, prefix:2});
                var controls = $('.swControls2').detach();
                controls.appendTo('#page2');
                },100);
        }
    });

    $(".tabContainer3").click(function(){
        if (!__pagination.tab3 != true) {
            __pagination.tab3 = true;
            setTimeout(function(){
                $('#page3').sweetPages({perPage:20, prefix:3});
                var controls = $('.swControls3').detach();
                controls.appendTo('#page3');
            },100);
        }    
    
    });
    
    
    $(".industry-view").click(function(){ 
        lightBox.show('mainModal', 'Industry', {'remote':'/providers/industry/id/'+$(this).data('id')}, 'post');
    });
    
    $(".view-subcategories").click(function(){
        var el = $(this).attr('data-parent');    
        $("#children-of-"+el).slideToggle( "slow", function(){});
        
    });

    
    
});
