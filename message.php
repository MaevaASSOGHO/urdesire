<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer la liste des utilisateurs pour le select
$users = $pdo->query("SELECT user_id, full_name FROM users WHERE user_id != {$_SESSION['user_id']}")
             ->fetchAll();

// Envoi d'un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $recipient_id = $_POST['recipient_id'];
    $encrypted_content = $_POST['encrypted_content'];
    
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, recipient_id, encrypted_content) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $recipient_id, $encrypted_content]);
    
    $success = "Message envoyé avec succès!";
}

// Récupérer les messages reçus
$messages = $pdo->query("
    SELECT m.*, u.full_name as sender_name 
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.recipient_id = {$_SESSION['user_id']}
    ORDER BY m.sent_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Messagerie</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
         background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
    }
    
    .container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        animation: slideIn 0.5s ease-out;
        padding: 10px;
    }
    
    .header {
        background: linear-gradient(45deg, #4CAF50, #45a049);
        color: white;
        padding: 25px;
        text-align: center;
    }
    
    .header h1 {
        font-size: 1.8em;
        margin-bottom: 5px;
    }
    
    .message-form {
        padding: 30px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        color: #333;
        font-size: 1.1em;
    }
    
    select, textarea, input {
        width: 100%;
        padding: 14px;
        border: 2px solid #ddd;
        border-radius: 10px;
        font-size: 16px;
        font-family: inherit;
        transition: all 0.3s;
    }
    
    select:focus, textarea:focus, input:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
    }
    
    textarea {
        resize: vertical;
        min-height: 200px;
        line-height: 1.6;
    }
    
    .btn {
        background: linear-gradient(45deg, #4CAF50, #45a049);
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 50px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-block;
        margin-top: 10px;
    }
    
    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(76, 175, 80, 0.3);
    }
    
    .btn:active {
        transform: translateY(1px);
    }
    
    .btn-secondary {
        background: linear-gradient(45deg, #6c757d, #5a6268);
        margin-left: 15px;
    }
    
    .btn-secondary:hover {
        box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
    }
    
    .status {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-weight: bold;
        text-align: center;
    }
    
    .status.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .key-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-left: 4px solid #4CAF50;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .container {
            border-radius: 15px;
        }
        .message-form {
            padding: 20px;
        }
        .btn {
            width: 100%;
            margin-bottom: 10px;
        }
        .btn-secondary {
            margin-left: 0;
        }
    }
</style>
</head>
<body>
    <div class="container">
        <h1>Messagerie sécurisée</h1>
        <h2>Envoyer un message</h2>
        <form method="POST">
            <div class="input-group">
                <label>Destinataire:</label>
                <select name="recipient_id" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Message chiffré:</label>
                <textarea name="encrypted_content" required></textarea>
            </div>
            <button type="submit" name="send_message" class="btn">Envoyer</button>
        </form>
        
        <h2>Messages reçus</h2>
        <?php if (empty($messages)): ?>
            <p>Aucun message reçu.</p>
        <?php else: ?>
            <div class="message-list">
                <?php foreach ($messages as $message): ?>
                    <div class="message">
                        <strong>De: <?= htmlspecialchars($message['sender_name']) ?></strong>
                        <small>Le <?= $message['sent_at'] ?></small>
                        <div class="encrypted-message"><?= htmlspecialchars($message['encrypted_content']) ?></div>
                        <button onclick="decryptMessage('<?= htmlspecialchars($message['encrypted_content']) ?>')" class="btn">
                            Déchiffrer
                        </button>
                        <a href="dashboard.php" class="btn">← Retour</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.3.2/jsencrypt.min.js"></script>
    <script>
        // Fonctions pour le chiffrement/déchiffrement côté client
        function decryptMessage(encryptedContent) {
            // Récupérer la clé privée depuis le stockage local
            const privateKey = localStorage.getItem('rsa_private_key');
            
            if (!privateKey) {
                alert("Vous devez d'abord générer vos clés RSA dans la section clés.");
                return;
            }
            
            const decrypt = new JSEncrypt();
            decrypt.setPrivateKey(privateKey);
            const decrypted = decrypt.decrypt(encryptedContent);
            
            if (decrypted) {
                alert("Message déchiffré:\n\n" + decrypted);
            } else {
                alert("Impossible de déchiffrer ce message. Vérifiez votre clé privée.");
            }
        }
    </script>
    <script>
        // JS – RSA Encryption avant envoi du message
let recipientPublicKey = null;

// 1. Récupérer la clé publique du destinataire
function fetchPublicKey(recipientId) {
  fetch(`get_public_key.php?user_id=${recipientId}`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.public_key) {
        recipientPublicKey = forge.pki.publicKeyFromPem(data.public_key);
        console.log("Clé publique reçue pour l'utilisateur ", recipientId);
      } else {
        alert("Impossible de récupérer la clé publique du destinataire.");
      }
    });
}

// 2. Chiffrer le message avec forge.js
function encryptMessage(rawMessage) {
  if (!recipientPublicKey) {
    alert("Aucune clé publique chargée.");
    return null;
  }
  const encrypted = recipientPublicKey.encrypt(forge.util.encodeUtf8(rawMessage), 'RSA-OAEP');
  return forge.util.encode64(encrypted);
}

// 3. Avant de soumettre le formulaire
const form = document.getElementById("message-form");
form.addEventListener("submit", function (e) {
  e.preventDefault();
  const raw = document.getElementById("message-content").value;
  const encrypted = encryptMessage(raw);
  if (!encrypted) return;
  document.getElementById("encrypted_content").value = encrypted;
  form.submit();
});

// 4. Exécuter fetchPublicKey(user_id) à chaque changement de destinataire
const recipientSelect = document.getElementById("recipient_id");
recipientSelect.addEventListener("change", function () {
  const selectedId = this.value;
  if (selectedId) fetchPublicKey(selectedId);
});

    </script>
</body>
</html>