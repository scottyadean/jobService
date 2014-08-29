var sb = {ie:undefined}; 

var lightBox = {
    path:null,
    div:null,
    callback:null,
     
    show:function(div, label, options, method) {   
        
	if (options.remote != undefined && this.path != options.remote) {
            
	    var params = options.params != undefined ? options.params : {};
            var reqm = (method != undefined) ? method.toLowerCase() : 'get'; 
            var send = (reqm == 'post') ? asyncAction.sendPost : asyncAction.sendGet;
            
	    this.callback = options.callback != undefined ? options.callback : null;
            this.path = options.remote;
            this.div = div;     
            
	    $("#"+div+"Label").html(label);
            $('#'+div+"Body").html( "loading..." );
            //console.log(div);
	    send(this.path, params,  $.proxy(this.load, this), 'html');
                        
        }else{
            
           $('#'+div).modal('show');
        }   
    },
    
    load:function(data) {
	
	$('#'+this.div+"Body").html(data);
        $('#'+this.div).modal('show');
	
	if (typeof this.callback == 'function') {
	    this.callback.call(data);
	}
	
    },
    
    close:function(div) {
	$("#"+div).modal('hide');
    },
    
    dateFields:function() {
	
	 $(".date_widget").each(function(){
            
            var el = $(this);
                el.attr('readonly','readonly');

            var id = el.attr('id');
            var html = $("#"+id+"-element");

            var efield = html.html();
                         html.html("");
                                            
            var template = _.template($('#date-template').html());
            html.append(template({start:'now',field:efield, id:'date-picker'+id}));
	    $('#date-picker'+id).datepicker({format:'yyyy-mm-dd'}).on('changeDate', function(ev){}); 
        });
    },
    
    dateFieldsById:function(element, id) {
        
            var el = $(element);
                el.attr('readonly','readonly');

            var html = $(element+"-element");

            var efield = html.html();
                         html.html("");
                                            
            var template = _.template($('#date-template').html());
            html.append(template({start:'now',field:efield, id:'date-picker'+id}));
	    $('#date-picker'+id).datepicker({format:'yyyy-mm-dd'}).on('changeDate', function(ev){}); 
        
        
        
    }
    
};

var content = {
        load:function(path, params, callback, format) {
            
        
        if (params.get !== undefined) {
            asyncAction.sendGet(path, params, callback, format);
            return;
        }
            asyncAction.sendPost(path, params, callback, format);
        }
}; 

var asyncAction = {
    
    ele:{},
    sendPost:function( path, params, callback, format) {
        var reqf = format !== undefined ? format : 'json'; 
        $.post(path,
               params,
               callback,
               reqf).error(function(e){ console.log(e); });        
    },
    sendGet:function( path, params, callback, format) {
        var reqf = format !== undefined ? format : 'json'; 
        $.get(path,
              params,
              callback,
              reqf).error(function(e){ console.log(e); });  
    },
    appendToDom:function(ele, path, params, method, format) {
        var reqf = format !== undefined ? format.toLowerCase() : 'html'; 
        var reqm = method !== undefined ? method.toLowerCase() : 'get';
        var send = reqm == 'post' ? this.sendPost : this.sendGet;
        this.ele = ele;
        send( path, params, this.appendToDomComplete, format ); 
        return true;
    },
    appendToDomComplete:function(html) {
        $("#"+asyncAction.ele.id).append(html);
        if (typeof asyncAction.ele.callback == 'function') {
          asyncAction.ele.callback( asyncAction.ele.id, html );
        }
    }
};



