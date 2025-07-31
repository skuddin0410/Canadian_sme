<!DOCTYPE html>
<html>

<head>
  <title>KYC Status</title>
</head>

<body>
  <h2> Hi, {{$user->name ?? ''}}</h2>
  <h3>Your KYC has been: {{ !empty($user) && $user->kyc_verified == 1 ? "Approved" : "Rejected" }}</h3>
  @if(!empty($user) && $user->kyc_verified == 0)
  <h4 style="color:red">{{$user->reasons}}</h4>
  @endif
</body>

</html>