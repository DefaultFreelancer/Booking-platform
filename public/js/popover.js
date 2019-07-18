
(function($){

    var pluginName = 'common_popover',
        popoverId ="popover"+ Math.random().toString(36).substr(2, 5);

    // These are the plugin defaults values
    var defaults = {
        title:'',
        content:'',
        placement:'bottom',
        trigger:'click',
        width:'200px'
    };

    var common_popover = function(element,options){

        this.element = $(element);
        this.popoverId = popoverId;
        this.options = $.extend({}, defaults, options);
        this.init();
        this.initTriggers();

        return this;
    };

    //************** Start create template  *************//
    common_popover.prototype.init = function(e) {
        var title= this.options.title,
            content='&nbsp;';

        if(this.options.content) content = this.options.content;

        var template = "<div class='popover' id='"+popoverId+"'>";
            template +="<div class='arrow'></div>";
            if(title) template +="<h3 class='popover-header'>"+title +"</h3>";
            template +="<div class='popover-body'>"+ content + "</div>";
            template +="</div>";

        this.htmlStr = template;
    };
    //************** End create template  *************//


    //************** Start events *************//
    common_popover.prototype.initTriggers = function(){
        var triggers = (this.options.trigger).split(" ");
        triggers = jQuery.unique(triggers);

        for(var i=0;i<triggers.length;i++) {
            triggers[i] === 'click' ? this.initClickTrigger() : '';
        }
    };

    common_popover.prototype.initClickTrigger = function(){

        var elem = this.element;
        $('body').on("click", '#'+this.element[0].id+'', function() {
            $(elem).common_popover("show");
        });
    };
    //************** End Events **********//


    //*******Start display popover *******//
    common_popover.prototype.display = function(option){

        var popoverId = "#"+this.popoverId;

        //Old popover remove
        $(popoverId).remove();

        //Append new popover in body
        if(!$(popoverId).length) {
            $("body").append(this.htmlStr);

            //Set new popover position
            this.setPopupPosition();
        }
    };
    //*******End display popover *******//


    //******Start popover position or offset function *****//

    // Calculate offsets
    common_popover.prototype.getOffsetRect = function(elem) {
        var box = elem.getBoundingClientRect();

        var body = document.body,
            docElem = document.documentElement,
            scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop,
            scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft,
            clientTop = docElem.clientTop || body.clientTop || 0,
            clientLeft = docElem.clientLeft || body.clientLeft || 0,
            top  = box.top +  scrollTop - clientTop,
            left = box.left + scrollLeft - clientLeft;

        return { top: Math.round(top), left: Math.round(left) }
    };

    common_popover.prototype.getOffset = function( elem ){
        if (elem.getBoundingClientRect) {
            return this.getOffsetRect(elem)
        }
    };

    common_popover.prototype.setPopupPosition = function(){

        $("#"+this.popoverId).css({width:this.options.width});

        var position = this.getOffset(this.element[0]),
            popoverPosition = $("#"+this.popoverId).position(),
            parentWidth = $(this.element).outerWidth(),
            parentHeight = $(this.element).outerHeight(),
            parentLeft = position.left,
            parentTop = position.top,
            popoverWidth = $("#"+this.popoverId).outerWidth(),
            popoverHeight = $("#"+this.popoverId).outerHeight(),
            left, top;

        if(this.options.placement === 'bottom' || this.options.placement === 'top'){

            var parentCenter = (parentWidth/2),
                popoverCenter = (popoverWidth/2);

            if(this.options.placement === 'bottom') top = parentTop + parentHeight;
            else top = parentTop - popoverHeight - 10;

            var pCenterLeft = parentLeft + parentCenter;

            left = pCenterLeft - popoverCenter;

        } else if(this.options.placement === 'left' || this.options.placement === 'right'){

            var parentCenter = (parentHeight/2),
                popoverCenter = (popoverHeight/2);

            if(this.options.placement === 'left') left = parentLeft - popoverWidth - 10;
            else left = parentLeft + parentWidth;

            var pCenterTop = parentTop + parentCenter;

            top = pCenterTop - popoverCenter;

        }

        //Get arrow class
        var placement = this.options.placement,
            arrowPlacementClass = "bs-popover-bottom";

        if(placement && placement === "right") arrowPlacementClass = "bs-popover-right";
        else if(placement && placement === "left") arrowPlacementClass = "bs-popover-left";
        else if(placement && placement === "top") arrowPlacementClass = "bs-popover-top";
        else arrowPlacementClass = "bs-popover-bottom";

        //Remove existing class for popover arrow
        $("#"+this.popoverId).removeClass("bs-popover-right bs-popover-top bs-popover-bottom bs-popover-left");

        //Add class and style
        $("#"+this.popoverId).css({"left":left+"px","top":top+"px"}).addClass(arrowPlacementClass);

    };
    //******End popover position or offset function *****//


    //******Start popover function *****//
    $.fn.common_popover = function(options){

        if (typeof options == 'string'){
            var obj = $(this).data(pluginName);

            if(jQuery.isEmptyObject(obj))
                return this;
            esw
            obj.display(options);
        }
        else{
            if(!$.data(this, pluginName)){

                var pop = new common_popover(this,options);
                $("#"+pop.element[0].id).data(pluginName,pop);
                $.data(this, pluginName, pop);

                return this;
            }
        }
    };
    //******End popover function *****//

}(jQuery));
