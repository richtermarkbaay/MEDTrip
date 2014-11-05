var Bootstrap3ConfirmModal= Backbone.View.extend({
    
    events: {
        'click button[type="submit"]': 'onClickConfirm',
        'hidden.bs.modal': 'onHideModal',
        'show.bs.modal': 'onShowModal'
    },
    
    hide: function() {
        this.$el.modal('hide');
    },
    
    show: function() {
        this.$el.modal('show');
    },
    
    onClickConfirm: function(e) {
        e.preventDefault();
    },
    
    onHideModal: function(e){
        this.remove();
    },
    
    onShowModal: function(e){},
    
});