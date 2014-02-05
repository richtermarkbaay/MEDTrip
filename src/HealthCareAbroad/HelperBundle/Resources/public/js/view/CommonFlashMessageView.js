var CommonFlashMessageView = Backbone.View.extend({
    tagName: 'div',
    
    className: 'alert',
    
    /**
     * 
     */
    initialize: function(options){
        this.message = options.message || '';
        switch(options.type || 'success'){
            case 'error': 
                this.className = this.className+' alert-error';
                break;
            case 'warning':
                this.className = this.className+' alert-warning';
                break;
            default:
                this.className = this.className+' alert-success';
                break;
        }
        
    },
    
    /**
     * 
     */
    render: function(){
        
        this.$el.text(this.message);
        this.$el.attr('style', 'position: fixed;bottom: 1em;right: 1em;');
        this.$el.attr('class', this.className);
        
        return this;
    },
    
    /**
     * 
     */
    show: function(){
        this.render();
        $('body').append(this.$el);
        
        var _this = this;
        setTimeout(function(){_this.remove()}, 3500);
    }
});