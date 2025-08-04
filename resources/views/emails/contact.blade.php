<h2>You've received a new message</h2>
<p><strong>From:</strong> {{ $data['sender']->name }} ({{ $data['sender']->email }})</p>
<p><strong>Message:</strong></p>
<p>{{ $data['message_body'] }}</p>