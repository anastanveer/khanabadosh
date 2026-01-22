<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light only">
  <title>{{ $title ?? config('app.name') }}</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f5f1ea;
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
      color: #2d2822;
    }
    .preheader {
      display: none !important;
      visibility: hidden;
      opacity: 0;
      color: transparent;
      height: 0;
      width: 0;
      overflow: hidden;
      mso-hide: all;
    }
    .wrapper {
      width: 100%;
      background: #f5f1ea;
      padding: 24px 0 36px;
    }
    .container {
      width: 100%;
      max-width: 640px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 18px;
      overflow: hidden;
      border: 1px solid #eadfce;
    }
    .header {
      padding: 28px 36px 16px;
      background: #f0e7d8;
      text-align: center;
    }
    .logo {
      width: 160px;
      height: auto;
      display: inline-block;
    }
    .content {
      padding: 24px 36px 12px;
    }
    h1 {
      font-size: 24px;
      line-height: 1.3;
      margin: 0 0 8px;
      font-weight: 600;
    }
    p {
      margin: 0 0 12px;
      line-height: 1.6;
      color: #3c352b;
    }
    .muted {
      color: #7a6f61;
      font-size: 14px;
    }
    .section-title {
      margin: 24px 0 12px;
      font-size: 16px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #5a5144;
    }
    .summary {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 18px;
    }
    .summary td {
      padding: 6px 0;
      vertical-align: top;
    }
    .summary .label {
      width: 38%;
      font-weight: 600;
      color: #4a4338;
    }
    .summary .value {
      text-align: right;
      color: #2f2922;
    }
    .items {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }
    .items th {
      text-align: left;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #7a6f61;
      padding-bottom: 8px;
      border-bottom: 1px solid #efe3d4;
    }
    .items td {
      padding: 10px 0;
      border-bottom: 1px solid #f3eadf;
      font-size: 14px;
      color: #2f2922;
    }
    .items td.amount,
    .items th.amount {
      text-align: right;
      white-space: nowrap;
    }
    .total-row td {
      padding-top: 14px;
      font-weight: 700;
      border-bottom: none;
    }
    .note {
      background: #f9f3ea;
      border: 1px solid #eadfce;
      border-radius: 12px;
      padding: 14px 16px;
      margin: 18px 0;
      font-size: 14px;
    }
    .cta {
      display: inline-block;
      margin: 8px 0 16px;
      padding: 10px 18px;
      background: #2d2822;
      color: #ffffff !important;
      text-decoration: none;
      border-radius: 999px;
      font-size: 13px;
      letter-spacing: 0.04em;
    }
    .footer {
      padding: 18px 36px 28px;
      text-align: center;
      font-size: 12px;
      color: #8a7d6c;
      line-height: 1.6;
    }
    .footer a {
      color: #6d5f4f;
      text-decoration: none;
    }
    @media (max-width: 640px) {
      .header,
      .content,
      .footer {
        padding-left: 20px !important;
        padding-right: 20px !important;
      }
      .summary td,
      .summary .value {
        display: block;
        text-align: left !important;
      }
      .summary .label {
        width: auto;
      }
      .items th.amount,
      .items td.amount {
        text-align: left;
      }
    }
  </style>
</head>
<body>
  <span class="preheader">{{ $preheader ?? '' }}</span>
  <table class="wrapper" role="presentation" cellspacing="0" cellpadding="0">
    <tr>
      <td align="center">
        <table class="container" role="presentation" cellspacing="0" cellpadding="0">
          <tr>
            <td class="header">
              <a href="{{ config('app.url') }}" aria-label="{{ config('app.name') }}">
                <img src="{{ asset('assets/brand/logo.avif') }}" alt="{{ config('app.name') }}" class="logo">
              </a>
            </td>
          </tr>
          <tr>
            <td class="content">
              <h1>{{ $title ?? 'Update' }}</h1>
              @if (!empty($subtitle))
                <p class="muted">{{ $subtitle }}</p>
              @endif
              @yield('content')
            </td>
          </tr>
          <tr>
            <td class="footer">
              <div>Need help? Reach us at <a href="mailto:info@khanabadoshfashion.ca">info@khanabadoshfashion.ca</a>.</div>
              <div>{{ config('app.name') }} · {{ config('app.url') }}</div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
