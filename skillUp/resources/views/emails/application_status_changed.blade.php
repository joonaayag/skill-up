<h2>{{ __('messages.email.hello') . $application->candidate_name }},</h2>

<p>{{ __('messages.email.text-1') }} <strong>"{{ $application->jobOffer->name }}"</strong> {{ __('messages.email.text-2') }} <strong>{{ $application->jobOffer->company->name }}</strong> {{ __('messages.email.text-3') }}</p>

<p><strong>{{ __('messages.email.text-4') }}</strong></p>
<p>{{ $application->jobOffer->description }}</p>

<hr>

<p><strong>{{ __('messages.email.text-5') }}</strong> {{ ucfirst($application->state) }}</p>
<p>{{ $customMessage }}</p>

<hr>
<p>{{ __('messages.email.text-6') }}</p>
<p>{{ __('messages.email.text-7') }}</p>
