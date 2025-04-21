<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur 500 - Erreur serveur</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .content {
            padding: 50px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .title {
            font-size: 72px;
            margin-bottom: 20px;
        }
        .message {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .details {
            display: none;
            text-align: left;
            background-color: #eee;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            max-width: 600px;
            overflow: auto;
        }
        button {
            padding: 10px 20px;
            background-color: #636b6f;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #555;
        }
        pre{display:none;}
    </style>
</head>
<body>
    <div class="content">
        <div class="title">Oups !</div>
        <p class="message">Erreur interne du serveur</p>

        @if(auth()->user()->user_type=='admin')
            <a href="{{ route('adminhome') }}">Retour au tableau de bord</a><br><br>
        @else
            <a href="{{ route('home') }}">Retour au tableau de bord</a><br><br>
        @endif

        <button onclick="toggleDetails()">Afficher les détails de l'erreur</button>
        <div class="details" id="errorDetails">
            <?php
                // Récupérer l'exception d'origine
                $originalException = $exception;
                while ($originalException->getPrevious()) {
                    $originalException = $originalException->getPrevious();
                }

                // Obtenir les détails de l'exception d'origine
                $file = $originalException->getFile();
                $line = $originalException->getLine();
                $message = $originalException->getMessage();
                $trace = $originalException->getTraceAsString();
            ?>

            <p><strong>Message d'erreur :</strong> <?= $message ?? 'Aucun détail disponible' ?></p>
            <p><strong>Fichier :</strong> <?= $file ?></p>
            <p><strong>Ligne :</strong> <?= $line ?></p>
            <pre><?php $trace ?></pre>
        </div>
    </div>

    <script>
        function toggleDetails() {
            var details = document.getElementById('errorDetails');
            if (details.style.display === "none" || details.style.display === "") {
                details.style.display = "block";
            } else {
                details.style.display = "none";
            }
        }
    </script>
</body>
</html>
