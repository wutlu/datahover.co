<div id="cookieModal" class="modal fade" aria-hidden="true" aria-labelledby="cookieModalLabel" tabindex="-1">
	<div class="modal-dialog modal-lg modal-dialog-centered align-items-end">
		<div class="modal-content shadow border-0 rounded-0">
			<div class="modal-body d-flex justify-content-between align-items-center gap-3">
				<p class="mb-0">We use cookies to personalise content and ads, to provide social media features and to analyse our traffic.</p>
				<a href="#" data-bs-dismiss="modal" class="btn btn-light rounded-0 text-nowrap">I agree</a>
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
