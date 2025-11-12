<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Work Schedule Update' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #1a1816; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; border-collapse: collapse; background-color: #2a2826; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                    <tr>
                        <td align="center" style="background-color: #1a1816; padding: 40px 20px;">
                            <img src="https://tinatangi.site/logo.png" alt="Tinatangi Cafe" width="120" style="display: block;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h1 style="font-family: Georgia, 'Times New Roman', Times, serif; color: #cda45e; margin: 0 0 20px 0; font-size: 28px; font-weight: bold;">
                                {{ $title ?? 'Work Schedule Update' }}
                            </h1>
                            <p style="color: #eeeeee; margin: 0 0 15px 0; font-size: 16px; line-height: 1.6;">
                                Hi {{ $name ?? 'Team Member' }},
                            </p>
                            <p style="color: #eeeeee; margin: 0 0 25px 0; font-size: 16px; line-height: 1.6;">
                                {{ $headline ?? 'Your work schedule has been updated.' }}
                            </p>
                            <div style="background-color: #1a1816; border: 1px solid #444; border-radius: 6px; padding: 20px; color: #f5f5f5; font-size: 15px; line-height: 1.7;">
                                <p style="margin: 0 0 12px 0;"><strong style="color: #cda45e;">Shift Title:</strong> {{ $scheduleTitle ?? 'Scheduled Shift' }}</p>
                                <p style="margin: 0 0 12px 0;"><strong style="color: #cda45e;">Working Days:</strong> {{ $daySummary ?? 'No working days specified.' }}</p>
                                <p style="margin: 0;"><strong style="color: #cda45e;">Shift Hours:</strong> {{ $timeRange ?? 'No timeframe specified.' }}</p>
                            </div>
                            @if(!empty($description))
                                <div style="margin-top: 25px;">
                                    <p style="color: #eeeeee; margin: 0 0 10px 0; font-size: 16px; line-height: 1.6;">
                                        <strong style="color: #cda45e;">Notes:</strong>
                                    </p>
                                    <p style="color: #eeeeee; margin: 0; font-size: 15px; line-height: 1.7;">
                                        {{ $description }}
                                    </p>
                                </div>
                            @endif
                            @if(($changeType ?? 'updated') !== 'removed' && !empty($actionUrl))
                                <p style="text-align: center; margin: 30px 0 0 0;">
                                    <a href="{{ $actionUrl }}"
                                       style="display: inline-block; padding: 12px 24px; background-color: #cda45e; color: #1a1816; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                                        View My Schedule
                                    </a>
                                </p>
                            @else
                                <p style="color: #bbbbbb; margin: 25px 0 0 0; font-size: 15px; line-height: 1.6;">
                                    We will notify you once a new schedule has been assigned. For questions, please reach out to your supervisor.
                                </p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="background-color: #1a1816; padding: 30px 20px; color: #aaa; font-size: 14px;">
                            <p style="margin: 0 0 10px 0; color: #cda45e; font-size: 18px; font-family: Georgia, 'Times New Roman', Times, serif;">Tinatangi Cafe ERP System</p>
                            <p style="margin: 0;">&copy; {{ date('Y') }} Tinatangi Cafe. All Rights Reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

