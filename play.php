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

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const playerContainer = document.getElementById('player');

      // Function to get query parameters
      function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
      }

      // Get the channel ID from the URL
      const channelId = getQueryParam('id');

      if (!channelId) {
        // Show error if no channel ID is provided
        playerContainer.innerHTML = '<p class="error-message">No channel selected!</p>';
        return;
      }

      // Fetch channels.json to get the channel list
      fetch('channels.json')
        .then((response) => response.json())
        .then((channels) => {
          const channel = channels.find((ch) => ch.id === channelId);

          if (!channel) {
            // Show error if the channel is not found
            playerContainer.innerHTML = '<p class="error-message">Channel not found!</p>';
            return;
          }

          // Initialize the Clappr player with the channel URL
          new Clappr.Player({
            source: channel.url,
            parentId: "#player",
            autoPlay: true,
            mute: false,
            width: "100%",
            height: 500,
            playback: {
              hlsjsConfig: {
                debug: false, // Disable HLS.js debugging
              },
            },
          });
        })
        .catch((err) => {
          // Show error if channels.json cannot be loaded
          playerContainer.innerHTML = '<p class="error-message">Error loading channels!</p>';
          console.error(err);
        });
    });
  </script>
</body>
</html>
