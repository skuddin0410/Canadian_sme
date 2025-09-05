<!DOCTYPE html>
<html>
  <head>
    <title>{{config('app.name')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
      table, tbody, tr, th, td {border: 0; border-collapse: collapse; }
    </style>
  </head>
  <body>
    <table style="width: 100%; font-family: Arial, sans-serif;">
      <tr>
        <td style="width: 100%">
          <table style="width: 600px; margin-left: auto; margin-right: auto; background: #fff; border-bottom: solid 6.67px rgb(235, 62, 45);">
            <tr>
              <td style="padding: 17px 0px 20px">
               @include('email.layout.header')
              </td>
            </tr>
            <tr>
              <td style="padding: 40px 57px; border-top: 2px solid rgb(235, 62, 45);">
                <table style="width: 100%; padding-top: 27px;">
                  <tr>
                    <td style="padding: 0px;">
                      <h2 style="margin: 0 0 20px; text-align: left; color: rgb(65, 65, 65); font-size: 20px; font-weight: bold; line-height: 20px;">Hi, {{$user->name ?? ''}} {{$user->lastname??''}}</h2>
                      <p style=style="margin: 0 0 20px; text-align: left; color: rgb(65, 65, 65); font-size: 20px; font-weight: bold; line-height: 20px;">Welcome to {{config('app.name')}}!</p>

                      <p style=style="margin: 0 0 20px; text-align: left; color: rgb(65, 65, 65); font-size: 20px; font-weight: bold; line-height: 20px;">Your email {{$user->email ?? ''}} and password {{$user->password ?? ''}}</p>

                      <p style=style="margin: 0 0 20px; text-align: left; color: rgb(65, 65, 65); font-size: 20px; font-weight: bold; line-height: 20px;"><a href="{{config('app.url')}}">Click here to login</a></p>

                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td style="padding: 0 57px; background: #414141;">
                @include('email.layout.footer')
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>