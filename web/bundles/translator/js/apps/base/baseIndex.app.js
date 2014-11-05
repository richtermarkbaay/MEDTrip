var BaseIndexApp = Backbone.View.extend({

    events: {
        'submit form#form_search_filter': 'onSubmitSearchFilterForm',
        'click .pagination-container ul.pagination > li > a': 'onClickPagination'
    },
    
    initialize: function(options) {
        this.collectionContainer = this.$el.find('.collection-container');
        
        this.customInitialization(options);
        
        this.listenTo(this.collection, 'add', this.onAddItem);
        this.listenTo(this.collection, 'reset', this.onResetCollection);

        this.fetchCollection({});
    },
    
    customInitialization: function(options){},
    
    getItemView: function(options){
        alert('override this function');
    },
    
    onClickPagination: function(e) {
        e.preventDefault();
        if(!$(e.target).parent('li').hasClass('active')){
            var form = this.$el.find('form#form_search_filter');
            var filters = {
                name: form.find('input[name="filters[name]"]').val(),
                page : $(e.target).attr('page')
            };
            this.fetchCollection(filters);
            window.scrollTo(0,0);
        }
    },
    
    onSubmitSearchFilterForm: function(e) {
        e.preventDefault();
        var form = this.$el.find('form#form_search_filter');
        var filters = {
            name: form.find('input[name="filters[name]"]').val()
        };
        
        this.fetchCollection(filters);
    },
    
    onAddItem: function(item) {
        var view = this.getItemView(item);
        view.render();
        
        this.collectionContainer.append(view.$el);
    },
    
    onResetCollection: function() {
        this.collection.each(this.onAddItem, this);
    },
    
    fetchCollection: function(filters){
        var loader = $('<tr class="info"><td colspan="4"><img src="/images/admin/loading.gif"></td></tr>');
        this.collectionContainer.html('');
        this.collectionContainer.append(loader);
        var me = this.$el;
        this.collection.fetch({
            reset: true,
            data: filters,
            success: function(collection, response, options){
                loader.remove();
                if (0 == collection.length) {
                    var tr = $('<tr class="warning"></tr>').append($('<td colspan="4"></td>').html('No items.'));
                    options.collectionContainer.append(tr);
                }
                if(response.isPaginable){
                    me.find('.pagination-container').html(response.pagination);
                }
                else{
                    me.find('.pagination-container').empty();
                }
            },
            error: function(){
                loader.remove();
            },
            collectionContainer: this.collectionContainer
        });
    }
});