var helpers = {
    
    params:null,

    formatDate:function(date_str, format) {
        var f = format === undefined ? "dddd, mmmm dS, yyyy" : format; 
        var d = new Date(date_str);
        return dateFormat(d, f);
    },

    popover:function(id) {
        $('#popover-'+id).popover();
    },	

    editIcon:function(element) {
    
        var ele = $(element);
    
        //onmouse over    
        ele.mouseenter(function () {
            ele.find("div:last").append($("<p class='pull-right'><i class='icon-edit'> </i> <small>edit</small></p>"));
        });
    
        //on mouse out
        ele.mouseleave(function () {
            ele.find("p:last").remove();
        });

    },
    
    wysiwyg:function() {
         $('.wysiwyg').summernote({height: 200});
    },
    
    inlineEdit:function(val, pk, url, name, title, type, src, style, id){
        
        id      = helpers.empty(id) ? "edit-"+pk : id;
        name    = helpers.empty(name) ? "" : this.escapeHtml(name);
        title   = helpers.empty(title) ? "" : this.escapeHtml(title);
        type    = helpers.empty(type) ? "text" : this.escapeHtml(type);
        style   = helpers.empty(style) ? "editable" : this.escapeHtml(style);
        src     = helpers.empty(src) ? "" : "data-source='"+this.escapeHtml(src)+"'";
        
        return "<a class='"+style+"' id='"+id+"' "+src+" data-name='"+name+"' data-type='"+type+"' data-pk='"+pk+"' data-url='"+url+"' data-title='"+title+"' >"+val+"</a>";

   },
   
   randomString:function (length, chars) {
        var mask = '';
        if (chars.indexOf('a') > -1) mask += 'abcdefghijklmnopqrstuvwxyz';
        if (chars.indexOf('A') > -1) mask += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (chars.indexOf('#') > -1) mask += '0123456789';
        if (chars.indexOf('!') > -1) mask += '~`!@#$%^&*()_+-={}[]:";\'<>?,./|\\';
        var result = '';
        for (var i = length; i > 0; --i) result += mask[Math.round(Math.random() * (mask.length - 1))];
        return result;
    },

    escapeHtml:function(str) {
        try{
            if ($.isNumeric(str)) {
                return parseFloat(str);
            }
            
            if (str !== undefined) {
                return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");   
            }
            
        }catch(e){
        
            console.log(e);
        
        }
        
        return '';
    },

    empty:function(str) {
      
      if (str === undefined) {
         return true;
      }
      
      if(typeof str === 'undefined'){
        return true;
      }

      if (str === false) {
        return true;
      }
      
      if (str !== undefined && str.trim() == '') {
        return true;
      }
      
      return false;
        
      
    },

    noSpecialChars:function(str) {
        
        return (typeof str == undefined) ? '' : str.replace(/[^\w\s]/gi, '').trim();
    
    },
    
    strFunction:function(functionName, context, args) {
        var namespaces = functionName.split(".");
        var func = namespaces.pop();
        
        try{
            if (typeof context === undefined) {
                return false;
            }
            for(var i = 0; i < namespaces.length; i++) {
                context = context[namespaces[i]];
            }
            if (typeof context[func] !== 'function') {
                console.log(functionName+" not found");
                return false;
            }
            
            return context[func].apply(this, args);
        
        }catch(e){
            console.log(e);
            return false;
        }
    },

    onOff:function(v) {
        return "<i class='ico "+v+"' title='"+v+"'> </i>";
    },
    
    
    input:function(type, style, id, args) {
      return "<input type='"+type+"' class='"+style+"' id='"+id+"' "+args+" />";
    },
    
    params:function(string) {
           
           var array = string != undefined && string.length > 0 ?  string.split("&") : false; 
           var values = {};
           
           if (array == false) {
               return {};
           }
           for(var key in array){
               
               var v = array[key];
               
               if (v != '') {
                   var p = v.split("=");
                   values[p[0]] = p[1];
               }
           };
           
          return values; 
       
     },
    
    
    pagination:function(path, parent, params, target){
        
        $( "body" ).undelegate( parent+" .asyncClick", "click", $.proxy(helpers.page, this) );
        
        this.params = params;
        this.parent = $(parent);
        this.total = parseInt(this.parent.attr('data-pages'));
        this.path  = path;
        this.target = $(target);
        
        $("body").delegate(parent+" .asyncClick", "click", $.proxy(helpers.page, this) );        
                      
    },
    
    page:function(event){
        
            event.stopPropagation();
            this.parent.find('li').removeClass('active');
            
            var curr  = parseInt(this.parent.attr('data-current'));
            var div = $(event.currentTarget);          
            var page = div.attr("data-page");
        
            if (!isNaN(page)) {
                var move = page;
                this.parent.attr('data-current', page);
                div.parent().addClass('active');
            }else{
                var move = curr;
                if (page == 'next') {
                    if (curr < this.total) {
                         move = curr + 1;
                         this.parent.attr('data-current', move);
                    }
                }else{
                    if (curr > 1) {
                        move = curr - 1;
                        this.parent.attr('data-current',  move);
                    }
                }
                $("a[data-page="+move+"]").parent().addClass('active');
            }
            move > 1 ?  this.parent.find(".page-back").parent().removeClass('disabled') : this.parent.find(".page-back").parent().addClass('disabled');         
            move >= this.total ? this.parent.find(".page-next").parent().addClass('disabled') : this.parent.find(".page-next").parent().removeClass('disabled');
            
            var callback = $.proxy(function (data) { this.target.html(data); }, this ); 
            
            if( curr != move){
                $.post(this.path+move, this.params, callback,'html').error(function(e){ console.log(e); });
            }

    },
    
    DragData:{

	prefix:'js-drag-selected-',
	exams:[],

	add:function(scope, id) {
	   this[scope].push(id);
	   this.update(scope);           
	},
	
	update:function(scope) {
		$('#'+this.prefix+scope).val(this[scope].join(","));
	},
	
	remove:function(scope, id) {
		this.exams = _.without(this[scope], id);
		$("#drag-"+scope+"-"+id).parent().remove();
		this.update(scope);
	},
	
        addEvent:function(id) {
               $( "#js-exams-index tr:first-child").draggable({
               cursor: "move",
               scope:"exams",
               cursorAt: { top: 0, left: 1 },
               helper: 'clone',
               });     
	}
    },
    
    
    getUrlParam:function(p) {
        
        var url = window.location.search.substring(1);
        var args = url.split('&');
        for (var i = 0; i < args.length; i++) 
        {
            var name = args[i].split('=');
            
            if (name[0] == p)  {
                return name[1];
            }
        }
        
        return null;

    }
    
};

String.prototype.trunc = String.prototype.trunc ||
      function(n){
          return this.length>n ? this.substr(0,n-1)+'&hellip;' : this;
      };

/*
var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
g.src='//www.google-analytics.com/ga.js';
s.parentNode.insertBefore(g,s)}(document,'script'));
*/


$(document).ready(function() {
    
 if( $('.js-favs-list').size() > 0 ) {    
    content.load('/favs', {'query':true}, function(data){

        var el = $(".js-favs-list");
            
        if (typeof data.query == 'object') {
            if (!$.isEmptyObject(data.query)) {
                el.removeClass('hidden');
            }
            for (var key in data.query) {
              el.append( "<div class='badge'><a class='white-txt' href='/providers/view/id/"+data.query[key]['id']+"'>"+ data.query[key]['name'] +"</a></div>" );  
            }
        }
    
    }, 'json' ); 
    };
    
});



