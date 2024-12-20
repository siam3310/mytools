<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>You Are Watching</title>
  <script src="https://cdn.jsdelivr.net/npm/clappr@latest/dist/clappr.min.js"></script>
  <style>
    #player {
      width: 100%;
      height: 100%;
      max-width: 640px;
      max-height: 360px;
      margin: 20px auto;
      background-color: black;
    }

    /* Hide loading spinner */
    .clappr-loading-spinner {
      display: none !important;
    }

    /* Error message style */
    .error-message {
      color: white;
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <h1 style="text-align: center;">You Are Watching</h1>
  <div id="player"></div>

  <?php
    // Fetch channel ID from URL
    $channelId = isset($_GET['id']) ? $_GET['id'] : null;

    // Load channels.json
    $channelsFile = 'channels.json';
    $channels = [];

    if (file_exists($channelsFile)) {
        $channels = json_decode(file_get_contents($channelsFile), true);
    }

    // Find the requested channel
    $selectedChannel = null;
    if ($channelId) {
        foreach ($channels as $channel) {
            if ($channel['id'] == $channelId) {
                $selectedChannel = $channel;
                break;
            }
        }
    }
  ?>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const playerContainer = document.getElementById('player');

      <?php if (!$channelId): ?>
        // No channel selected
        playerContainer.innerHTML = '<p class="error-message">No channel selected!</p>';
      <?php elseif (!$selectedChannel): ?>
        // Channel not found
        playerContainer.innerHTML = '<p class="error-message">Channel not found!</p>';
      <?php else: ?>
        // Initialize Clappr player with the selected channel URL
        new Clappr.Player({
          source: "<?php echo $selectedChannel['url']; ?>",
          parentId: "#player",
          autoPlay: true,
          mute: false,
          width: "100%",
          height: 500,
          playback: {
            hlsjsConfig: {
              debug: false // Disable HLS.js debugging
            }
          }
        });
      <?php endif; ?>
    });
  </script>
</body>
</html>
