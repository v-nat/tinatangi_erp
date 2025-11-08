<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update to Mandatory Contributions</title>
</head>
<body style="margin: 0; padding: 0; background-color: #1a1816; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; border-collapse: collapse; background-color: #2a2826; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">

                    <tr>
                        <td align="center" style="background-color: #1a1816; padding: 40px 20px;">
                            <img src="https://tinatangi.site/logo.png" alt="Tinatangi Cafe" width="120" style="display: block; width: 120px;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 30px;">

                            <h1 style="font-family: Georgia, 'Times New Roman', Times, serif; color: #cda45e; margin: 0 0 20px 0; font-size: 28px; font-weight: bold;">
                                Notice of Update to Mandatory Contributions
                            </h1>

                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 0 0 15px 0; font-size: 16px; line-height: 1.6;">
                                Hello,
                            </p>

                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 0 0 25px 0; font-size: 16px; line-height: 1.6;">
                                Please be advised that the company-wide mandatory contributions have been updated. The new monthly amounts are as follows:
                            </p>

                            <div style="background-color: #1a1816; border: 1px solid #444; border-radius: 5px; padding: 20px; font-size: 16px; line-height: 2.0; color: #eeeeee;">
                                <strong style="color: #cda45e;">SSS:</strong>
                                <span style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 18px; color: #eeeeee;">₱{{ number_format($contributions['sss'], 2) }}</span>
                                <br>
                                <strong style="color: #cda45e;">PhilHealth:</strong>
                                <span style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 18px; color: #eeeeee;">₱{{ number_format($contributions['philhealth'], 2) }}</span>
                                <br>
                                <strong style="color: #cda45e;">Pag-IBIG:</strong>
                                <span style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 18px; color: #eeeeee;">₱{{ number_format($contributions['pagibig'], 2) }}</span>
                            </div>

                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 25px 0 25px 0; font-size: 16px; line-height: 1.6;">
                                These changes will be reflected in the upcoming payroll period.
                            </p>

                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 25px 0 0 0; font-size: 16px; line-height: 1.6;">
                                Thanks,<br>
                                {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background-color: #1a1816; padding: 30px 20px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #aaa; font-size: 14px;">
                            <p style="margin: 0 0 10px 0; color: #cda45e; font-size: 18px; font-family: Georgia, 'Times New Roman', Times, serif;">{{ config('app.name') }}</p>
                            <p style="margin: 0;">&copy; {{ date('Y') }} Tinatangi Cafe. All Rights Reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
