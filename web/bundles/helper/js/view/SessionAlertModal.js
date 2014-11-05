var SessionAlertModal = Bootstrap3ConfirmModal.extend({
	logoutPath : '/logout',
	initialize: function(options){
		this.logoutPath = options.logoutPath;
	},
    events: {
        'click .btn': 'onCloseAlert'
    },
	onCloseAlert: function(e){
		e.preventDefault();
		// log out user to redirect to login page
		window.location.replace(this.logoutPath);
	}
});