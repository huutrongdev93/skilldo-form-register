$(function(){
	$(document).on('submit', '.email-register-form', function() {

		let button = $(this).find('button[type=submit]');

		let loading = SkilldoUtil.buttonLoading(button);

		let form = $(this);

		let data = $(this).serializeJSON();

		data.action = 'Form_Register_Ajax::register';

		loading.loading()

		request.post(ajax, data).then(function(response) {

			SkilldoMessage.response(response);

			loading.success()

			if( response.status === 'success' ) {

				form.trigger("reset");

				if(response.is_redirect === true) {
					window.location.href = response.url_redirect;
				}
			}
		});

		return false;
	})
});