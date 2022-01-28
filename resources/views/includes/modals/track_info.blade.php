<div id="trackInfoModal" class="modal" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content shadow border-0 rounded-0">
			<div class="modal-header border-0 pb-0">
				<h5 class="modal-title">What is track?</h5>
				<a title="Close" href="#" class="btn-close rounded-circle" data-bs-dismiss="modal" aria-label="Close"></a>
			</div>
			<div class="modal-body">
				<div class="accordion" id="trackInfoAccordion">
					@foreach (config('sources') as $key => $item)
					<div class="accordion-item border-0 rounded-0">
						<div class="accordion-header">
							<button
								class="accordion-button rounded-0 shadow-none collapsed d-flex align-items-center gap-2"
								type="button"
								data-bs-toggle="collapse"
								data-bs-target="#track-info-{{ $key }}"
								aria-expanded="false"
								aria-controls="track-info-{{ $key }}">
								<img alt="{{ $item['name'] }}" src="{{ asset($item['icon']) }}" width="24" height="24" />
								{{ $item['name'] }}
							</button>
						</div>
						<div id="track-info-{{ $key }}" class="accordion-collapse collapse border-0" data-bs-parent="#trackInfoAccordion">
							<div class="accordion-body">
								<div class="d-flex align-items-center justify-content-center gap-5 mb-3">
									<div class="d-flex flex-column align-items-center justify-content-center">
										@php
											$first = true;
										@endphp
										@foreach (array_keys($item['tracks']) as $track)
											@if (!$first)
												<span class="text-muted">or</span>
											@endif
											<span>1 {{ $track }} tracking</span>

											@php
												$first = false;
											@endphp
										@endforeach
									</div>
									<span class="fw-bold">=</span>
									<span>1 track</span>
								</div>
								<p class="text-muted text-center mb-0">{{ $item['gains'] }}</p>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
