<div id="{{ "$name-modal" }}" class="modal" aria-hidden="true" aria-labelledby="{{ "$name-modal" }}" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content shadow border-0 rounded-0">
			<div class="modal-header border-0 mb-0">
				<h5 class="modal-title">{{ $title }}</h5>
				<a href="#" class="btn-close rounded-circle" data-bs-dismiss="modal" aria-label="Close"></a>
			</div>
			<ul class="list-group list-group-flush">
				@foreach ($lines as $line)
					<li class="list-group-item bg-transparent border-0">- {!! $line !!}</li>
				@endforeach
			</ul>
		</div>
	</div>
</div>
