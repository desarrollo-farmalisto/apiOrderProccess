<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación Ordenes de traslado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header_warning {
            background-color: rgb(247, 157, 22);
            color: #ffffff;
            padding: 10px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .header_success {
            background-color: rgb(28, 150, 103);
            color: #ffffff;
            padding: 10px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .header_error {
            background-color: rgb(151, 5, 36);
            color: #ffffff;
            padding: 10px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
            line-height: 1.6;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding: 10px;
        }



        .btn {
            display: inline-block;
            padding: 10px 20px;
            color: #ffffff;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header_success">
            <h2>Notificación</h2>
        </div>
        <div class="content">
            <p>Se remite el reporte en Excel con las órdenes de traslado correspondientes a la semana anterior.</p>
            <p>Gracias por utilizar nuestro servicio. </p>
            <p>Si tiene alguna pregunta o requiere asistencia adicional, no dude en ponerse en contacto con nosotros.
            </p>

            <p>Atentamente,<br>El equipo de desarrollo</br></p>

            <div class="footer">
                <p>&copy; {{ now()->year }} Distribuciones AXA S.A.S | Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>

</html>