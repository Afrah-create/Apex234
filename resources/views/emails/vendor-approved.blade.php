<h2>Congratulations, your vendor account has been approved!</h2>
<p>You can now log in to the Caramel Yogurt Management System using the following credentials:</p>
<ul>
    <li><strong>Email:</strong> {{ $user->email }}</li>
    <li><strong>Password:</strong> {{ $password }}</li>
</ul>
<p>For security, please log in and change your password as soon as possible.</p>
<p><a href="{{ url('/login') }}">Click here to log in</a></p>
<p>Thank you,<br>The Caramel Yogurt Admin Team</p> 