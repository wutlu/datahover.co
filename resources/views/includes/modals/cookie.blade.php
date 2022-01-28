<div id="cookieModal" class="modal fade" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog modal-sm modal-dialog-centered align-items-end">
		<div class="modal-content shadow border-0 rounded-0">
			<div class="modal-body">
				<p class="mb-0">We use cookies to personalise content and ads, to provide social media features and to analyse our traffic.</p>
				<a title="I agree" href="#" data-bs-dismiss="modal" class="text-nowrap">I agree</a>
			</div>
		</div>
	</div>
</div>

@push('js')
	$(window).on('load', function() {
		let cookieModal = $('#cookieModal').modal({
			backdrop: 'static',
			keyboard: false
		})

		if (!$.cookie('cookies'))
			cookieModal.modal('show')

		cookieModal.on('hidden.bs.modal', function () {
			$.cookie('cookies', true, { path: '/' });
		})
	})
@endpush
