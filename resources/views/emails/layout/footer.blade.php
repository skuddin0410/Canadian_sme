            
            </td>
          </tr>

            <tr>
            <td class="content" style="padding:30px 25px; font-size:15px; line-height:1.6; color:#444;">
            <table role="presentation" align="center" cellspacing="0" cellpadding="0" border="0">
              <tr>
                <td align="center" style="padding:0 6px 10px 6px;">
                  <a href="{{ $androidUrl ?? 'https://play.google.com/store/apps/details?id=com.canadianSME.app' }}" target="_blank" style="display:block;">
                    <img src="{{ asset('images/playstore.png') }}" alt="Get it on Google Play" style="width:150px; height:auto; display:block; border:0;">
                  </a>
                </td>
                <td align="center" style="padding:0 6px 10px 6px;">
                  <a href="{{ $iosUrl ?? 'https://apps.apple.com/us/app/sme-summit-2025/id6753012008' }}" target="_blank" style="display:block;">
                    <img src="{{ asset('images/appstore.png') }}" alt="Download on the App Store" style="width:150px; height:auto; display:block; border:0;">
                  </a>
                </td>
                <td align="center" style="padding:0 6px 10px 6px;">
                  <a href="{{ $websiteUrl ?? config('app.url') }}" target="_blank" style="display:inline-block; min-width:150px; background:#002364; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700; line-height:1.2; padding:16px 18px; border-radius:6px; text-align:center; box-sizing:border-box;">
                    Visit Website
                  </a>
                </td>
              </tr>
            </table>
            </td>
            </tr>


          <tr>
            <td align="center" bgcolor="#002364" style="padding:15px; color:#ffffff; font-size:13px;">
              © {{ date('Y') }} | All Rights Reserved
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
