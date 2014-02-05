var CommonConfirmModal= Backbone.View.extend({
    tagName: 'div',
    
    className: 'modal hide fade modal-box',
    
    events: {
        'hidden': 'onHideModal'
    },
    
    initialize: function(){
        this.$el
            .attr('role', 'dialog')
            .attr('tabindex', "-1");
        
        this.rendered = false;
    },
    
    onHideModal: function(e) {
        this.remove();
    },
    
    render: function(){
        var prototype = ich.common_confirm_modal_prototype();
        this.$el.html(prototype.html());
        
        this.$el.appendTo($('body'));
        
        this.$el.find('.modal-header h3').html(this.header || ''); // set the header
        this.$el.find('.modal-body').html(this.body || ''); // set the body
        this.$el.find('.modal-footer').html(this.footer || '');
        
        this.rendered = true;
    },
    
    hide: function() {
        this.$el.modal('hide');
    },
    
    show: function() {
        if (!this.rendered) {
            this.render();
        }
        this.$el.modal('show');
    }
    
});