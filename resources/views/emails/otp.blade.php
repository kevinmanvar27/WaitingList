<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Verification Code</title>
  <!-- Basic resets for email clients; most styling is inline for compatibility -->
  <style>
    /* Some clients honor head styles; leave minimal resets here */
    body { margin: 0; padding: 0; background-color: #F5F5F5; }
    table { border-collapse: collapse; }
    img { border: 0; outline: none; text-decoration: none; display: block; }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#F5F5F5; -webkit-font-smoothing:antialiased;">
  @php
    use Illuminate\Support\Str;
    $settings = \App\Models\Settings::getInstance();
    $appName = $settings->application_name ?? 'Waitinglist';
    $primary = '#FF6B00'; // Bright Orange (app primary)
  @endphp

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F5F5F5;" align="center">
    <tr>
      <td align="center" style="padding:32px 16px;">

        <!-- Card -->
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#FFFFFF; border-radius:12px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.06);">
          <!-- Brand bar / header -->
          <tr>
            <td style="background: {{ $primary }}; padding:20px 24px;">
              <table role="presentation" width="100%">
                <tr>
                  <td align="left" valign="middle">
                    @if(!empty($settings->logo))
                      @php
                        // Compute absolute logo URL; support both storage paths and absolute URLs
                        $logo = $settings->logo;
                        $isAbsolute = preg_match('/^https?:\\/\\//i', $logo);
                        $logoUrl = $isAbsolute ? $logo : (Str::startsWith($logo, ['storage/', 'public/']) ? url('/'.ltrim($logo, '/')) : url('/storage/'.$logo));
                      @endphp
                      <img src="{{ $logoUrl }}" alt="{{ $appName }}" height="40" style="height:40px; max-height:40px;" />
                    @else
                      <span style="font-family:Arial, Helvetica, sans-serif; font-size:20px; font-weight:700; color:#FFFFFF; letter-spacing:0.4px;">{{ $appName }}</span>
                    @endif
                  </td>
                  <td align="right" valign="middle">
                    <span style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#FFE5D1;">Secure Verification</span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Body content -->
          <tr>
            <td style="padding:28px 28px 8px 28px;">
              <h1 style="margin:0 0 12px 0; font-family:Arial, Helvetica, sans-serif; font-size:22px; line-height:28px; color:#111111;">Your verification code</h1>
              <p style="margin:0 0 16px 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:22px; color:#444444;">
                We received a request to sign in to your {{ $appName }} account using this email address (<strong style="color:#222">{{ $email }}</strong>).
              </p>
              <p style="margin:0 0 16px 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:22px; color:#444444;">
                Enter the following One‑Time Password (OTP) to continue:
              </p>
            </td>
          </tr>

          <!-- OTP block -->
          <tr>
            <td align="center" style="padding:4px 28px 20px 28px;">
              <table role="presentation" cellpadding="0" cellspacing="0" style="border:2px solid {{ $primary }}; border-radius:10px;">
                <tr>
                  <td style="padding:16px 28px;">
                    <span style="display:inline-block; font-family:Arial, Helvetica, sans-serif; font-size:32px; line-height:36px; font-weight:700; color:{{ $primary }}; letter-spacing:8px;">
                      {{ $otp }}
                    </span>
                  </td>
                </tr>
              </table>
              <div style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#777777; margin-top:10px;">Valid for 30 minutes</div>
            </td>
          </tr>

          <!-- Tips / notes -->
          <tr>
            <td style="padding:0 28px 8px 28px;">
              <p style="margin:0 0 12px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:21px; color:#444444;">
                If you didn’t request this code, you can safely ignore this email.
              </p>
              <p style="margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:20px; color:#666666;">
                For your security, never share this code with anyone.
              </p>
            </td>
          </tr>

          <!-- Divider -->
          <tr>
            <td style="padding:16px 28px 0 28px;">
              <hr style="border:none; border-top:1px solid #EEE; margin:0;" />
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:16px 28px 28px 28px;">
              <table role="presentation" width="100%">
                <tr>
                  <td align="left" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#888888;">
                    &mdash; {{ $appName }} Team
                  </td>
                </tr>
                <tr>
                  <td align="left" style="padding-top:6px; font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:17px; color:#999999;">
                    This email was sent from a notification‑only address. Please do not reply.
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <!-- /Card -->

        <!-- Spacer below card -->
        <div style="height:24px; line-height:24px;">&nbsp;</div>

      </td>
    </tr>
  </table>
</body>
</html>
