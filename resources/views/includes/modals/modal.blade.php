<div
	id="{{ "$name-modal" }}"
	class="modal"
	aria-hidden="true"
	aria-labelledby="{{ "$name-modal" }}"
	tabindex="-1"
	data-bs-backdrop="{{ @$backdrop ?? 'true' }}"
	data-bs-keyboard="{{ @$keyboard ?? 'true' }}">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content shadow border-0 rounded-0">
			<div class="modal-header border-0 mb-0">
				<h5 class="modal-title">{{ $title }}</h5>
				<a href="#" class="btn-close rounded-circle" data-bs-dismiss="modal" aria-label="Close"></a>
			</div>
			<div class="modal-body">
				{{ $slot }}
			</div>
		</div>
	</div>
</div>
