!function(i){i.redshopAlert=function(t,n,e){var p=null,a=t,d=n,s="success";void 0!==e&&(s=e),this.init=function(){null==p&&(i("#redshop-alert-wrapper").length<=0?(p=i("<div>").attr("id","redshop-alert-wrapper").css({display:"none",position:"fixed",top:"70px",right:"2%"}),i("<div>").append(i("<h4>")).append(i("<p>")).appendTo(p),p.appendTo(i("body"))):p=i("#redshop-alert-wrapper"))},this.display=function(){p.fadeIn("slow",function(){window.setTimeout(function(){p.fadeOut("slow")},5e3)})},this.prepare=function(){var t=i(p.children("div")[0]);t.attr("class","").addClass("callout callout-"+s),t.find("h4").html(a),t.find("p").html(d)},this.init(),this.prepare(),this.display()}}(jQuery);