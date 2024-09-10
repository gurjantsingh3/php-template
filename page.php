<?php
include 'includes/config.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : 'default';
$data = fetchApiData('/video_links');
$pageData = array_filter($data['_items'], function ($item) use ($slug) {
    return strtolower($item['name']) === strtolower($slug);
});
$pageData = !empty($pageData) ? reset($pageData) : null;

if (!$pageData) {
    include 'pages/error.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageData['heading']); ?></title>
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo htmlspecialchars($pageData['heading']); ?></h1>
        </header>
        <iframe src="<?php echo htmlspecialchars($pageData['video_link']); ?>" width="560" height="400" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        <div class="footer">
            <img src="<?php echo htmlspecialchars($pageData['image_link']); ?>" alt="Logo">
        </div>
    </div>
</body>
</html>
