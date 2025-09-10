@if(!empty($user))
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin-top:20px;">
<tr>
  <td bgcolor="#004fb8" class="btn" style="border-radius:5px;">
    <a href="#"  style="display:inline-block; padding:12px 20px; font-size:14px; font-weight:bold; 
       color:#ffffff; text-decoration:none; border-radius:5px;">
       <img src="{{asset($user->qr_code)}}" alt="QR Code" style="max-width:150px; height:auto;">
    </a>
  </td>
</tr>
</table>
@endif


