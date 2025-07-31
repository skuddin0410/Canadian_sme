<!DOCTYPE html>
<html>

<head>
  <title>Welcome to {{config('name')}}</title>
</head>

<body>
  <h2> Hi, {{$user->username ?? ''}}</h2>
  <h3>Your Email : {{$user->email ?? ''}}</h3>
  <h3>Your Password : {{$user->user_password ?? ''}}</h3>
  <h3>Your Referral coupon : {{$user->referral_coupon ?? ''}}</h3>
  <p>Login here ? <a href="{{route('login')}}">Click</a></p>
 
  
</body>

</html>