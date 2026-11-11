<!DOCTYPE html>

<html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <meta content="width=device-width" name="viewport"/>
        <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
        <title></title>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"/>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    </head>
    <body style="background-color: #d6d6d5;font-family: 'Roboto', sans-serif; padding:20px">
        <div class="container content-width" style="max-width: 700px;min-width:300px; margin:auto; border:2px solid #000000; ">
            <div style="text-align:center;background-color: #c7c719; padding: 30px; ">
                <img width="80" height="80" src="{{get_image($merchant->BusinessLogo,'business_logo',$merchant->id, true,true,"email")}}"/>
                <p style="font-size:40px;color:#000000; font-weight: 600;margin: 9px auto;">@lang("$string_file.account_deletion_scheduled_heading")</p>
            </div>
            <div style="padding:40px; ">
                <p style="text-align:center;font-size:20px;font-weight: 500;margin: 9px auto;">
                    @lang("$string_file.account_deletion_email_line1")
                </p>
                <table style=" margin:10px auto; border-collapse: collapse;width: 100%;">
                    <tbody>
                        <tr>
                            <td style="text-align:center;">
                                <h3>@lang("$string_file.account_deletion_date")</h3>
                                <p style="font-size:16px;color:#1d1c1cc4;">{{$deletion_date}}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p style="text-align:center;font-size:16px;font-weight: 400;margin: 9px auto;">
                    @lang("$string_file.account_deletion_email_line2") {{$deletion_date}}.
                </p>
                <p style="text-align:center;font-size:16px;font-weight: 400;margin: 9px auto;">
                    @lang("$string_file.account_deletion_email_line3")
                </p>
                <div style="vertical-align: middle; margin:30px 25px 0 25px; text-align:center;font-weight:normal;">
                    <p style="font-size:10px;padding-top:15px; padding-bottom:5px;color:#000000;margin:0">©{{$merchant->BusinessName}}! . @lang("$string_file.all_right_reserved")</p>
                </div>
            </div>
        </div>
    </body>
</html>
