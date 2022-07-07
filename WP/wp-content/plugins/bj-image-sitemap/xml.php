<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php echo '<?xml-stylesheet type="text/xsl" href="' . plugins_url('bj-image-sitemap') . '/xml-sitemap-image.xsl"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php foreach ($urls as $url) : ?>
    <url>
        <loc><?php echo $url['loc']; ?></loc>
        <lastmod><?php echo $url['lastmod']; ?></lastmod>
        <priority><?php echo $url['priority']; ?></priority>
<?php foreach ($url['images'] as $image) : ?>
        <image:image>
            <image:loc><?php echo $image['loc']; ?></image:loc>
            <image:caption><?php echo $image['caption']; ?></image:caption>
            <image:title><?php echo $image['title']; ?></image:title>
        </image:image>
<?php endforeach; ?>
    </url>
<?php endforeach; ?>
</urlset>