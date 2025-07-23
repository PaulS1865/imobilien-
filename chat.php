<?php
// Nachricht speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $msg = strip_tags($_POST['message']);
    $timestamp = date('H:i');
    file_put_contents('chatlog.txt', "[$timestamp] $msg\n", FILE_APPEND);
    exit;
}

// Nachrichten abrufen
if (isset($_GET['load'])) {
    $messages = file_exists('chatlog.txt') ? file('chatlog.txt', FILE_IGNORE_NEW_LINES) : [];
    header('Content-Type: application/json');
    echo json_encode($messages);
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Einfacher Web-Chat</title>
  <style>
    body {
      background: #111;
      color: #fff;
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    .chat-box {
      background: #222;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      max-height: 300px;
      overflow-y: auto;
    }
    .chat-box p {
      margin: 5px 0;
    }
    form {
      display: flex;
      gap: 10px;
    }
    input[type="text"] {
      flex: 1;
      padding: 8px;
      border: none;
      border-radius: 5px;
    }
    button {
      padding: 8px 16px;
      border: none;
      background: #0f0;
      color: #000;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <h1>💬 Öffentlicher Chat</h1>

  <div class="chat-box" id="chat-messages">
    Lade Chatverlauf...
  </div>

  <form id="chat-form">
    <input type="text" id="message" placeholder="Nachricht eingeben" autocomplete="off" required />
    <button type="submit">Senden</button>
  </form>

  <script>
    const chatBox = document.getElementById('chat-messages');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('message');

    async function loadMessages() {
      const res = await fetch('?load=1');
      const messages = await res.json();
      chatBox.innerHTML = messages.map(m => `<p>${m}</p>`).join('');
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const msg = input.value.trim();
      if (msg === '') return;

      const formData = new FormData();
      formData.append('message', msg);

      await fetch('', {
        method: 'POST',
        body: formData
      });

      input.value = '';
      loadMessages();
    });

    // Starte Chat-Refresh
    loadMessages();
    setInterval(loadMessages, 3000);
  </script>

</body>
</html>
