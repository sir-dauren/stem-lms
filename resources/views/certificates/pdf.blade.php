<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #070b18;
            color: #ffffff;
            width: 100%;
            height: 100%;
        }
        .frame {
            margin: 18px;
            border: 3px solid #d3f256;
            border-radius: 16px;
            padding: 50px 60px;
            height: 520px;
            position: relative;
            background: #0b1020;
        }
        .brand { text-align: center; }
        .brand .logo {
            display: inline-block;
            width: 34px; height: 34px;
            background: #d3f256;
            color: #070b18;
            font-weight: bold;
            font-size: 20px;
            line-height: 34px;
            border-radius: 8px;
            text-align: center;
        }
        .brand .name {
            font-size: 20px;
            font-weight: bold;
            vertical-align: middle;
            margin-left: 8px;
        }
        .kicker {
            text-align: center;
            color: #d3f256;
            font-size: 12px;
            letter-spacing: 5px;
            text-transform: uppercase;
            margin-top: 40px;
        }
        .muted { color: #8891a8; font-size: 13px; text-align: center; }
        .recipient {
            text-align: center;
            font-size: 44px;
            font-weight: bold;
            margin-top: 12px;
            color: #ffffff;
        }
        .course {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #d3f256;
            margin-top: 6px;
        }
        .spacer-lg { margin-top: 34px; }
        .spacer { margin-top: 20px; }
        .footer {
            position: absolute;
            left: 60px; right: 60px; bottom: 44px;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding-top: 18px;
        }
        .footer td { vertical-align: bottom; }
        .signer { font-size: 16px; font-weight: bold; }
        .signer-title { font-size: 11px; color: #8891a8; }
        .meta { text-align: right; font-size: 11px; color: #8891a8; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="brand">
            <span class="logo">S</span><span class="name">{{ setting('site_name', 'STEMLY') }}</span>
        </div>

        <div class="kicker">Сертификат о прохождении курса</div>

        <p class="muted spacer-lg">Настоящим подтверждается, что</p>
        <div class="recipient">{{ $certificate->recipient_name }}</div>

        <p class="muted spacer">успешно завершил(а) курс</p>
        <div class="course">«{{ $certificate->course_title }}»</div>

        <div class="footer">
            <table width="100%">
                <tr>
                    <td>
                        <div class="signer">{{ setting('certificate_signer', 'Dr. Ada Quantum') }}</div>
                        <div class="signer-title">{{ setting('certificate_signer_title', 'Академический директор, STEMLY') }}</div>
                    </td>
                    <td class="meta">
                        № {{ $certificate->certificate_number }}<br>
                        {{ $certificate->issued_at->format('d.m.Y') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
