<h2>Hola {{ $application->candidate_name }},</h2>

<p>Tu candidatura para la oferta <strong>"{{ $application->jobOffer->name }}"</strong> publicada por <strong>{{ $application->jobOffer->company->name }}</strong> ha sido actualizada.</p>

<p><strong>Descripción del puesto:</strong></p>
<p>{{ $application->jobOffer->description }}</p>

<hr>

<p><strong>Estado actual de tu candidatura:</strong> {{ ucfirst($application->state) }}</p>
<p>{{ $customMessage }}</p>

<hr>
<p>Gracias por tu interés.</p>
<p>— El equipo de SkillUp</p>
