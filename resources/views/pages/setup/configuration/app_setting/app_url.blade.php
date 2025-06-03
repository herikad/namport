<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @if ($crm_slug == 'about-us')
      <title>About Us</title>
    @elseif($crm_slug == 'privacy-policy')
    <title>Privacy Policy</title>
    @elseif($crm_slug == 'terms-and-conditions')
      <title>Terms and Conditions</title>
    @elseif($crm_slug == 'delete-account')
      <title>Delete Account</title>
    @endif
</head>

<body>
    @if ($crm_slug == 'about-us')
      {!! @$setting->about_us !!}
    @elseif($crm_slug == 'privacy-policy')
      {!! @$setting->privacy_policy !!}
    @elseif($crm_slug == 'terms-and-conditions')
      {!! @$setting->terms_and_conditions !!}
    @elseif($crm_slug == 'delete-account')
      {!! @$setting->delete_account !!}
    @else
      <h4>No data found!</h4>
    @endif
</body>

</html>
