<!doctype html>
<html lang="en" title="">
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html">
    <title></title>
    <style>
        /* -------------------------------------
            GLOBAL RESETS
        ------------------------------------- */

        /*All the styling goes here*/

        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
            margin: 0 auto;
            border: 0;
            outline: none;
            text-decoration: none;
            display: block;
        }

        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
        }

        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }

        /* -------------------------------------
            BODY & CONTAINER
        ------------------------------------- */

        .body {
            background-color: #f6f6f6;
            width: 100%;
        }

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block;
            margin: 0 auto !important;
            /* makes it centered */
            max-width: 580px;
            padding: 10px;
            width: 580px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 580px;
            padding: 10px;
        }

        /* -------------------------------------
            HEADER, FOOTER, MAIN
        ------------------------------------- */
        .main {
            background: #ffffff;
            border-radius: 3px;
            width: 100%;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 20px;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .footer {
            clear: both;
            margin-top: 10px;
            width: 100%;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #8E8E93;
            font-size: 12px;
            text-align: center;
        }

        /* -------------------------------------
            TYPOGRAPHY
        ------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: #303637;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 10px;
            text-align: center;
        }

        h1 {
            font-weight: bold;
            font-size: 36px;
            line-height: 43px;
            text-transform: capitalize;
        }

        h2 {
            font-weight: bold;
            font-size: 20px;
            line-height: 24px;
            color: #303637;
        }

        p,
        ul,
        ol {
            font-family: sans-serif;
            font-size: 16px;
            line-height: 19px;
            font-weight: normal;
            color: #303637;
            margin: 15px 30px;
            text-align: center;
        }

        p li,
        ul li,
        ol li {
            list-style-position: inside;
            margin-left: 5px;
        }

        p.undernote {
            font-size: 13px;
            line-height: 16px;
            color: #303637;
        }

        p.undernote a {
            cursor: pointer;
            color: #8E8E93;
            text-decoration: none;
        }

        a {
            cursor: pointer;
            color: #3498db;
            text-decoration: underline;
        }

        /* -------------------------------------
            BUTTONS
        ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%;
        }

        .btn > tbody > tr > td {
            padding-bottom: 15px;
        }

        .btn table {
            width: auto;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #ce2e98;
            border-radius: 5px;
            box-sizing: border-box;
            color: #ce2e98;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
        }

        .btn-primary table td {
            background-color: #ce2e98;
        }

        .btn-primary a {
            background-color: #ce2e98;
            border-color: #ce2e98;
            color: #ffffff;
        }

        /* -------------------------------------
            OTHER STYLES THAT MIGHT BE USEFUL
        ------------------------------------- */
        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .align-center {
            text-align: center;
        }

        .align-right {
            text-align: right;
        }

        .align-left {
            text-align: left;
        }

        .clear {
            clear: both;
        }

        .mt0 {
            margin-top: 0;
        }

        .mb0 {
            margin-bottom: 0;
        }

        .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0;
        }

        hr {
            border: 0;
            border-bottom: 1px solid #EAEAEA;
            margin: 20px;
        }

        /* -------------------------------------
            RESPONSIVE AND MOBILE FRIENDLY STYLES
        ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table[class=body] .wrapper,
            table[class=body] .article {
                padding: 10px !important;
            }

            table[class=body] .content {
                padding: 0 !important;
            }

            table[class=body] .container {
                padding: 0 !important;
                width: 100% !important;
            }

            table[class=body] .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }

            table[class=body] .btn table {
                width: 100% !important;
            }

            table[class=body] .btn a {
                width: 100% !important;
            }

            table[class=body] .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
        }

        /* -------------------------------------
            PRESERVE THESE STYLES IN THE HEAD
        ------------------------------------- */
        @media all {
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
            }

            .btn-primary table td:hover {
                background-color: #ce2e98 !important;
            }

            .btn-primary a:hover {
                background-color: #ce2e98 !important;
                border-color: #ce2e98 !important;
            }
        }

    </style>
</head>
<body class="">
    <span class="preheader">---</span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
                <div class="content">

                    <!-- START CENTERED WHITE CONTAINER -->
                    <table role="presentation" class="main">

                        <!-- START MAIN CONTENT AREA -->
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <img src="{{ config('app.email.images.logo') }}" alt="{{ config('app.email.src_alt_name') }}" width="126"
                                         height="96" border="0" style="margin: 20px auto">
                                    </tr>
                                    <tr>
                                        <td>
                                            <h2>
                                                {{$title}}
                                                @if ($isVerified)
                                                    <img src="{{config('app.email.images.verified_badge')}}" alt="verified" style="margin-left: 6px;display: inline" width="18" height="18">
                                                @endif
                                            </h2>
                                            <p>{{$body}}</p>
                                            <div class="btn btn-primary" style="width: 100%;text-decoration: none;text-align: center">
                                                <a href="{{config('app.frontend_url')}}" target="_blank" style="width: 70%;text-decoration: none">
                                                    <strong>Open Waptap</strong>
                                                </a>
                                            </div>
                                            <hr/>
                                            <p class="undernote">
                                                <b>If you did not make this request, please contact us at</b>
                                                <a href="">{{ config('app.supportEmail') }}</a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- END MAIN CONTENT AREA -->

                    </table>
                    <!-- END CENTERED WHITE CONTAINER -->

                    <!-- START FOOTER -->
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    Zorg Media Inc, 8 The Green STE A
                                    <br>
                                    Dover, DE 19901 United States
                                    <br>
                                    Don't want to get our emails?
                                    <a href="{{ $unsubscribe_url }}" target="_blank" rel="noopener" style="color: #29b2e6; text-decoration: underline; line-height: inherit;">Unsubscribe</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- END FOOTER -->
                    
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
</body>
</html>


