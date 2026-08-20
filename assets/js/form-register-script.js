$(function(){
	$(document).on('submit', '.email-register-form, .form-information', function() {

		let button = $(this).find('button[type=submit]');

		let loading = SkilldoUtil.buttonLoading(button);

		let form = $(this);

		let data = $(this).serializeJSON();

		data.action = 'FormRegister\\Ajax\\Web\\FormRegisterAjax::register';

		loading.start()

		request.post(ajax, data).then(function(response) {

			SkilldoMessage.response(response);

			loading.stop()

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