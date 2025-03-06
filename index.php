<?php
// Xtream API Credentials
$xtream_url = "http://YOUR_XTREAM_SERVER"; // Replace with your Xtream server URL
$username = "YOUR_USERNAME"; // Replace with your username
$password = "YOUR_PASSWORD"; // Replace with your password

// Fetch channel list
$api_url = "$xtream_url/player_api.php?username=$username&password=$password&action=get_live_categories";
$categories = json_decode(file_get_contents($api_url), true);

$channels = [];
foreach ($categories as $category) {
    $cat_id = $category['category_id'];
    $channel_api = "$xtream_url/player_api.php?username=$username&password=$password&action=get_live_streams&category_id=$cat_id";
    $channels[$category['category_name']] = json_decode(file_get_contents($channel_api), true);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live TV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Live TV Channels</h2>

    <input type="text" id="search" class="form-control mb-3" placeholder="Search channels...">

    <div id="categories" class="mb-3">
        <button class="btn btn-secondary" onclick="filterCategory('all')">All</button>
        <?php foreach ($channels as $category => $ch_list) { ?>
            <button class="btn btn-secondary" onclick="filterCategory('<?php echo $category; ?>')"><?php echo $category; ?></button>
        <?php } ?>
    </div>

    <div id="channel-list">
        <?php foreach ($channels as $category => $ch_list) { ?>
            <h4 class="category-header"><?php echo $category; ?></h4>
            <div class="row">
                <?php foreach ($ch_list as $channel) { ?>
                    <div class="col-md-3 channel-card" data-category="<?php echo $category; ?>" data-name="<?php echo strtolower($channel['name']); ?>">
                        <a href="play.php?id=<?php echo $channel['stream_id']; ?>" class="card">
                            <img src="<?php echo $channel['stream_icon'] ?: 'default.jpg'; ?>" class="card-img-top" alt="Channel">
                            <div class="card-body">
                                <h6 class="card-title"><?php echo $channel['name']; ?></h6>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<script>
function filterCategory(category) {
    document.querySelectorAll('.category-header, .channel-card').forEach(el => {
        el.style.display = category === 'all' || el.getAttribute('data-category') === category ? 'block' : 'none';
    });
}

document.getElementById('search').addEventListener('input', function() {
    let searchVal = this.value.toLowerCase();
    document.querySelectorAll('.channel-card').forEach(card => {
        card.style.display = card.getAttribute('data-name').includes(searchVal) ? 'block' : 'none';
    });
});
</script>

</body>
</html>
