<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

  <head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>

    <style>
      table, td, div, h1, p {font-family: Arial, sans-serif;}
    </style>

  </head>

  <body style="margin:0;padding:0;">
    <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
      <tr>
        <td align="center" style="padding:0;">
          <table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">

            <tr>
              <td align="center" style="padding:40px 0 30px 0;background:#e8e9f2;">
                <img src="" alt="" width="300" style="height:auto;display:block;" />
              </td>
            </tr>

            <tr>
              <td style="padding:36px 30px 42px 30px;">
                <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                
                  <tr>
                    <td style="padding:0 0 25px 0;color:#153643;">
                      <h1 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;text-align:center;">Forgot your password?</h1>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding:0 0 36px 0;color:#153643;">
                      <h5 style="font-size:18px;margin:0 0 20px 0;font-family:Arial,sans-serif;">Hello,</h5>
                      <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">We received a request to reset the password for the account associated with {{ @$data['username'] }}. </p>
                      <p>You can reset your password by clicking the link below:</p>
                      <div style="margin:0 0 12px 0;width: 100%;display: flex;justify-content: center;">
                        <a href="{{ url ('/password_reset/' .$data['email_token'])}}"  style="background: #263a96;border: #263a96;border-radius: 0.358rem;padding: 0.5rem 1.3rem;color: #fff;text-decoration: none;font-size: 1rem;font-weight: 500;line-height: 1.5;text-align: center;width: 50%;">Reset Password</a>
                      </div>       
                      <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">If you didn't request this, you can ignore this email.</p>             
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <b>Thanks,</b> <br>
                      <p>The {{ env('APP_NAME') }} team</p>
                    </td>
                  </tr>
                  
                </table>
              </td>
            </tr>

          </table>
        </td>
      </tr>
    </table>
  </body>
  
</html>
