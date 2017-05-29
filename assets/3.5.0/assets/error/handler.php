<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, user-scalable=no"/>
		<style type="text/css">
            body {
                background-color: #e0e0e0;
                font-family: Geneva,Tahoma,Verdana,sans-serif;
                font-size: 14px;
                font-weight: 400;
                font-style: normal;
                font-variant: normal;
                line-height: 22px;
                margin: 0;
                padding: 0;
            }

            div#container {
                margin: 0;
                padding: 0;
                position: absolute;
                width: 100%;
                height: 100%;
            }

            div#copyright-master {
                position: absolute;
                bottom: 0;
                right: 0;
                padding: 10px;
            }

            div#copyright-master span {
                color: #606060;
                font-size: 11px;
                text-decoration: none;
            }

            div#copyright-master span.dev {
                color: #808080;
            }

            div#box {
                background-color: #ffffff;
                border: 0;

                -webkit-box-shadow:  1px  1px 3px rgba(0, 0, 0, 0.2);
                   -moz-box-shadow:  1px  1px 3px rgba(0, 0, 0, 0.2);
                    -ms-box-shadow:  1px  1px 3px rgba(0, 0, 0, 0.2);
                     -o-box-shadow:  1px  1px 3px rgba(0, 0, 0, 0.2);
                        box-shadow:  1px  1px 3px rgba(0, 0, 0, 0.2);

                margin: 0;
                margin-top: 5%;
                margin-left: 3%;
                margin-right: 3%;
                padding: 0;
                word-wrap: break-word;
                overflow: hidden;
            }

            div.label {
                border: 0;
                border-bottom: 1px solid #efefef;
                margin: 0;
                padding: 20px;
                padding-left: 15px;
                padding-right: 15px;
            }

            div.message {
                border: 0;
                margin: 0;
                padding: 15px;
                padding-left: 20px;
                padding-right: 20px;
            }

            div#box span,
            div#box strong {
                color: #707070;
                font-family: Geneva,Tahoma,Verdana,sans-serif;
                font-size: 15px;
                font-variant: normal;
            }
        </style>
		<title><?php echo $title; ?></title>
	</head>
	<body>
		<div id="container">
			<div id="box">
				<div class="label">
					<strong class="label"><?php echo $label; ?></strong>
				</div>
				<div class="message">
					<span class="message"><?php echo $message; ?></span>
				</div>
			</div>
			<div id="copyright-master">
				<span>&copy; 2017 <span class="dev">IzeroCs</span></span>
			</div>
		</div>
	</body>
</html>